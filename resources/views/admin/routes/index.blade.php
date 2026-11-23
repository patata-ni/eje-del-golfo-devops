@extends('layouts.admin')

@section('content')
    <div class="dashboard-header">
        <h1>Gestión de Rutas</h1>
        <p>Administra los destinos disponibles para las salidas.</p>
    </div>

    @if(session('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif

    <!-- FORMULARIO PARA AGREGAR RUTA -->
    <div class="dashboard-panel" style="margin-bottom: 2rem;">
        <h3>Crear Nueva Ruta</h3>
        <form action="{{ route('admin.routes.store') }}" method="POST" style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem; margin-top: 1rem;">
            @csrf
            <input type="text" name="origen" placeholder="Origen (Ej. Poza Rica)" class="admin-input" required>
            <input type="text" name="destino" placeholder="Destino (Ej. Papantla)" class="admin-input" required>
            <input type="number" name="duracion_minutos" placeholder="Duración (minutos)" class="admin-input">
            <input type="number" step="0.01" name="precio" placeholder="Precio ($)" class="admin-input" required>
            
            <button type="submit" class="btn-primary" style="grid-column: span 4; justify-self: start;">Guardar Ruta</button>
        </form>
    </div>

    <!-- TABLA DE RUTAS -->
    <div class="dashboard-panel">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3>Listado de Rutas</h3>
            <!-- BUSCADOR -->
            <form action="{{ route('admin.routes.index') }}" method="GET" style="display: flex; gap: 0.5rem;">
                <input type="text" name="search" placeholder="Buscar por origen o destino..." class="admin-input" style="width: 250px;" value="{{ request('search') }}">
                <button type="submit" class="btn-primary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Buscar</button>
            </form>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Duración</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rutas as $ruta)
                <tr>
                    <td>{{ $ruta->origen }}</td>
                    <td>{{ $ruta->destino }}</td>
                    <td>{{ $ruta->duracion_minutos }} min</td>
                    <td>${{ $ruta->precio }}</td>
                    <td><span class="badge success">{{ ucfirst($ruta->estado) }}</span></td>
                    <td style="display: flex; gap: 1rem;">
                        <button onclick="abrirModalEditarRuta({{ $ruta }})" style="color: var(--verde-esmeralda); background: none; border: none; cursor: pointer; font-weight: 600;">Editar</button>
                        <form action="{{ route('admin.routes.destroy', $ruta->id_ruta) }}" method="POST" onsubmit="return confirm('¿Eliminar ruta?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="color: #c0392b; background: none; border: none; cursor: pointer; font-weight: 600;">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #999; padding: 2rem;">No se encontraron rutas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- PAGINACIÓN -->
        <div style="margin-top: 1.5rem;">
            {{ $rutas->links() }}
        </div>
    </div>

    <!-- MODAL DE EDICIÓN -->
    <div id="modal-editar-ruta" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: 12px; width: 100%; max-width: 500px;">
            <h3 style="margin-bottom: 1.5rem;">Editar Ruta</h3>
            <form id="form-editar-ruta" method="POST">
                @csrf @method('PUT')
                <div style="display: grid; gap: 1rem;">
                    <input type="text" name="origen" id="edit-r-origen" class="admin-input" required>
                    <input type="text" name="destino" id="edit-r-destino" class="admin-input" required>
                    <input type="number" name="duracion_minutos" id="edit-r-duracion" class="admin-input">
                    <input type="number" step="0.01" name="precio" id="edit-r-precio" class="admin-input" required>
                    <select name="estado" id="edit-r-estado" class="admin-input">
                        <option value="activa">Activa</option>
                        <option value="inactiva">Inactiva</option>
                    </select>
                </div>
                <div style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" onclick="document.getElementById('modal-editar-ruta').style.display='none'" style="background: #eee; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; cursor: pointer;">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function abrirModalEditarRuta(ruta) {
            document.getElementById('edit-r-origen').value = ruta.origen;
            document.getElementById('edit-r-destino').value = ruta.destino;
            document.getElementById('edit-r-duracion').value = ruta.duracion_minutos || '';
            document.getElementById('edit-r-precio').value = ruta.precio;
            document.getElementById('edit-r-estado').value = ruta.estado;
            document.getElementById('form-editar-ruta').action = `/admin/routes/${ruta.id_ruta}`;
            document.getElementById('modal-editar-ruta').style.display = 'flex';
        }
    </script>
@endsection