@extends('layouts.admin')

@section('content')
    <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Programación de Salidas</h1>
            <p>Asigna unidades y choferes a los horarios en fechas específicas.</p>
        </div>
        <a href="{{ route('admin.pdf.departures') }}" target="_blank" class="btn-primary" style="background-color: #00845A; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
            📄 Descargar Asignaciones
        </a>
    </div>

    @if(session('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif

    <div class="dashboard-panel" style="margin-bottom: 2rem;">
        <h3>Programar Nueva Salida</h3>
        <form action="{{ route('admin.departures.store') }}" method="POST" style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem; margin-top: 1rem; align-items: end;">
            @csrf
            <div>
                <label style="font-size: 0.8rem; color: #666;">Horario / Ruta</label>
                <select name="horario_id" class="admin-input" required>
                    <option value="">Elegir horario...</option>
                    @foreach($horarios as $horario)
                        <option value="{{ $horario->id_horario }}">
                            {{ $horario->route->origen }} - {{ $horario->route->destino }} ({{ $horario->hora_salida }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size: 0.8rem; color: #666;">Fecha</label>
                <input type="date" name="fecha" class="admin-input" required min="{{ date('Y-m-d') }}">
            </div>
            <div>
                <label style="font-size: 0.8rem; color: #666;">Unidad (Autobús)</label>
                <select name="unidad_id" class="admin-input" required>
                    <option value="">Elegir unidad...</option>
                    @foreach($unidades as $unidad)
                        <option value="{{ $unidad->id_unidad }}">{{ $unidad->numero_unidad }} ({{ $unidad->placas }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size: 0.8rem; color: #666;">Chofer</label>
                <select name="chofer_id" class="admin-input" required>
                    <option value="">Elegir chofer...</option>
                    @foreach($choferes as $chofer)
                        <option value="{{ $chofer->id_chofer }}">
                            {{ $chofer->personal->nombre }} {{ $chofer->personal->apellido_paterno }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary" style="grid-column: span 4; justify-self: start;">Programar Salida</button>
        </form>
    </div>

    <div class="dashboard-panel">
        <h3>Salidas Programadas</h3>
        <table class="admin-table" style="margin-top: 1rem;">
            <thead>
                <tr>
                    <th>Ruta</th>
                    <th>Fecha y Hora</th>
                    <th>Unidad</th>
                    <th>Chofer</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($salidas as $salida)
                <tr>
                    <td><strong>{{ $salida->schedule->route->origen ?? 'N/A' }} - {{ $salida->schedule->route->destino ?? 'N/A' }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($salida->fecha)->format('d/m/Y') }} a las {{ $salida->schedule->hora_salida ?? '' }}</td>
                    <td>{{ $salida->unit->numero_unidad ?? 'Sin asignar' }}</td>
                    <td>{{ $salida->driver->personal->nombre ?? 'Sin asignar' }}</td>
                    <td><span class="badge success">{{ ucfirst($salida->estado) }}</span></td>
                    <td style="display: flex; gap: 1rem;">
                        <button onclick="abrirModalEditarSalida({{ $salida }})" style="color: var(--verde-esmeralda); background: none; border: none; cursor: pointer; font-weight: 600;">Editar</button>
                        <form action="{{ route('admin.departures.destroy', $salida->id_salida) }}" method="POST" onsubmit="return confirm('¿Cancelar salida?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="color: #c0392b; background: none; border: none; cursor: pointer; font-weight: 600;">Cancelar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #999;">No hay salidas programadas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- MODAL DE EDICIÓN -->
<div id="modal-editar-salida" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 2rem; border-radius: 12px; width: 100%; max-width: 500px;">
        <h3 style="margin-bottom: 1.5rem;">Editar Salida</h3>
        <form id="form-editar-salida" method="POST">
            @csrf @method('PUT')
            
            <div style="display: grid; gap: 1rem;">
                <div>
                    <label style="font-size: 0.8rem; color: #666;">Unidad Asignada</label>
                    <select name="unidad_id" id="edit-sal-unidad" class="admin-input" required>
                        @foreach($unidades as $unidad)
                            <option value="{{ $unidad->id_unidad }}">{{ $unidad->numero_unidad }} ({{ $unidad->placas }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8rem; color: #666;">Chofer Asignado</label>
                    <select name="chofer_id" id="edit-sal-chofer" class="admin-input" required>
                        @foreach($choferes as $chofer)
                            <option value="{{ $chofer->id_chofer }}">{{ $chofer->personal->nombre }} {{ $chofer->personal->apellido_paterno }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8rem; color: #666;">Estado</label>
                    <select name="estado" id="edit-sal-estado" class="admin-input">
                        <option value="programada">Programada</option>
                        <option value="en_curso">En Curso</option>
                        <option value="completada">Completada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>
            </div>
            
            <div style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('modal-editar-salida').style.display='none'" style="background: #eee; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; cursor: pointer;">Cancelar</button>
                <button type="submit" class="btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalEditarSalida(salida) {
        document.getElementById('edit-sal-unidad').value = salida.unidad_id;
        document.getElementById('edit-sal-chofer').value = salida.chofer_id;
        document.getElementById('edit-sal-estado').value = salida.estado;
        document.getElementById('form-editar-salida').action = `/admin/departures/${salida.id_salida}`;
        document.getElementById('modal-editar-salida').style.display = 'flex';
    }
</script>
@endsection