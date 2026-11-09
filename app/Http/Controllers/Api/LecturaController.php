<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\Sensor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LecturaController extends Controller
{
    public function store(Request $request): JsonResponse
{
    $datos = $request->validate([
        'sensor_id' => 'required|integer|exists:sensores,id',
        'valor' => 'required|numeric',
        'medida_at' => 'required|date',
    ]);

    $lectura = DB::transaction(function () use ($datos) {
        $sensor = Sensor::query()
            ->lockForUpdate()
            ->findOrFail($datos['sensor_id']);

        if (
            $sensor->tipo_dato === 'logico'
            && !in_array((float) $datos['valor'], [0.0, 1.0], true)
        ) {
            throw ValidationException::withMessages([
                'valor' => 'Un sensor lógico solo admite 0 o 1.',
            ]);
        }

        $lectura = $sensor->lecturas()->create([
            'valor' => $datos['valor'],
            'medida_at' => $datos['medida_at'],
        ]);

        $sensor->dispositivo()->update([
            'ultima_comunicacion_at' => now(),
        ]);

        if (
            $sensor->activo
            && $sensor->tipo === 'puerta'
            && $sensor->tipo_dato === 'logico'
            && $sensor->valor_esperado !== null
        ) {
            $alerta = Alerta::query()
                ->where('sensor_id', $sensor->id)
                ->where('tipo', 'estado_puerta')
                ->whereNull('resuelta_at')
                ->first();

            $fueraDeEstado = (int) $lectura->valor !== (int) $sensor->valor_esperado;

            if ($fueraDeEstado && $alerta === null) {
                $alerta = new Alerta();
                $alerta->sensor_id = $sensor->id;
                $alerta->tipo = 'estado_puerta';
                $alerta->mensaje = (int) $lectura->valor === 1
                    ? 'Puerta abierta fuera del estado esperado'
                    : 'Puerta cerrada fuera del estado esperado';
                $alerta->iniciada_at = $lectura->medida_at;
                $alerta->save();
            } elseif (!$fueraDeEstado && $alerta !== null) {
                $alerta->resuelta_at = $lectura->medida_at;
                $alerta->save();
            }
        }

        return $lectura;
    });

    return response()->json([
        'mensaje' => 'Lectura recibida',
        'datos' => $lectura,
    ], 201);
}

}
