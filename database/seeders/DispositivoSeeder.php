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

        $dispositivo->sensores()->updateOrCreate(
            ['nombre' => 'Temperatura interior'],
            [
                'tipo' => 'temperatura',
                'tipo_dato' => 'numerico',
                'medida' => '°C',
                'umbral_minimo' => 2,
                'umbral_maximo' => 8,
                'valor_esperado' => null,
                'activo' => true,
            ]
        );

        $dispositivo->sensores()->updateOrCreate(
            ['nombre' => 'Estado de la puerta'],
            [
                'tipo' => 'puerta',
                'tipo_dato' => 'logico',
                'medida' => null,
                'umbral_minimo' => null,
                'umbral_maximo' => null,
                'valor_esperado' => 0,
                'valor_simulado' => 0,
                'activo' => true,
            ]
        );
        $dispositivo->sensores()->updateOrCreate(
    ['nombre' => 'Humedad interior'],
    [
        'tipo' => 'humedad',
        'tipo_dato' => 'numerico',
        'medida' => '%HR',
        'umbral_minimo' => 40,
        'umbral_maximo' => 75,
        'valor_esperado' => null,
        'activo' => true,
    ]
);

$dispositivo->sensores()->updateOrCreate(
    ['nombre' => 'Consumo del compresor'],
    [
        'tipo' => 'consumo',
        'tipo_dato' => 'numerico',
        'medida' => 'W',
        'umbral_minimo' => null,
        'umbral_maximo' => 900,
        'valor_esperado' => null,
        'activo' => true,
    ]
);

$dispositivo->sensores()->updateOrCreate(
    ['nombre' => 'Agua en la bandeja de drenaje'],
    [
        'tipo' => 'fuga_agua',
        'tipo_dato' => 'logico',
        'medida' => null,
        'umbral_minimo' => null,
        'umbral_maximo' => null,
        'valor_esperado' => 0,
        'valor_simulado' => 0,
        'activo' => true,
    ]
);
    }
}
