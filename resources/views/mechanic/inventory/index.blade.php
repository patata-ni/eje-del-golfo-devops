@extends('layouts.mechanic')

@section('content')
    <div class="dashboard-header">
        <h1>Inventario de Piezas</h1>
        <p>Consulta el stock de refacciones disponibles en el almacén.</p>
    </div>

    <div class="dashboard-panel" style="margin-top: 2rem;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Pieza</th>
                    <th>Descripción</th>
                    <th>Ubicación</th>
                    <th>Stock Actual</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($piezas as $p)
                <tr>
                    <td><strong>{{ $p->nombre }}</strong></td>
                    <td>{{ $p->descripcion ?? 'Sin descripción' }}</td>
                    <td>{{ $p->inventory->ubicacion ?? 'Almacén Central' }}</td>
                    <td>
                        <strong style="color: {{ ($p->inventory->cantidad_actual ?? 0) <= $p->stock_minimo ? '#c0392b' : 'var(--verde-bosque)' }};">
                            {{ $p->inventory->cantidad_actual ?? 0 }}
                        </strong>
                        <small style="color: #999;">(Mín: {{ $p->stock_minimo }})</small>
                    </td>
                    <td>
                        @if(($p->inventory->cantidad_actual ?? 0) <= $p->stock_minimo)
                            <span class="badge danger">Stock Bajo</span>
                        @else
                            <span class="badge success">Suficiente</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #999; padding: 2rem;">No hay piezas registradas en el inventario.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection