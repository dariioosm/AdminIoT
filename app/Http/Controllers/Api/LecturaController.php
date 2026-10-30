<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lectura;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LecturaController extends Controller
{
    public function store(Request $request):JsonResponse{
        $datos = $request->validate([
            'sensor_id'=>'required|integer|exists:sensores,id',
            'valor'=>'required|numeric',
            'medida_at'=>'required|date',
        ]);
        $lectura = Lectura::create($datos);
        return response()->json([
            'mensaje'=>'Lecturas recibidas',
            'datos'=>$lectura
            ],201);
    }
}
