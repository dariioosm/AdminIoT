<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\Lectura;
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

            $this->actualizarAlerta($sensor, $lectura);

            return $lectura;
        });

        return response()->json([
            'mensaje' => 'Lectura recibida',
            'datos' => $lectura,
        ], 201);
    }

    private function actualizarAlerta(Sensor $sensor, Lectura $lectura): void
    {
        if (!$sensor->activo) {
            return;
        }

        [$tipo, $mensaje] = $this->detectarIncidencia(
            $sensor,
            (float) $lectura->valor
        );

        $alertaAbierta = $sensor->alertas()
            ->whereIn('tipo', [
                'estado_puerta',
                'estado_inesperado',
                'umbral_minimo',
                'umbral_maximo',
            ])
            ->whereNull('resuelta_at')
            ->first();

        if ($alertaAbierta !== null) {
            if ($alertaAbierta->tipo === $tipo) {
                return;
            }

            $alertaAbierta->resuelta_at = $lectura->medida_at;
            $alertaAbierta->save();
        }

        if ($tipo === null) {
            return;
        }

        $alerta = new Alerta();
        $alerta->sensor_id = $sensor->id;
        $alerta->lectura_id = $lectura->id;
        $alerta->tipo = $tipo;
        $alerta->mensaje = $mensaje;
        $alerta->iniciada_at = $lectura->medida_at;
        $alerta->save();
    }

    private function detectarIncidencia(Sensor $sensor, float $valor): array
    {
        if ($sensor->tipo_dato === 'logico') {
            if (
                $sensor->valor_esperado === null
                || $valor === (float) $sensor->valor_esperado
            ) {
                return [null, null];
            }

            if ($sensor->tipo === 'puerta') {
                return [
                    'estado_puerta',
                    $valor === 1.0
                        ? 'Puerta abierta fuera del estado esperado'
                        : 'Puerta cerrada fuera del estado esperado',
                ];
            }

            return [
                'estado_inesperado',
                "{$sensor->nombre}: valor recibido {$valor}, esperado {$sensor->valor_esperado}.",
            ];
        }

        if ($sensor->umbral_minimo !== null && $valor < (float) $sensor->umbral_minimo) {
            return [
                'umbral_minimo',
                "{$sensor->nombre}: {$valor} por debajo del mínimo de {$sensor->umbral_minimo}.",
            ];
        }

        if ($sensor->umbral_maximo !== null && $valor > (float) $sensor->umbral_maximo) {
            return [
                'umbral_maximo',
                "{$sensor->nombre}: {$valor} por encima del máximo de {$sensor->umbral_maximo}.",
            ];
        }

        return [null, null];
    }
}
