@extends('layouts.app')
@section('titulo','Edita el dispositivo')
@section('contenido')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h1 class="mb-0">Edita {{$dispositivo->nombre}}</h1>
    <a href="{{ route('dispositivos.index') }}" class="btn btn-secondary">Volver al listado de dispositivos</a>
</div>
<form action="{{ route('dispositivos.update', $dispositivo) }}" method="POST">
    @csrf
    @method('PUT')

    @include('dispositivos._form')

    <button type="submit" class="btn btn-primary">Guardar cambios</button>
</form>
@endsection
