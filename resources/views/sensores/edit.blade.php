@extends('layouts.app')
@section('titulo','Editar sensor')
@section('contenido')
<h1 class="mb-0">Editar {{$sensor->nombre}}</h1>
<form action="{{ route('sensores.update',['id'=>$sensor->id]) }}" method="post">
    @csrf
    @method('PUT')
    @include('sensores._form')
    <button type="submit" class="btn btn-primary">Guardar cambios</button>
</form>

@endsection
