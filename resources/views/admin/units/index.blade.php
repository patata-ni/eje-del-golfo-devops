@extends('layouts.admin')

@section('content')
    <div class="dashboard-header">
        <h1>Gestión de Unidades</h1>
        <p>Administra los autobuses de la flota.</p>
    </div>

    @if(session('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif

    <!-- FORMULARIO PARA AGREGAR -->
    <div class="dashboard-panel" style="margin-bottom: 2rem;">
        <h3>Registrar Nueva Unidad</h3>
        <form action="{{ route('admin.units.store') }}" method="POST" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-top: 1rem;">
            @csrf
            <input type="text" name="numero_unidad" placeholder="Número (Ej. BUS-026)" class="admin-input" required>
            <input type="text" name="placas" placeholder="Placas" class="admin-input" required>
            <input type="text" name="marca" placeholder="Marca" class="admin-input">
            <input type="text" name="modelo" placeholder="Modelo" class="admin-input">
            <input type="number" name="anio" placeholder="Año" class="admin-input">
            <input type="number" name="capacidad" placeholder="Capacidad (Asientos)" class="admin-input" required>
            
            <select name="estado" class="admin-input" required>
                <option value="disponible">Disponible</option>
                <option value="en_mantenimiento">En Mantenimiento</option>
                <option value="fuera_de_servicio">Fuera de Servicio</option>
            </select>
            
            <button type="submit" class="btn-primary" style="grid-column: span 3; justify-self: start;">Guardar Unidad</button>
        </form>
    </div>

    <!-- TABLA DE UNIDADES -->
    <div class="dashboard-panel">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3>Listado de Unidades</h3>
            <!-- BUSCADOR -->
            <form action="{{ route('admin.units.index') }}" method="GET" style="display: flex; gap: 0.5rem;">
                <input type="text" name="search" placeholder="Buscar por número o placa..." class="admin-input" style="width: 250px;" value="{{ request('search') }}">
                <button type="submit" class="btn-primary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Buscar</button>
            </form>
        </div>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Placas</th>
                    <th>Capacidad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($unidades as $unidad)
                <tr>
                    <td><strong>{{ $unidad->numero_unidad }}</strong></td>
                    <td>{{ $unidad->placas }}</td>
                    <td>{{ $unidad->capacidad }} lugares</td>
                    <td>
                        <span class="badge {{ $unidad->estado == 'disponible' ? 'success' : 'warning' }}">
                            {{ ucfirst(str_replace('_', ' ', $unidad->estado)) }}
                        </span>
                    </td>
                    <td style="display: flex; gap: 1rem;">
                        <button onclick="abrirModalEditar({{ $unidad }})" style="color: var(--verde-esmeralda); background: none; border: none; cursor: pointer; font-weight: 600;">Editar</button>
                        <form action="{{ route('admin.units.destroy', $unidad->id_unidad) }}" method="POST" onsubmit="return confirm('¿Seguro que quieres eliminar esta unidad?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="color: #c0392b; background: none; border: none; cursor: pointer; font-weight: 600;">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #999; padding: 2rem;">No se encontraron unidades.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- PAGINACIÓN -->
        <div style="margin-top: 1.5rem;">
            {{ $unidades->links() }}
        </div>
    </div>

    <!-- MODAL DE EDICIÓN -->
    <div id="modal-editar" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: 12px; width: 100%; max-width: 500px;">
            <h3 style="margin-bottom: 1.5rem;">Editar Unidad</h3>
            <form id="form-editar" method="POST">
                @csrf
                @method('PUT')
                <div style="display: grid; gap: 1rem;">
                    <input type="text" name="numero_unidad" id="edit-numero" class="admin-input" required>
                    <input type="text" name="placas" id="edit-placas" class="admin-input" required>
                    <input type="text" name="marca" id="edit-marca" class="admin-input" placeholder="Marca">
                    <input type="text" name="modelo" id="edit-modelo" class="admin-input" placeholder="Modelo">
                    <input type="number" name="anio" id="edit-anio" class="admin-input" placeholder="Año">
                    <input type="number" name="capacidad" id="edit-capacidad" class="admin-input" required>
                    <select name="estado" id="edit-estado" class="admin-input" required>
                        <option value="disponible">Disponible</option>
                        <option value="en_mantenimiento">En Mantenimiento</option>
                        <option value="fuera_de_servicio">Fuera de Servicio</option>
                    </select>
                </div>
                <div style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" onclick="cerrarModal()" style="background: #eee; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; cursor: pointer; font-weight: 600;">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModalEditar(unidad) {
            document.getElementById('edit-numero').value = unidad.numero_unidad;
            document.getElementById('edit-placas').value = unidad.placas;
            document.getElementById('edit-marca').value = unidad.marca || '';
            document.getElementById('edit-modelo').value = unidad.modelo || '';
            document.getElementById('edit-anio').value = unidad.anio || '';
            document.getElementById('edit-capacidad').value = unidad.capacidad;
            document.getElementById('edit-estado').value = unidad.estado;
            document.getElementById('form-editar').action = `/admin/units/${unidad.id_unidad}`;
            document.getElementById('modal-editar').style.display = 'flex';
        }

        function cerrarModal() {
            document.getElementById('modal-editar').style.display = 'none';
        }
    </script>
@endsection