@extends('layouts.admin')

@section('content')
    <div class="dashboard-header">
        <h1>Gestión de Horarios</h1>
        <p>Define las horas de salida base para cada ruta.</p>
    </div>

    @if(session('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif

    <div class="dashboard-panel" style="margin-bottom: 2rem;">
        <h3>Crear Horario Base</h3>
        <form action="{{ route('admin.schedules.store') }}" method="POST" style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 1rem; margin-top: 1rem; align-items: end;">
            @csrf
            <div>
                <label style="font-size: 0.8rem; color: #666;">Selecciona la Ruta</label>
                <select name="ruta_id" class="admin-input" required>
                    <option value="">Elegir ruta...</option>
                    @foreach($rutas as $ruta)
                        <option value="{{ $ruta->id_ruta }}">{{ $ruta->origen }} - {{ $ruta->destino }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size: 0.8rem; color: #666;">Hora de Salida</label>
                <input type="time" name="hora_salida" class="admin-input" required>
            </div>
            <button type="submit" class="btn-primary">Guardar Horario</button>
        </form>
    </div>

    <div class="dashboard-panel">
        <h3>Listado de Horarios</h3>
        <table class="admin-table" style="margin-top: 1rem;">
            <thead>
                <tr>
                    <th>Ruta</th>
                    <th>Hora de Salida</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($horarios as $horario)
                <tr>
                    <td>{{ $horario->route->origen ?? 'N/A' }} - {{ $horario->route->destino ?? 'N/A' }}</td>
                    <td><strong>{{ \Carbon\Carbon::parse($horario->hora_salida)->format('h:i A') }}</strong></td>
                    <td><span class="badge success">{{ ucfirst($horario->estado) }}</span></td>
                    <td style="display: flex; gap: 1rem;">
                        <button onclick="abrirModalEditarHorario({{ $horario }})" style="color: var(--verde-esmeralda); background: none; border: none; cursor: pointer; font-weight: 600;">Editar</button>
                        <form action="{{ route('admin.schedules.destroy', $horario->id_horario) }}" method="POST" onsubmit="return confirm('¿Eliminar horario?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="color: #c0392b; background: none; border: none; cursor: pointer; font-weight: 600;">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #999;">No hay horarios registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="modal-editar-horario" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 2rem; border-radius: 12px; width: 100%; max-width: 400px;">
        <h3 style="margin-bottom: 1.5rem;">Editar Horario</h3>
        <form id="form-editar-horario" method="POST">
            @csrf @method('PUT')
            <div style="display: grid; gap: 1rem;">
                <label style="font-size: 0.8rem; color: #666;">Hora de salida</label>
                <input type="time" name="hora_salida" id="edit-h-hora" class="admin-input" required>
                <label style="font-size: 0.8rem; color: #666;">Estado</label>
                <select name="estado" id="edit-h-estado" class="admin-input">
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
            <div style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('modal-editar-horario').style.display='none'" style="background: #eee; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; cursor: pointer;">Cancelar</button>
                <button type="submit" class="btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
<script>
    function abrirModalEditarHorario(horario) {
        const horaCorta = horario.hora_salida.substring(0, 5); 
        document.getElementById('edit-h-hora').value = horaCorta;
        document.getElementById('edit-h-estado').value = horario.estado;
        document.getElementById('form-editar-horario').action = `/admin/schedules/${horario.id_horario}`;
        document.getElementById('modal-editar-horario').style.display = 'flex';
    }
</script>
@endsection