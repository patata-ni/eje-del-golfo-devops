@extends('layouts.admin')

@section('content')
    <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Gestión de Choferes</h1>
            <p>Asigna el número de empleado al personal operativo.</p>
        </div>
        <a href="{{ route('admin.drivers.pdf') }}" target="_blank" class="btn-primary" style="background-color: #00845A; text-decoration: none;">📄 Descargar Lista</a>
    </div>

    @if(session('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">{{ session('success') }}</div>
    @endif

    <div class="dashboard-panel" style="margin-bottom: 2rem;">
        <h3>Registrar Nuevo Chofer</h3>
        <form action="{{ route('admin.drivers.store') }}" method="POST" style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 1rem; margin-top: 1rem; align-items: end;">
            @csrf
            <div>
                <label style="font-size: 0.8rem; color: #666;">Selecciona al Empleado (Rol: Chofer)</label>
                <select name="personal_id" class="admin-input" required>
                    <option value="">Elegir personal...</option>
                    @foreach($personalDisponible as $persona)
                        <option value="{{ $persona->id_personal }}">{{ $persona->nombre }} {{ $persona->apellido_paterno }}</option>
                    @endforeach
                </select>
                @if($personalDisponible->isEmpty())
                    <small style="color: #c0392b;">No hay empleados con el rol "Chofer". Ve a Personal y asigna ese rol a alguien.</small>
                @endif
            </div>
            <div>
                <label style="font-size: 0.8rem; color: #666;">Número de Empleado (Automático)</label>
                <input type="text" value="{{ $nuevoNumero }}" class="admin-input" readonly style="background-color: #f0f0f0; cursor: not-allowed;">
            </div>
            <button type="submit" class="btn-primary" {{ $personalDisponible->isEmpty() ? 'disabled' : '' }}>Registrar Chofer</button>
        </form>
    </div>

    <div class="dashboard-panel">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3>Listado de Choferes</h3>
            <form action="{{ route('admin.drivers.index') }}" method="GET" style="display: flex; gap: 0.5rem;">
                <input type="text" name="search" placeholder="Buscar chofer..." class="admin-input" style="width: 250px;" value="{{ request('search') }}">
                <button type="submit" class="btn-primary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Buscar</button>
            </form>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Número de Empleado</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($choferes as $chofer)
                <tr>
                    <td><strong>{{ $chofer->personal->nombre ?? 'N/A' }} {{ $chofer->personal->apellido_paterno ?? '' }}</strong></td>
                    <td>{{ $chofer->personal->correo ?? 'N/A' }}</td>
                    <td>{{ $chofer->numero_empleado }}</td>
                    <td><span class="badge success">{{ ucfirst($chofer->estado) }}</span></td>
                    <td style="display: flex; gap: 1rem;">
                        <button onclick="abrirModalEditarChofer({{ $chofer }})" style="color: var(--verde-esmeralda); background: none; border: none; cursor: pointer; font-weight: 600;">Editar</button>
                        <form action="{{ route('admin.drivers.destroy', $chofer->id_chofer) }}" method="POST" onsubmit="return confirm('¿Eliminar chofer?');">
                            @csrf @method('DELETE')
                            <button type="submit" style="color: #c0392b; background: none; border: none; cursor: pointer; font-weight: 600;">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align: center; color: #999; padding: 2rem;">No se encontraron choferes.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 1.5rem;">
            {{ $choferes->links() }}
        </div>
    </div>

    <div id="modal-editar-chofer" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: 12px; width: 100%; max-width: 400px;">
            <h3 style="margin-bottom: 1.5rem;">Estado del Chofer</h3>
            <form id="form-editar-chofer" method="POST">
                @csrf @method('PUT')
                <p style="margin-bottom: 1rem;">Empleado: <strong id="edit-c-nombre"></strong></p>
                <div style="display: grid; gap: 1rem;">
                    <label style="font-size: 0.8rem; color: #666;">Nuevo Estado</label>
                    <select name="estado" id="edit-c-estado" class="admin-input">
                        <option value="disponible">Disponible</option>
                        <option value="en_ruta">En Ruta (Viajando)</option>
                        <option value="vacaciones">Vacaciones</option>
                        <option value="enfermo">Enfermo</option>
                        <option value="de_baja">Dado de Baja</option>
                    </select>
                </div>
                <div style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" onclick="document.getElementById('modal-editar-chofer').style.display='none'" style="background: #eee; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; cursor: pointer;">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function abrirModalEditarChofer(chofer) {
            document.getElementById('edit-c-nombre').textContent = chofer.personal.nombre + ' ' + chofer.personal.apellido_paterno;
            document.getElementById('edit-c-estado').value = chofer.estado;
            document.getElementById('form-editar-chofer').action = `/admin/drivers/${chofer.id_chofer}`;
            document.getElementById('modal-editar-chofer').style.display = 'flex';
        }
    </script>
@endsection