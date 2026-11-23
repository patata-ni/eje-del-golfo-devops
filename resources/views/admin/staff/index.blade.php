@extends('layouts.admin')

@section('content')
    <div class="dashboard-header">
        <h1>Gestión de Personal</h1>
        <p>Administra a los empleados, mecánicos y administradores.</p>
    </div>

    @if(session('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <ul>@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
    @endif

    <div class="dashboard-panel" style="margin-bottom: 2rem;">
        <h3>Registrar Nuevo Empleado</h3>
        <form action="{{ route('admin.staff.store') }}" method="POST" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-top: 1rem;">
            @csrf
            <input type="text" name="nombre" placeholder="Nombre(s)" class="admin-input" required>
            <input type="text" name="apellido_paterno" placeholder="Apellido Paterno" class="admin-input" required>
            <input type="text" name="apellido_materno" placeholder="Apellido Materno" class="admin-input">
            <input type="email" name="correo" placeholder="Correo electrónico" class="admin-input" required>
            <input type="password" name="contrasena" placeholder="Contraseña" class="admin-input" required>
            <input type="text" name="telefono" placeholder="Teléfono" class="admin-input">
            <select name="rol_id" class="admin-input" required>
                <option value="">Selecciona un rol...</option>
                @foreach($roles as $rol)
                    <option value="{{ $rol->id_rol }}">{{ $rol->nombre }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary" style="grid-column: span 3; justify-self: start;">Guardar Empleado</button>
        </form>
    </div>

    <div class="dashboard-panel">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3>Listado de Personal</h3>
            <form action="{{ route('admin.staff.index') }}" method="GET" style="display: flex; gap: 0.5rem;">
                <input type="text" name="search" placeholder="Buscar por nombre o correo..." class="admin-input" style="width: 250px;" value="{{ request('search') }}">
                <button type="submit" class="btn-primary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Buscar</button>
            </form>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nombre Completo</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($personal as $persona)
                <tr>
                    <td><strong>{{ $persona->nombre }} {{ $persona->apellido_paterno }}</strong></td>
                    <td>{{ $persona->correo }}</td>
                    <td><span class="badge" style="background: #eef2ff; color: #4338ca;">{{ $persona->role->nombre ?? 'Sin rol' }}</span></td>
                    <td><span class="badge success">{{ ucfirst($persona->estado) }}</span></td>
                    <td style="display: flex; gap: 1rem;">
                        <button onclick="abrirModalEditarStaff({{ $persona }})" style="color: var(--verde-esmeralda); background: none; border: none; cursor: pointer; font-weight: 600;">Editar</button>
                        <form action="{{ route('admin.staff.destroy', $persona->id_personal) }}" method="POST" onsubmit="return confirm('¿Eliminar a este empleado?');">
                            @csrf @method('DELETE')
                            <button type="submit" style="color: #c0392b; background: none; border: none; cursor: pointer; font-weight: 600;">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align: center; color: #999; padding: 2rem;">No se encontró personal.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 1.5rem;">
            {{ $personal->links() }}
        </div>
    </div>

    <!-- MODAL DE EDICIÓN -->
    <div id="modal-editar-staff" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: 12px; width: 100%; max-width: 500px;">
            <h3 style="margin-bottom: 1.5rem;">Editar Personal</h3>
            <form id="form-editar-staff" method="POST">
                @csrf @method('PUT')
                <div style="display: grid; gap: 1rem; grid-template-columns: 1fr 1fr;">
                    <input type="text" name="nombre" id="edit-s-nombre" class="admin-input" required>
                    <input type="text" name="apellido_paterno" id="edit-s-paterno" class="admin-input" required>
                    <input type="text" name="apellido_materno" id="edit-s-materno" class="admin-input">
                    <input type="text" name="telefono" id="edit-s-telefono" class="admin-input">
                    <input type="email" name="correo" id="edit-s-correo" class="admin-input" required>
                    <select name="rol_id" id="edit-s-rol" class="admin-input">
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id_rol }}">{{ $rol->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" onclick="document.getElementById('modal-editar-staff').style.display='none'" style="background: #eee; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; cursor: pointer;">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function abrirModalEditarStaff(persona) {
            document.getElementById('edit-s-nombre').value = persona.nombre;
            document.getElementById('edit-s-paterno').value = persona.apellido_paterno;
            document.getElementById('edit-s-materno').value = persona.apellido_materno || '';
            document.getElementById('edit-s-telefono').value = persona.telefono || '';
            document.getElementById('edit-s-correo').value = persona.correo;
            document.getElementById('edit-s-rol').value = persona.rol_id;
            document.getElementById('form-editar-staff').action = `/admin/staff/${persona.id_personal}`;
            document.getElementById('modal-editar-staff').style.display = 'flex';
        }
    </script>
@endsection