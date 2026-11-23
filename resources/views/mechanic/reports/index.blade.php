@extends('layouts.mechanic')

@section('content')
    <div class="dashboard-header">
        <h1>Reportes de Fallas</h1>
        <p>Revisa las fallas reportadas y acéptalas para ingresar la unidad al taller.</p>
    </div>

    @if(session('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif

    <div class="dashboard-panel" style="margin-top: 2rem;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Unidad</th>
                    <th>Falla Reportada</th>
                    <th>Prioridad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportesPendientes as $rep)
                <tr>
                    <td><strong>{{ $rep->unit->numero_unidad ?? 'N/A' }}</strong></td>
                    <td>{{ $rep->descripcion }}</td>
                    <td>
                        <span class="badge {{ $rep->prioridad == 'critica' ? 'danger' : 'warning' }}">
                            {{ ucfirst($rep->prioridad) }}
                        </span>
                    </td>
                    <td>
                        <button onclick="abrirModalReporte({{ $rep }})" class="btn-primary" style="background-color: #0fb541; padding: 0.4rem 0.8rem; font-size: 0.8rem;">Aceptar y Revisar</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #999; padding: 2rem;">No hay reportes de fallas pendientes.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- MODAL: ACEPTAR REPORTE -->
    <div id="modal-reporte" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: 12px; width: 100%; max-width: 500px;">
            <h3 style="margin-bottom: 1.5rem;">Aceptar Reporte e Ingresar al Taller</h3>
            <form action="{{ route('mechanic.maintenance.store') }}" method="POST">
                @csrf
                <input type="hidden" name="unidad_id" id="rep-unidad-id">
                <input type="hidden" name="incidencia_id" id="rep-incidencia-id">
                
                <div style="display: grid; gap: 1rem;">
                    <p><strong>Unidad:</strong> <span id="rep-unidad-nombre"></span></p>
                    <p><strong>Falla:</strong> <span id="rep-descripcion"></span></p>
                    
                    <div>
                        <label style="font-size: 0.8rem; color: #666;">Tipo de Mantenimiento</label>
                        <select name="tipo" class="admin-input" required>
                            <option value="correctivo">Correctivo (Reparación)</option>
                            <option value="preventivo">Preventivo (Servicio)</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; color: #666;">Asignar al Mecánico</label>
                        <select name="mecanico_asignado" class="admin-input" required>
                            <option value="">Selecciona quién lo reparará...</option>
                            @foreach($mecanicos as $mec)
                                <option value="{{ $mec->id_personal }}">{{ $mec->nombre }} {{ $mec->apellido_paterno }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; color: #666;">Diagnóstico</label>
                        <input type="text" name="diagnostico" id="rep-diagnostico" class="admin-input" required>
                    </div>
                </div>
                <div style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" onclick="document.getElementById('modal-reporte').style.display='none'" style="background: #eee; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; cursor: pointer;">Cancelar</button>
                    <button type="submit" class="btn-primary" style="background-color: #0fb541;">Ingresar al Taller</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModalReporte(rep) {
            document.getElementById('rep-unidad-id').value = rep.unidad_id;
            document.getElementById('rep-incidencia-id').value = rep.id_incidencia;
            document.getElementById('rep-unidad-nombre').textContent = rep.unit.numero_unidad;
            document.getElementById('rep-descripcion').textContent = rep.descripcion;
            document.getElementById('rep-diagnostico').value = rep.descripcion; 
            document.getElementById('modal-reporte').style.display = 'flex';
        }
    </script>
@endsection