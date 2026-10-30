<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sensor extends Model
{
    protected $table='sensores';
    protected $fillable = [
        'dispositivo_id',
        'nombre',
        'tipo',
        'medida',
        'umbral_minimo',
        'umbral_maximo',
        'activo',
    ];

    protected $casts = [
        'activo'=>'boolean',
        'umbral_minimo'=>'decimal:4',
        'umbral_maximo'=>'decimal:4',
    ];


    public function dispositivo():BelongsTo{
        return $this->belongsTo(Dispositivo::class,'dispositivo_id');
    }

    public function lecturas():HasMany{
        return $this->hasMany(Lectura::class,'sensor_id');
    }

    public function alertas():HasMany{
        return $this->hasMany(Alerta::class,'sensor_id');
    }


}
