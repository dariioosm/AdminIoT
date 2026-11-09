@extends('layouts.app')
@section('titulo','Crea tu sensor')
@section('contenido')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h1 class="mb-0">Guarda sensores para {{$dispositivo->nombre}}</h1>
    <a href="{{ route('dispositivos.show',$dispositivo->id) }}" class="btn btn-secondary">Volver al dispositivo</a>
</div>
<form action="{{ route('sensores.store',['id'=>$dispositivo->id]) }}" method="post">
    @csrf
    @include('sensores._form')
    <button type="submit" class="btn btn-primary">Guardar</button>
</form>
@endsection
