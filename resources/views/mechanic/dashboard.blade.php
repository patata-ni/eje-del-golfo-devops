@extends('layouts.mechanic')

@section('content')
    <div class="dashboard-header">
        <h1>Dashboard de Taller</h1>
        <p>Resumen general de las operaciones del taller.</p>
    </div>

    <div class="dashboard-stats" style="grid-template-columns: repeat(2, 1fr);">
        <!-- Tarjeta de Reportes Nuevos -->
        <div class="stat-card" style="border-left: 4px solid #c0392b;">
            <div class="stat-icon">🚨</div>
            <div class="stat-details">
                <p>Reportes de Falla Pendientes</p>
                <h2>{{ $reportesCount }}</h2>
                <a href="{{ route('mechanic.reports.index') }}" style="font-size: 0.8rem; color: var(--verde-esmeralda); font-weight: bold;">Ver reportes →</a>
            </div>
        </div>

        <!-- Tarjeta de Unidades en Taller -->
        <div class="stat-card" style="border-left: 4px solid #0fb541;">
            <div class="stat-icon">🔧</div>
            <div class="stat-details">
                <p>Mantenimientos Activos</p>
                <h2>{{ $mantenimientosCount }}</h2>
                <a href="{{ route('mechanic.maintenance.index') }}" style="font-size: 0.8rem; color: var(--verde-esmeralda); font-weight: bold;">Ver taller →</a>
            </div>
        </div>
    </div>

    <!-- NUEVO PANEL DE GRÁFICA DE INVENTARIO -->
    <div class="dashboard-panel">
        <h3>Estado del Inventario (Stock Actual vs Mínimo)</h3>
        <div style="position: relative; height: 250px; width: 100%; margin-top: 1rem;">
            <canvas id="inventoryChart"></canvas>
        </div>
    </div>
    <!-- TABLA DE MECÁNICOS -->
    <div class="dashboard-panel" style="margin-top: 2rem;">
        <h3>Directorio de Mecánicos</h3>
        <table class="admin-table" style="margin-top: 1rem;">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Fecha de Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mecanicos as $mec)
                <tr>
                    <td><strong>{{ $mec->nombre }} {{ $mec->apellido_paterno }}</strong></td>
                    <td>{{ $mec->correo }}</td>
                    <td>{{ \Carbon\Carbon::parse($mec->created_at)->format('d/m/Y') }}</td>
                    <td>
                        <button onclick="abrirModalMecanico({{ $mec }})" style="color: var(--verde-esmeralda); background: none; border: none; cursor: pointer; font-weight: 600;">Visualizar</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #999;">No hay mecánicos registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- MODAL DE MECÁNICO -->
    <div id="modal-mecanico" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: 12px; width: 100%; max-width: 400px;">
            <h3 style="margin-bottom: 1.5rem; color: var(--verde-bosque);">Perfil del Mecánico</h3>
            
            <div style="display: grid; gap: 0.8rem; margin-bottom: 1.5rem;">
                <div>
                    <span style="font-size: 0.8rem; color: #999;">Nombre Completo</span>
                    <p style="font-weight: 600; color: #333;" id="modal-mec-nombre"></p>
                </div>
                <div>
                    <span style="font-size: 0.8rem; color: #999;">Correo Electrónico</span>
                    <p style="font-weight: 600; color: #333;" id="modal-mec-correo"></p>
                </div>
                <div>
                    <span style="font-size: 0.8rem; color: #999;">Teléfono</span>
                    <p style="font-weight: 600; color: #333;" id="modal-mec-telefono"></p>
                </div>
                <div>
                    <span style="font-size: 0.8rem; color: #999;">Fecha de Registro</span>
                    <p style="font-weight: 600; color: #333;" id="modal-mec-fecha"></p>
                </div>
            </div>
            
            <div style="display: flex; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('modal-mecanico').style.display='none'" style="background: #eee; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; cursor: pointer; font-weight: 600;">Cerrar</button>
            </div>
        </div>
    </div>

    <script>
        function abrirModalMecanico(mec) {
            document.getElementById('modal-mec-nombre').textContent = mec.nombre + ' ' + mec.apellido_paterno + ' ' + (mec.apellido_materno || '');
            document.getElementById('modal-mec-correo').textContent = mec.correo;
            document.getElementById('modal-mec-telefono').textContent = mec.telefono || 'No registrado';
            
            const fecha = new Date(mec.created_at);
            document.getElementById('modal-mec-fecha').textContent = fecha.toLocaleDateString('es-MX', { year: 'numeric', month: 'long', day: 'numeric' });
            
            document.getElementById('modal-mecanico').style.display = 'flex';
        }
    </script>
        <!-- Script para la Gráfica de Inventario -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const invCtx = document.getElementById('inventoryChart').getContext('2d');
        
        const nombresPiezas = @json($nombresPiezas ?? []);
        const stockActual = @json($stockActual ?? []);
        const stockMinimo = @json($stockMinimo ?? []);

        new Chart(invCtx, {
            type: 'bar',
            data: {
                labels: nombresPiezas,
                datasets: [
                    {
                        label: 'Stock Actual',
                        data: stockActual,
                        backgroundColor: '#00845A',
                        borderRadius: 4
                    },
                    {
                        label: 'Stock Mínimo (Alerta)',
                        data: stockMinimo,
                        backgroundColor: '#c0392b',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    </script>
@endsection