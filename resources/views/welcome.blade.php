@extends('layouts.app')

@section('titulo', 'Inicio | Simulador IoT')

@section('contenido')
    <h1>Simulador IoT</h1>

    <p class="lead">
        Gestión de dispositivos, sensores y lecturas.
    </p>

    <a href="{{ url('/dispositivos') }}" class="btn btn-primary">
        Ver dispositivos
    </a>
@endsection
