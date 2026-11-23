@extends('layouts.admin')

@section('content')
    <div class="dashboard-header">
        <h1>Reporte de Incidencias</h1>
        <p>Registra fallas en las unidades y da seguimiento a su resolución.</p>
    </div>

    @if(session('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif

    <!-- FORMULARIO PARA REPORTAR -->
    <div class="dashboard-panel" style="margin-bottom: 2rem; border-left: 4px;">
        <h3>Reportar Nueva Incidencia</h3>
        <form action="{{ route('admin.incidents.store') }}" method="POST" style="display: grid; grid-template-columns: 1fr 1fr 2fr 1fr auto; gap: 1rem; margin-top: 1rem; align-items: end;">
            @csrf
            <div>
                <label style="font-size: 0.8rem; color: #666;">Unidad</label>
                <select name="unidad_id" class="admin-input" required>
                    <option value="">Selecciona...</option>
                    @foreach($unidades as $unidad)
                        <option value="{{ $unidad->id_unidad }}">{{ $unidad->numero_unidad }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size: 0.8rem; color: #666;">Tipo de Falla</label>
                <input type="text" name="tipo" placeholder="Ej. Eléctrica, Frenos" class="admin-input" required>
            </div>
            <div>
                <label style="font-size: 0.8rem; color: #666;">Descripción</label>
                <input type="text" name="descripcion" placeholder="Describe el problema..." class="admin-input" required>
            </div>
            <div>
                <label style="font-size: 0.8rem; color: #666;">Prioridad</label>
                <select name="prioridad" class="admin-input" required>
                    <option value="baja">Baja</option>
                    <option value="media">Media</option>
                    <option value="alta">Alta</option>
                    <option value="critica">Crítica</option>
                </select>
            </div>
            <button type="submit" class="btn-primary" style="background-color: #c0392b;">Reportar</button>
        </form>
    </div>

    <!-- TABLA DE INCIDENCIAS -->
    <div class="dashboard-panel">
        <h3>Historial de Incidencias</h3>
        <table class="admin-table" style="margin-top: 1rem;">
            <thead>
                <tr>
                    <th>Unidad</th>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incidencias as $inc)
                <tr>
                    <td><strong>{{ $inc->unit->numero_unidad ?? 'N/A' }}</strong></td>
                    <td>{{ $inc->tipo }}</td>
                    <td>{{ $inc->descripcion }}</td>
                    <td>
                        @php
                            $badgeClass = match($inc->prioridad) {
                                'critica' => 'danger',
                                'alta' => 'warning',
                                'media' => 'success',
                                default => 'success'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($inc->prioridad) }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $inc->estado == 'cerrada' ? 'success' : 'warning' }}">
                            {{ ucfirst(str_replace('_', ' ', $inc->estado)) }}
                        </span>
                    </td>
                    <td style="display: flex; gap: 1rem;">
                        <button onclick="abrirModalIncidencia({{ $inc }})" style="color: var(--verde-esmeralda); background: none; border: none; cursor: pointer; font-weight: 600;">Actualizar</button>
                        
                        <form action="{{ route('admin.incidents.destroy', $inc->id_incidencia) }}" method="POST" onsubmit="return confirm('¿Eliminar incidencia?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="color: #c0392b; background: none; border: none; cursor: pointer; font-weight: 600;">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #999;">No hay incidencias reportadas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- MODAL DE ACTUALIZACIÓN -->
    <div id="modal-incidencia" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: 12px; width: 100%; max-width: 400px;">
            <h3 style="margin-bottom: 1.5rem;">Actualizar Estado de Incidencia</h3>
            <form id="form-incidencia" method="POST">
                @csrf @method('PUT')
                <div style="display: grid; gap: 1rem;">
                    <div>
                        <label style="font-size: 0.8rem; color: #666;">Estado de la Incidencia</label>
                        <select name="estado" id="edit-i-estado" class="admin-input" required>
                            <option value="reportada">Reportada</option>
                            <option value="en_revision">En Revisión</option>
                            <option value="atendida">Atendida</option>
                            <option value="cerrada">Cerrada</option>
                        </select>
                    </div>
                </div>
                <div style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" onclick="document.getElementById('modal-incidencia').style.display='none'" style="background: #eee; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; cursor: pointer;">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModalIncidencia(inc) {
            document.getElementById('edit-i-estado').value = inc.estado;
            document.getElementById('form-incidencia').action = `/admin/incidents/${inc.id_incidencia}`;
            document.getElementById('modal-incidencia').style.display = 'flex';
        }
    </script>
@endsection