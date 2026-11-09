<?php

namespace App\Console\Commands;

use App\Models\Dispositivo;
use App\Models\Sensor;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

#[Signature('iot:simular {dispositivo? : ID del dispositivo}')]
#[Description('Envía lecturas simuladas de los sensores activos')]
class SimularLecturas extends Command
{
    public function handle(): int
    {
        $dispositivoId = $this->argument('dispositivo');

        $consulta = Dispositivo::with([
            'sensores' => fn ($query) => $query->where('activo', true),
        ])->whereHas('sensores', fn ($query) => $query->where('activo', true));

        if ($dispositivoId !== null) {
            $consulta->whereKey($dispositivoId);
        }

        $dispositivos = $consulta->get();

        if ($dispositivos->isEmpty()) {
            $this->warn('No se encontraron dispositivos con sensores activos.');

            return self::SUCCESS;
        }

        $url = rtrim(config('app.url'), '/') . '/api/lecturas';
        $huboErrores = false;

        foreach ($dispositivos as $dispositivo) {
            $fecha = now()->format('Y-m-d H:i:s');

            foreach ($dispositivo->sensores as $sensor) {
                $valor = $this->valorPara($sensor);

                try {
                    $respuesta = Http::acceptJson()->timeout(5)->post($url, [
                        'sensor_id' => $sensor->id,
                        'valor' => $valor,
                        'medida_at' => $fecha,
                    ]);
                } catch (ConnectionException $excepcion) {
                    $this->error("No se pudo conectar con {$url}: {$excepcion->getMessage()}");

                    return self::FAILURE;
                }

                if (!$respuesta->successful()) {
                    $this->error("Sensor {$sensor->id}: HTTP {$respuesta->status()}");
                    $this->line($respuesta->body());
                    $huboErrores = true;

                    continue;
                }

                $this->info("{$dispositivo->nombre} · {$sensor->nombre}: {$valor}");
            }
        }

        return $huboErrores ? self::FAILURE : self::SUCCESS;
    }

    private function valorPara(Sensor $sensor): float|int
    {
        if ($sensor->tipo_dato === 'logico') {
            return (int) ($sensor->valor_simulado ?? $sensor->valor_esperado ?? 0);
        }

        $minimo = $sensor->umbral_minimo;
        $maximo = $sensor->umbral_maximo;

        if ($minimo !== null && $maximo !== null) {
            return ((float) $minimo + (float) $maximo) / 2;
        }

        if ($minimo !== null || $maximo !== null) {
            return (float) ($minimo ?? $maximo);
        }

        return match ($sensor->tipo) {
            'temperatura' => 4.2,
            'humedad' => 65,
            default => 0,
        };
    }
}
