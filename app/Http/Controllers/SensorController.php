<?php

namespace App\Http\Controllers;

use App\Models\Dispositivo;
use App\Models\Sensor;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
class SensorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(String $id)
    {
        $dispositivo = Dispositivo::with('sensores')->findOrFail($id);
        return view('sensores.create',compact('dispositivo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,$id)
    {
        $dispositivo=Dispositivo::findOrFail($id);
        $datos = $request->validate([
            'nombre'=>'required|string|max:255',
            'tipo'=>'required|string|max:255',
            'tipo_dato'=>'required|in:numerico,logico',
            'umbral_minimo'=>'exclude_if:tipo_dato,logico|numeric|nullable',
            'umbral_maximo'=>['exclude_if:tipo_dato,logico',
                'numeric','nullable'],

        ]);
        if ($datos['tipo_dato'] === 'logico') {
            $datos['umbral_minimo'] = null;
            $datos['umbral_maximo'] = null;
        }
        $datos['dispositivo_id']=$dispositivo->id;
        $datos['activo']=true;
        $sensor = Sensor::create($datos);
        return redirect()->route('sensores.show',['id'=>$sensor->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sensor = Sensor::with('dispositivo')->findOrFail($id);

        $lecturas = $sensor->lecturas()
            ->orderByDesc('medida_at')->orderBy('id')->paginate(15);

            return view('sensores.show',compact('sensor','lecturas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $sensor=Sensor::findOrFail($id);
        return view('sensores.edit',compact('sensor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $sensor = Sensor::findOrFail($id);
        $datos = $request->validate([
            'nombre'=>'required|string|max:255',
            'tipo'=>'required|string|max:255',
            'umbral_minimo'=>'nullable|numeric',
            'umbral_maximo'=>'nullable|numeric',
            'tipo_dato'=>'required|in:numerico,logico',
        ]);
        if ($datos['tipo_dato'] === 'logico') {
                $datos['umbral_minimo'] = null;
                $datos['umbral_maximo'] = null;
            }
        $sensor->update($datos);
        return redirect()
        ->route('dispositivos.show', ['id' => $sensor->dispositivo->id])
        ->with('success', 'Sensor actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sensor=Sensor::findOrFail($id);
        $dispositivoId=Dispositivo::findOrFail($sensor->dispositivo->id);
        try {
    $sensor->delete();
} catch (QueryException $excepcion) {
    if ((int) ($excepcion->errorInfo[1] ?? 0) !== 1451) {
        throw $excepcion;
    }

    return back()->with(
        'error',
        'Este sensor tiene lecturas o alertas asociadas. Puedes desactivarlo para conservar su historial.'
    );
}

    }


    public function simularPuerta(Request $request, string $id){
        $sensor = Sensor::findOrFail($id);

        abort_unless(
            $sensor->activo
                && $sensor->tipo === 'puerta'
                && $sensor->tipo_dato === 'logico',
            404
        );

        $datos = $request->validate([
            'accion' => 'required|in:abrir,cerrar',
        ]);

        $sensor->valor_simulado = $datos['accion'] === 'abrir' ? 1 : null;
        $sensor->save();

        return back()->with('success', 'El cambio se aplicará en el próximo envío.');
    }
}
