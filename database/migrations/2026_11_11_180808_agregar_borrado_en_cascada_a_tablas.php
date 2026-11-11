<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->actualizarClavesForaneas('cascade');
    }

    public function down(): void
    {
        $this->actualizarClavesForaneas('restrict');
    }

    private function actualizarClavesForaneas(string $accion): void
    {
        $relaciones = [
            ['sensores', 'dispositivo_id', 'dispositivos'],
            ['lecturas', 'sensor_id', 'sensores'],
            ['alertas', 'sensor_id', 'sensores'],
            ['alertas', 'lectura_id', 'lecturas'],
        ];

        foreach ($relaciones as [$tabla, $columna, $tablaPadre]) {
            Schema::table($tabla, function (Blueprint $table) use ($columna, $tablaPadre, $accion) {
                $table->dropForeign([$columna]);
                $table->foreign($columna)
                    ->references('id')
                    ->on($tablaPadre)
                    ->onDelete($accion);
            });
        }
    }
};
