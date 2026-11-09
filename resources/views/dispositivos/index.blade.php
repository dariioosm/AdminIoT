@extends('layouts.app')

@section('titulo', 'Dispositivos')

@section('contenido')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h1 class="mb-0">Dispositivos</h1>

    <a href="{{ route('dispositivos.create') }}" class="btn btn-primary">
        Nuevo dispositivo
    </a>
</div>

<div class="row g-4 mb-4">
    @forelse ($dispositivos as $dispositivo)
        <div class="col-12 col-md-6 col-lg-4">
            <article class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title h5">
                        {{ $dispositivo->nombre }}
                    </h2>

                    <p class="card-text">
                        <strong>Ubicación:</strong>
                        {{ $dispositivo->ubicacion }}
                    </p>

                    <p class="card-text">
                        <strong>Sensores:</strong>
                        <span class="badge bg-primary">
                            {{ $dispositivo->sensores_count }}
                        </span>
                    </p>

                    <p class="card-text">
                        <strong>Última comunicación:</strong><br>
                        {{ $dispositivo->ultima_comunicacion_at
                            ? \Illuminate\Support\Carbon::parse($dispositivo->ultima_comunicacion_at)->format('d/m/Y H:i')
                            : 'No hay datos disponibles' }}
                    </p>
                </div>

                <div class="card-footer d-flex flex-wrap gap-2">
                    <a
                        href="{{ route('dispositivos.show', $dispositivo->id) }}"
                        class="btn btn-sm btn-outline-primary"
                    >
                        Ver
                    </a>

                    <a
                        href="{{ route('dispositivos.edit', $dispositivo->id) }}"
                        class="btn btn-sm btn-outline-secondary"
                    >
                        Editar
                    </a>

                    <form
                        action="{{ route('dispositivos.delete', $dispositivo->id) }}"
                        method="POST"
                        onsubmit="return confirm('¿Deseas eliminar este dispositivo?');"
                    >
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            Eliminar
                        </button>
                    </form>
                </div>
            </article>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info" role="status">
                Todavía no hay dispositivos registrados.
            </div>
        </div>
    @endforelse
</div>

{{ $dispositivos->links('pagination::bootstrap-5') }}
@endsection
