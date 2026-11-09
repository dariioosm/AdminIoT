@extends('layouts.app')

@section('titulo', 'Histórico de alertas')

@section('contenido')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h1 class="mb-0">Histórico de alertas</h1>

    @if ($dispositivoId !== null)
        <a href="{{ route('alertas.index') }}" class="btn btn-outline-secondary">
            Ver todos los dispositivos
        </a>
    @endif
</div>

@forelse ($alertasPorDispositivo as $id => $alertas)
    <section class="card mb-4" aria-labelledby="alertas-dispositivo-{{ $id }}">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h2 id="alertas-dispositivo-{{ $id }}" class="h5 mb-0">
                {{ $alertas->first()->sensor->dispositivo->nombre }}
            </h2>

            <a href="{{ route('dispositivos.show', ['id' => $id]) }}#historico"
               class="btn btn-sm btn-outline-primary">
                Ver histórico de lecturas
            </a>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">Sensor</th>
                        <th scope="col">Alerta</th>
                        <th scope="col">Inicio</th>
                        <th scope="col">Resolución</th>
                        <th scope="col">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alertas as $alerta)
                        <tr>
                            <td>{{ $alerta->sensor->nombre }}</td>
                            <td>{{ $alerta->mensaje }}</td>
                            <td>
                                {{ \Illuminate\Support\Carbon::parse($alerta->iniciada_at)->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                {{ $alerta->resuelta_at === null
                                    ? '—'
                                    : \Illuminate\Support\Carbon::parse($alerta->resuelta_at)->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                @if ($alerta->resuelta_at === null)
                                    <span class="badge bg-danger">Abierta</span>
                                @else
                                    <span class="badge bg-success">Resuelta</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@empty
    <p class="alert alert-info">
        {{ $dispositivoId === null
            ? 'Todavía no hay alertas registradas.'
            : 'Este dispositivo no tiene alertas registradas.' }}
    </p>
@endforelse
@endsection
