<?php
namespace App\Http\Controllers;

use App\Models\Sede;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SedeController extends Controller
{
    public function index()
    {
        $sedes = Sede::all();
        return Inertia::render('app/sedes/SedesIndex', [
            'sedes' => $sedes,
        ]);
    }

    public function create()
    {
        return Inertia::render('app/sedes/SedesCreate');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Sede::create($validated);
        return redirect()->route('sedes.index')->with('success', 'Sede creada correctamente.');
    }

}
