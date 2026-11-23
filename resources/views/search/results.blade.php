@extends('layouts.app')

@section('content')
<div class="container">
    <div class="results-header">
        <h2>{{ count($corridas) }} salidas encontradas</h2>
        <span>Ordenar por precio</span>
    </div>

    <div class="results-list">
        @foreach($corridas as $corrida)
        <div class="result-card">
            <div class="result-info">
                <span class="badge-directo">{{ $corrida['tipo'] }}</span>
                <span class="asientos">{{ $corrida['asientos'] }} asientos disponibles</span>
            </div>

            <div class="result-schedule">
                <div class="time">
                    <h3>{{ $corrida['hora_salida'] }}</h3>
                    <p>{{ $corrida['origen'] }}</p>
                </div>
                <div class="route-line">
                    <p>{{ $corrida['duracion'] }}</p>
                    <div class="line">
                        <span>
                            <svg class="inline-bus-icon" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M11 5h26a5 5 0 0 1 5 5v25a4 4 0 0 1-4 4H10a4 4 0 0 1-4-4V10a5 5 0 0 1 5-5Z" />
                                <path d="M7 25h34M12 10h24v11H12zM12 30h5m14 0h5" />
                                <circle cx="15" cy="38" r="3" />
                                <circle cx="33" cy="38" r="3" />
                            </svg>
                            {{ $corrida['unidad'] }}
                        </span>
                    </div>
                </div>
                <div class="time text-right">
                    <h3>{{ $corrida['hora_llegada'] }}</h3>
                    <p>{{ $corrida['destino'] }}</p>
                </div>
            </div>

            <div class="result-footer">
                <a href="#" class="link-detalles">Ver detalles y paradas</a>

                @auth
                    <span class="precio">${{ $corrida['precio'] }}</span>
                @else
                    <a href="#" class="btn-precio">Ver precio</a>
                @endauth
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection