@extends('layouts.app')
@section('titulo','Crear dispositivo')
@section('contenido')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h1 class="mb-0">Crear nuevo dispositivo</h1>
    <a href="{{ route('dispositivos.index') }}" class="btn btn-secondary">Volver al listado de dispositivos</a>
</div>
<form action="{{ route('dispositivos.store') }}" method="POST">
    @csrf

    @include('dispositivos._form')

    <button type="submit" class="btn btn-success">Crear</button>
</form>
@endsection
