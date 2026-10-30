<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dispositivo extends Model
{
    protected $fillable = [
        'nombre',
        'ubicacion',
        'ultima_comunicacion_at',
    ];

    public function sensores():HasMany{
        return $this->hasMany(Sensor::class,'dispositivo_id');
    }
}
