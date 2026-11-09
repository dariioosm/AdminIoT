<?php

namespace App\Http\Controllers;

use App\Models\Dispositivo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\Mime\Part\Multipart\DigestPart;

class DispositivoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dispositivos = Dispositivo::withCount('sensores')
        ->orderBy('nombre')->paginate(10);

        return view('dispositivos.index',compact('dispositivos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dispositivos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre'=>'required|string|max:255',
            'ubicacion'=>'required|string|max:255'
        ]);

        $dispositivo = Dispositivo::create($datos);
        return redirect()->route('dispositivos.show',$dispositivo->id);

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request,string $id)
    {
       // $dispositivo = Dispositivo::findOrFail($id);
        $periodos=[
            '1h'=>1,
            '24h'=>24,
            '7d'=>168,
        ];

        $periodo = $request->query('periodo','24h');

        if(!is_string($periodo)|| !isset($periodos[$periodo])){
            $periodo='24h';
        }

        $hasta=now();
        $desde=$hasta->copy()->subHours($periodos[$periodo]);

        $dispositivo = Dispositivo::with([
            'sensores.lecturas'=>fn($consulta)=>$consulta->whereBetween('medida_at',[$desde,$hasta])
            ->orderBy('medida_at')->orderBy('id'),
        ])->findOrFail($id);

        $series = $dispositivo->sensores->map(fn($sensor)=>[
            'id'=>$sensor->id,
            'nombre'=>$sensor->nombre,
            'tipo'=>$sensor->tipo,
            'tipoDato'=>$sensor->tipo_dato,
            'unidad'=>$sensor->medida,
            'puntos'=>$sensor->lecturas->map(fn($lectura)=>[
                'x'=>Carbon::parse($lectura->medida_at)->getTimeStamp()*1000,
                'y'=>(float)$lectura->valor,
            ])->values()->all(),
        ])->values()->all();

        $rango=[
            'desde'=>$desde->getTimestamp()*1000,
            'hasta'=>$hasta->getTimestamp()*1000
        ];

        return view('dispositivos.show',compact('dispositivo','periodo','series','rango'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $dispositivo = Dispositivo::findOrFail($id);
       return view('dispositivos.edit',compact('dispositivo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $dispositivo = Dispositivo::findOrFail($id);
        $datos = $request->validate([
            'nombre'=>'required|string|max:255',
            'ubicacion'=>'required|string|max:255'
        ]);

        $dispositivo->update($datos);

        return redirect()->route('dispositivos.show',$dispositivo->id)
            ->with('success','Dispositivo actualizado correctamente');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $dispositivo = Dispositivo::findOrFail($id);
        $dispositivo->delete();
       return redirect()->route('dispositivos.index')
       ->with('success','Dispositivo eliminado correctamente');
    }
}
