<?php
namespace App\Http\Controllers;

use App\Jobs\EnviarCorreoVisitaAutorizada;
use App\Jobs\EnviarCorreoVisitaRechazada;
use App\Models\Activo;
use App\Models\Sede;
use App\Models\Visita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class VisitaController extends Controller
{
    #API#
    public function pendientesJson()
    {
        $visitas = Visita::pending()->with(['sede', 'user'])->get();
        return response()->json(['visitas' => $visitas]);
    }
    #WEB#

    public function index()
    {
        $visitas = Visita::with(['user', 'sede'])->get()->map(function ($v) {
            return [
                'id'         => $v->id,
                'time_start' => $v->time_start,
                'time_end'   => $v->time_end,
                'dates'      => $v->dates ?? [],
                'sede'       => $v->sede,
                'user'       => $v->user,
                'status'     => $v->status,
                'qr_token'   => $v->qr_token,
            ];
        });

        return Inertia::render('app/visitas/VisitasIndex', [
            'visitas' => $visitas,
        ]);
    }

    public function create()
    {
        $sedes   = Sede::select('id', 'name')->get();
        $activos = Activo::select('name', 'serie')->get();

        return Inertia::render('app/visitas/VisitasCreate', [
            'sedes'   => $sedes,
            'activos' => $activos,
        ]);
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'time_start'    => 'required|date_format:H:i',
            'time_end'      => 'required|date_format:H:i|after:time_start',
            'dates'         => 'nullable|array',
            'dates.*'       => 'date', // Asegura que cada elemento sea una fecha válida
            'sede_id'       => 'required|exists:sedes,id',
            'activo_ids'    => 'nullable|array',
            'activo_ids.*'  => 'exists:activos,id',
            'authorized_by' => 'nullable|exists:users,id',
        ]);

        $visita = Visita::create([
            'time_start'    => $data['time_start'],
            'time_end'      => $data['time_end'],
            'dates'         => $data['dates'] ?? [],
            'sede_id'       => $data['sede_id'],
            'user_id'       => Auth::id(),
            'authorized_by' => null,
            'status'        => 'pending',
            'type'          => 'interna',
        ]);

        if (! empty($data['activo_ids'])) {
            $visita->activos()->sync($data['activo_ids']);
        }

        return redirect()->route('visitas.index')->with('success', 'Visita creada exitosamente.');
    }

    public function externasCreate()
    {
        $sedes = Sede::select('id', 'name')->get();
        return Inertia::render('app/visitas/VisitasExternasCreate', [
            'sedes' => $sedes,
        ]);
    }
    public function externasStore(Request $request)
    {
        // Validar los datos recibidos
        $validated = $request->validate([
            'time_start'              => 'required|string',
            'time_end'                => 'required|string',
            'dates'                   => 'required|array|min:1',
            'dates.*'                 => 'date', // formato ISO8601, ajusta si usas otro
            'sede_id'                 => 'required|exists:sedes,id',
            'visitor_name'            => 'required|string|max:255',
            'visitor_email'           => 'nullable|email|max:255',
            'visitor_company'         => 'nullable|string|max:255',
            'visitor_reason'          => 'nullable|string|max:1000',
            'participants'            => 'nullable|array',
            'participants.*.name'     => 'required_with:participants|string|max:255',
            'participants.*.document' => 'required_with:participants|string|max:255',
        ]);

        // Crear la visita externa
        $visita = Visita::create([
            'type'            => 'externa',
            'time_start'      => $validated['time_start'],
            'time_end'        => $validated['time_end'],
            'dates'           => $validated['dates'],
            'sede_id'         => $validated['sede_id'],
            'visitor_name'    => $validated['visitor_name'],
            'visitor_email'   => $validated['visitor_email'] ?? null,
            'visitor_company' => $validated['visitor_company'] ?? null,
            'visitor_reason'  => $validated['visitor_reason'] ?? null,
            'status'          => 'pending',
            'user_id'         => Auth::id(),
        ]);

        // Guardar participantes si vienen
        if (! empty($validated['participants'])) {
            foreach ($validated['participants'] as $participant) {
                $visita->participants()->create([
                    'name'     => $participant['name'],
                    'document' => $participant['document'],
                ]);
            }
        }

        // Responder o redireccionar
        return redirect()->route('visitas.index')->with('success', 'Visita externa registrada correctamente');
    }

    public function autorizarIndex()
    {
        $visitas = Visita::pending()->with(['sede', 'user'])->get();

        return Inertia::render('app/visitas/autorizar/AutorizarVisitaIndex', [
            'visitas' => $visitas,
        ]);
    }

    public function autorizar(Request $request)
    {
        $request->validate([
            'visita_id' => 'required|exists:visitas,id',
            'action'    => 'required|in:authorize,reject',
        ]);

        $visita = Visita::pending()->findOrFail($request->visita_id);

        if ($request->action === 'authorize') {
            $visita->status        = Visita::STATUS_AUTHORIZED;
            $visita->authorized_by = $request->user()->id;
            $visita->save();
            EnviarCorreoVisitaAutorizada::dispatch($visita);
            return redirect()->back()->with('success', 'Visita autorizada correctamente.');
        }

        if ($request->action === 'reject') {
            $visita->status = Visita::STATUS_REJECTED;
            $visita->save();
            EnviarCorreoVisitaRechazada::dispatch($visita);

            return redirect()->back()->with('success', 'Visita rechazada correctamente.');
        }
    }

    public function qrScanIndex(Request $request)
    {
        return Inertia::render('app/visitas/escanear/EscanearVisita', [
            'token' => $request->query('token'),
        ]);
    }

    public function qrScan(Request $request)
    {

        $request->validate([
            'token' => 'required|uuid',
        ]);

        $visita = Visita::where('qr_token', $request->token)->first();

        if (! $visita) {
            return redirect()->back()->with('error', 'QR no válido.');
        }

        if ($visita->status !== Visita::STATUS_AUTHORIZED) {
            return redirect()->back()->with('error', 'La visita no está autorizada para ingreso.');
        }

        if ($visita->isQrUsed()) {
            return redirect()->back()->with('error', 'El QR ya fue usado.');
        }

        $visita->status = Visita::STATUS_APPROVED;
        $visita->markQrAsUsed();

        return redirect()->back()->with('success', 'Ingreso validado correctamente.');
    }
}
