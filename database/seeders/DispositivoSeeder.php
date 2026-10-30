<?php

namespace Database\Seeders;

use App\Models\Dispositivo;
use Illuminate\Database\Seeder;

class DispositivoSeeder extends Seeder
{
    public function run(): void
    {
        $dispositivo = Dispositivo::firstOrCreate(
            ['nombre' => 'Cámara frigorífica 01'],
            [
                'ubicacion' => 'Almacén',
                'ultima_comunicacion_at' => null,
            ]
        );

        $dispositivo->sensores()->createMany([
            [
                'nombre' => 'Temperatura interior',
                'tipo' => 'temperatura',
                'medida' => '°C',
                'umbral_minimo' => 2,
                'umbral_maximo' => 8,
                'activo' => true,
            ],
            [
                'nombre' => 'Estado de la puerta',
                'tipo' => 'puerta',
                'medida' => null,
                'umbral_minimo' => null,
                'umbral_maximo' => null,
                'activo' => true,
            ],
        ]);
    }
}
