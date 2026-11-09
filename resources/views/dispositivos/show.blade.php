@extends('layouts.app')
@section('titulo','Detalle del dispositivo')
@section('contenido')
<div class="d-flex flex-wrap justify-content-between align-items-center">
    <h1 class="mb-0"> Sensores en {{$dispositivo->nombre}}</h1>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('dispositivos.index')}}" class="btn btn-primary">Todos los dispositivos</a>
            <a href="{{ route('sensores.create',['id'=>$dispositivo->id]) }}" class="btn btn-outline-secondary">Añadir sensores</a>
            <a href="{{ route('alertas.index', ['dispositivo_id' => $dispositivo->id]) }}" class="btn btn-outline-danger">
    Ver alertas
</a>
        </div>
</div>
<div class="row g-3 mt-4">
    @forelse($dispositivo->sensores as $sensor)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="card-title">
                        {{ $sensor->nombre }}
                    </h2>
                    <span class="badge {{ $sensor->activo?'bg-success':'bg-secondary' }}">{{ $sensor->activo?'Activo':'Inactivo'}}</span>
                    <p class="mt-3 mb-1">
                        <strong>Tipo: </strong>{{$sensor->tipo}}
                    </p>
                    @if ($sensor->tipo_dato === 'numerico')
                        <p class="mb-1">
                            <strong>Mínimo:</strong>
                            {{ $sensor->umbral_minimo === null
                                ? 'Sin definir'
                                : number_format((float) $sensor->umbral_minimo, 2, ',', '.') }}
                        </p>
                        <p class="mb-0">
                            <strong>Máximo:</strong>
                            {{ $sensor->umbral_maximo === null
                                ? 'Sin definir'
                                : number_format((float) $sensor->umbral_maximo, 2, ',', '.') }}
                        </p>
                    @elseif ($sensor->tipo_dato === 'logico')
                        <p class="mb-0">
                            <strong>Estado esperado:</strong>
                            <span class="badge bg-secondary">
                                @if ($sensor->valor_esperado === null)
                                    Sin configurar
                                @elseif ($sensor->tipo === 'puerta')
                                    {{ $sensor->valor_simulado ? 'Abierta' : 'Cerrada' }}
                                @else
                                    {{ $sensor->valor_simulado? 'Sí (1)' : 'No (0)' }}
                                @endif
                            </span>
                        </p>
                    @endif
                </div>
                <div class="card-footer d-flex flex-wrap gap-2">
                    <a href="{{ route('sensores.edit', ['id' => $sensor->id]) }}"
                       class="btn btn-outline-success btn-sm">
                        Editar
                    </a>
                    <form action="{{ route('sensores.destroy', ['id' => $sensor->id]) }}"
                          method="POST"
                          onsubmit="return confirm('¿Deseas eliminar este sensor?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            Eliminar
                        </button>
                    </form>
                    @if ($sensor->activo && $sensor->tipo === 'puerta' && $sensor->tipo_dato === 'logico')
                        @php
                            $aperturaSimulada = (int) $sensor->valor_simulado === 1;
                        @endphp
                        <form action="{{ route('sensores.simular-puerta', ['id' => $sensor->id]) }}"
                              method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    name="accion"
                                    value="{{ $aperturaSimulada ? 'cerrar' : 'abrir' }}"
                                    class="btn btn-sm {{ $aperturaSimulada ? 'btn-outline-success' : 'btn-outline-warning' }}">
                                {{ $aperturaSimulada ? 'Cerrar puerta' : 'Simular apertura' }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <p class="alert alert-info">No hay sensores registrados.</p>
        </div>
    @endforelse
</div>
  <section id="historico" class="mt-5" aria-labelledby="titulo-historico">
    <h2 id="titulo-historico" class="h4 dark-text mb-3">Histórico de lecturas</h2>
    <div class="d-flex flex-wrap gap-2 mb-4">
        @foreach (['1h' => 'Última hora', '24h' => '24 horas', '7d' => '7 días'] as $clave => $texto)
            <a
                href="{{ route('dispositivos.show', [
                    'id' => $dispositivo->id,
                    'periodo' => $clave,
                ]) }}#historico"
                class="btn btn-sm {{ $periodo === $clave ? 'btn-primary' : 'btn-outline-primary' }}"
                @if ($periodo === $clave) aria-current="true" @endif
            >
                {{ $texto }}
            </a>
        @endforeach
    </div>
    @forelse ($series as $serie)
        <article class="card mb-3">
            <div class="card-body">
                <h3 class="h5">{{ $serie['nombre'] }}</h3>
                @if (empty($serie['puntos']))
                    <p class="text-muted mb-0">Sin lecturas en este periodo.</p>
                @else
                    <div style="position: relative; height: 240px;">
                        <canvas
                            id="historico-sensor-{{ $serie['id'] }}"
                            role="img"
                            aria-label="Histórico de {{ $serie['nombre'] }}"
                        ></canvas>
                    </div>
                @endif
            </div>
        </article>
    @empty
        <p class="text-muted">Este dispositivo no contiene sensores.</p>
    @endforelse
</section>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.9/dist/chart.umd.min.js"></script>
<script>
    (() => {
        const series = {{ \Illuminate\Support\Js::from($series) }};
        const rango = {{ \Illuminate\Support\Js::from($rango) }};
        const formatoFecha = new Intl.DateTimeFormat('es-ES', {
            day: '2-digit',
            month: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
        });
        for (const serie of series) {
            const canvas = document.getElementById(`historico-sensor-${serie.id}`);
            if (!canvas || serie.puntos.length === 0) {
                continue;
            }
            const esLogico = serie.tipoDato === 'logico';
            const esPuerta = serie.tipo === 'puerta';
            function etiquetaEstado(valor) {
                if (esPuerta) {
                    return Number(valor) === 1 ? 'Abierta' : 'Cerrada';
                }
                return Number(valor) === 1 ? 'Sí (1)' : 'No (0)';
            }
            new Chart(canvas, {
                type: 'line',
                data: {
                    datasets: [{
                        label: serie.nombre,
                        data: serie.puntos,
                        borderColor: '#0d6efd',
                        backgroundColor: '#0d6efd',
                        pointRadius: 2,
                        stepped: esLogico ? 'after' : false,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: { left: 12, right: 12 },
                    },
                    scales: {
                        x: {
                            type: 'linear',
                            min: rango.desde,
                            max: rango.hasta,
                            ticks: {
                                maxTicksLimit: 6,
                                callback: valor => formatoFecha.format(Number(valor)),
                            },
                        },
                        y: {
                            // Usa el mismo ancho para alinear los gráficos.
                            afterFit: eje => {
                                eje.width = 80;
                            },
                            ...(esLogico ? {
                                min: 0,
                                max: 1,
                                ticks: {
                                    stepSize: 1,
                                    callback: etiquetaEstado,
                                },
                            } : {
                                title: {
                                    display: Boolean(serie.unidad),
                                    text: serie.unidad ?? '',
                                },
                            }),
                        },
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: puntos => formatoFecha.format(puntos[0].parsed.x),
                                label: punto => esLogico
                                    ? etiquetaEstado(punto.parsed.y)
                                    : `${punto.parsed.y} ${serie.unidad ?? ''}`.trim(),
                            },
                        },
                    },
                },
            });
        }
    })();
</script>
@endsection
