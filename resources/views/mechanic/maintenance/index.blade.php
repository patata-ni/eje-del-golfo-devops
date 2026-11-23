@extends('layouts.mechanic')

@section('content')
    <div class="dashboard-header">
        <h1>Mis Mantenimientos</h1>
        <p>Gestiona las unidades que están actualmente en el taller.</p>
    </div>

    @if(session('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif

    <!-- FORMULARIO PARA ABRIR NUEVO MANTENIMIENTO -->
    <div class="dashboard-panel" style="margin-bottom: 2rem; border-left: 4px solid #0fb541;">
        <h3>Ingresar Unidad al Taller (Manual)</h3>
        <form action="{{ route('mechanic.maintenance.store') }}" method="POST" style="display: grid; grid-template-columns: 2fr 1fr 2fr auto; gap: 1rem; margin-top: 1rem; align-items: end;">
            @csrf
            <div>
                <label style="font-size: 0.8rem; color: #666;">Unidad (Autobús)</label>
                <select name="unidad_id" class="admin-input" required>
                    <option value="">Selecciona la unidad...</option>
                    @foreach($unidades as $unidad)
                        <option value="{{ $unidad->id_unidad }}">{{ $unidad->numero_unidad }} ({{ $unidad->placas }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size: 0.8rem; color: #666;">Tipo</label>
                <select name="tipo" class="admin-input" required>
                    <option value="correctivo">Correctivo</option>
                    <option value="preventivo">Preventivo</option>
                </select>
            </div>
            <div>
                <label style="font-size: 0.8rem; color: #666;">Diagnóstico / Falla</label>
                <input type="text" name="diagnostico" placeholder="Ej. Ruido en frenos" class="admin-input" required>
            </div>
            <button type="submit" class="btn-primary" style="background-color: #0fb541;">Abrir Orden</button>
        </form>
    </div>

    <!-- TABLA DE MANTENIMIENTOS -->
    <div class="dashboard-panel">
        <h3>Órdenes de Trabajo Activas</h3>
        <table class="admin-table" style="margin-top: 1rem;">
            <thead>
                <tr>
                    <th>Unidad</th>
                    <th>Tipo</th>
                    <th>Diagnóstico</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mantenimientos as $m)
                <tr>
                    <td><strong>{{ $m->unit->numero_unidad ?? 'N/A' }}</strong></td>
                    <td>
                        <span class="badge {{ $m->tipo == 'correctivo' ? 'danger' : 'success' }}">
                            {{ ucfirst($m->tipo) }}
                        </span>
                    </td>
                    <td>{{ $m->diagnostico }}</td>
                    <td>
                        <span class="badge {{ $m->estado == 'pendiente' ? 'warning' : 'success' }}">
                            {{ ucfirst(str_replace('_', ' ', $m->estado)) }}
                        </span>
                    </td>
                    <td>
                        <button onclick="abrirModalAvance({{ $m }})" class="btn-primary" style="background-color: #0fb541; padding: 0.4rem 0.8rem; font-size: 0.8rem;">Registrar Avance</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #999; padding: 2rem;">No hay mantenimientos activos en este momento.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- MODAL: REGISTRAR AVANCE -->
    <div id="modal-avance" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: 12px; width: 100%; max-width: 600px;">
            <h3 style="margin-bottom: 1.5rem;">Registrar Avance</h3>
            <form id="form-avance" method="POST">
                @csrf @method('PUT')
                <div style="display: grid; gap: 1rem;">
                    <div>
                        <label style="font-size: 0.8rem; color: #666;">Estado</label>
                        <select name="estado" id="av-estado" class="admin-input" required>
                            <option value="pendiente">Pendiente</option>
                            <option value="en_proceso">En Proceso</option>
                            <option value="finalizado">Finalizado (Liberar Unidad)</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; color: #666;">Trabajo Realizado / Observaciones</label>
                        <textarea name="trabajo_realizado" id="av-trabajo" class="admin-input" rows="4"></textarea>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed #ccc;">
                    <div>
                        <label style="font-size: 0.8rem; color: #666;">Pieza Utilizada (Opcional)</label>
                        <select name="pieza_id" class="admin-input">
                            <option value="">No usar pieza...</option>
                                @foreach($piezas as $p)
                                    <option value="{{ $p->id_pieza }}">{{ $p->nombre }} (Stock: {{ $p->inventory->cantidad_actual }})</option>
                                @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; color: #666;">Cantidad</label>
                        <input type="number" name="cantidad_usada" class="admin-input" min="1" value="1">
                    </div>
                </div>
                <div style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" onclick="document.getElementById('modal-avance').style.display='none'" style="background: #eee; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; cursor: pointer;">Cancelar</button>
                    <button type="submit" class="btn-primary" style="background-color: #0fb541;">Guardar Avance</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModalAvance(m) {
            document.getElementById('av-estado').value = m.estado;
            document.getElementById('av-trabajo').value = m.trabajo_realizado || '';
            document.getElementById('form-avance').action = `/mechanic/maintenance/${m.id_mantenimiento}`;
            document.getElementById('modal-avance').style.display = 'flex';
        }
    </script>
@endsection