<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alerta extends Model
{
    protected $fillable = [
        'sensor_id',
        'valor',
        'medida_at',
    ];

    protected $casts = [
        'valor'=>'decimal:4',
    ];

    //TODO Hacer relaciones
    public function sensor():BelongsTo{
        return $this->belongsTo(Sensor::class,'sensor_id');
    }

    public function lectura():BelongsTo{
        return $this->belongsTo(Lectura::class,'lectura_id');
    }
}
