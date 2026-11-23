@extends('layouts.admin')

@section('content')
    <div class="dashboard-header">
        <h1>Gestión de Accesos al Sistema</h1>
        <p>Administra quién puede entrar al panel de administración o al de mecánicos.</p>
    </div>

    @if(session('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif
    
    @if($errors->any())
        <div style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- FORMULARIO PARA AGREGAR ACCESO -->
    <div class="dashboard-panel" style="margin-bottom: 2rem;">
        <h3>Dar de Alta Nuevo Acceso</h3>
        <form action="{{ route('admin.users.store') }}" method="POST" style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: 1rem; margin-top: 1rem; align-items: end;">
            @csrf
            <div>
                <label style="font-size: 0.8rem; color: #666;">Nombre Completo</label>
                <input type="text" name="name" placeholder="Ej. Pedro Pérez" class="admin-input" required>
            </div>
            <div>
                <label style="font-size: 0.8rem; color: #666;">Correo (debe ser @ejegolfo.com)</label>
                <input type="email" name="email" placeholder="pedro@ejegolfo.com" class="admin-input" required>
            </div>
            <div>
                <label style="font-size: 0.8rem; color: #666;">Contraseña Temporal</label>
                <input type="password" name="password" placeholder="Mínimo 6 caracteres" class="admin-input" required>
            </div>
            <div>
                <label style="font-size: 0.8rem; color: #666;">Rol de Acceso</label>
                <select name="role_id" class="admin-input" required>
                    <option value="1">Administrador</option>
                    <option value="2">Mecánico</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">Crear Acceso</button>
        </form>
    </div>

    <!-- TABLA DE USUARIOS DEL SISTEMA -->
    <div class="dashboard-panel">
        <h3>Usuarios con Acceso</h3>
        <table class="admin-table" style="margin-top: 1rem;">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $user)
                <tr>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge" style="background: #eef2ff; color: #4338ca;">
                            {{ $user->role_id == 1 ? 'Administrador' : 'Mecánico' }}
                        </span>
                    </td>
                    <td>
                        <!-- Evitamos que el admin actual se elimine a sí mismo -->
                        @if(Auth::id() !== $user->id)
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('¿Revocar acceso a este usuario?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="color: #c0392b; background: none; border: none; cursor: pointer; font-weight: 600;">Revocar Acceso</button>
                        </form>
                        @else
                        <span style="color: #999; font-size: 0.8rem;">Tu cuenta</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #999;">No hay usuarios de sistema registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection