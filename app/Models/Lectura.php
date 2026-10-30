<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lectura extends Model
{
    protected $fillable = [
        'sensor_id',
        'valor',
        'medida_at',
    ];

    protected $casts = [
        'valor'=>'decimal:4',
        'medida_at'=>'datetime',
    ];

    //TODO hacer relaciones

    public function sensor():BelongsTo{
        return $this->belongsTo(Sensor::class,'sensor_id');
    }

    public function alertas():HasMany{
        return $this->hasMany(Alerta::class,'lectura_id');
    }
}
