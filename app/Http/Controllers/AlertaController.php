<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alerta;
class AlertaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $filtros = $request->validate([
        'dispositivo_id' => 'nullable|integer|exists:dispositivos,id',
    ]);

    $dispositivoId = $filtros['dispositivo_id'] ?? null;

    $consulta = Alerta::with('sensor.dispositivo');

    if ($dispositivoId !== null) {
        $consulta->whereHas('sensor', function ($sensores) use ($dispositivoId) {
            $sensores->where('dispositivo_id', $dispositivoId);
        });
    }

    $alertasPorDispositivo = $consulta
        ->orderByDesc('iniciada_at')
        ->orderByDesc('id')
        ->get()
        ->groupBy('sensor.dispositivo_id');

    return view('alertas.index', compact(
        'alertasPorDispositivo',
        'dispositivoId'
    ));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
