<?php
namespace App\Http\Controllers;

use App\Models\Activo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivoController extends Controller
{
    public function index()
    {
        $activos = Activo::all();
        return Inertia::render('app/activos/ActivosIndex', [
            'activos' => $activos,
        ]);
    }

    public function create()
    {
        return Inertia::render('app/activos/ActivosCreate');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'serie' => 'required|string|max:255|unique:activos,serie',
        ]);

        Activo::create($validated);

        return redirect()->route('activos.index')->with('success', 'Activo creado correctamente.');
    }

}
