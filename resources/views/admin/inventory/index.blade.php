@extends('layouts.admin')

@section('content')
    <div class="dashboard-header">
        <h1>Inventario de Piezas</h1>
        <p>Controla el stock de refacciones para el taller.</p>
    </div>

    @if(session('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">{{ session('success') }}</div>
    @endif

    <div class="dashboard-panel" style="margin-bottom: 2rem; border-left: 4px solid var(--verde-esmeralda);">
        <h3>Registrar Nueva Pieza</h3>
        <form action="{{ route('admin.inventory.store') }}" method="POST" style="display: grid; grid-template-columns: 2fr 2fr 1fr 1fr auto; gap: 1rem; margin-top: 1rem; align-items: end;">
            @csrf
            <div>
                <label style="font-size: 0.8rem; color: #666;">Nombre de la Pieza</label>
                <input type="text" name="nombre" placeholder="Ej. Filtro de aceite" class="admin-input" required>
            </div>
            <div>
                <label style="font-size: 0.8rem; color: #666;">Descripción</label>
                <input type="text" name="descripcion" placeholder="Ej. Para motor Cummins" class="admin-input">
            </div>
            <div>
                <label style="font-size: 0.8rem; color: #666;">Stock Mínimo</label>
                <input type="number" name="stock_minimo" placeholder="Ej. 5" class="admin-input" required>
            </div>
            <div>
                <label style="font-size: 0.8rem; color: #666;">Cantidad Inicial</label>
                <input type="number" name="cantidad_actual" placeholder="Ej. 20" class="admin-input" required>
            </div>
            <button type="submit" class="btn-primary">Guardar Pieza</button>
        </form>
    </div>

    <div class="dashboard-panel">
        <h3>Catálogo de Refacciones</h3>
        <table class="admin-table" style="margin-top: 1rem;">
            <thead>
                <tr>
                    <th>Pieza</th>
                    <th>Stock Mínimo</th>
                    <th>Stock Actual</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($piezas as $p)
                <tr>
                    <td><strong>{{ $p->nombre }}</strong><br><small style="color:#999">{{ $p->descripcion }}</small></td>
                    <td>{{ $p->stock_minimo }}</td>
                    <td>
                        <strong style="color: {{ ($p->inventory->cantidad_actual ?? 0) <= $p->stock_minimo ? '#c0392b' : 'var(--verde-bosque)' }};">
                            {{ $p->inventory->cantidad_actual ?? 0 }}
                        </strong>
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
                    <td colspan="4" style="text-align: center; color: #999;">No hay piezas registradas en el inventario.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection