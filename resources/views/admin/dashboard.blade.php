@extends('layouts.admin')

@section('content')
    <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Resumen de Operaciones</h1>
            <p>Bienvenido de nuevo. Aquí está el estado actual de EJE del Golfo.</p>
        </div>
        <a href="{{ route('admin.pdf') }}" target="_blank" class="btn-primary" style="background-color: #00845A; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
            📄 Descargar Reporte de Taller
        </a>
    </div>

    <!-- TARJETAS DE RESUMEN -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon">🚌</div>
            <div class="stat-details">
                <p>Unidades Activas</p>
                <h2>{{ $unidadesActivas }} <span>/ {{ $totalUnidades }}</span></h2>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🛣️</div>
            <div class="stat-details">
                <p>Salidas Hoy</p>
                <h2>{{ $salidasHoy }}</h2>
            </div>
        </div>
        <div class="stat-card alert">
            <div class="stat-icon">⚠️</div>
            <div class="stat-details">
                <p>Incidencias Abiertas</p>
                <h2>{{ $incidenciasAbiertas }}</h2>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-details">
                <p>Piezas en Stock Bajo</p>
                <h2>{{ $stockBajo }}</h2>
            </div>
        </div>
    </div>

    <!-- SECCIÓN DE GRÁFICAS Y TABLAS (Placeholder) -->
    <div class="dashboard-grid">
        <div class="dashboard-panel">
            <h3>Rendimiento de la semana (Salidas)</h3>
            <div style="position: relative; height: 250px; width: 100%; margin-top: 1rem;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
        <div class="dashboard-panel">
            <h3>Últimas Incidencias</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Unidad</th>
                        <th>Problema</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ultimasIncidencias as $inc)
                    <tr>
                        <td>{{ $inc->unit->numero_unidad ?? 'N/A' }}</td>
                        <td>{{ $inc->tipo }}</td>
                        <td><span class="badge {{ $inc->prioridad == 'critica' ? 'danger' : 'warning' }}">{{ ucfirst($inc->prioridad) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="dashboard-panel">
            <h3>Estado del Inventario (Stock Actual vs Mínimo)</h3>
            <div style="position: relative; height: 250px; width: 100%; margin-top: 1rem;">
                <canvas id="inventoryChart"></canvas>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        const labels = @json($dias);
        const dataSalidas = @json($cantidadSalidas);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Salidas Programadas',
                    data: dataSalidas,
                    borderColor: '#00845A',
                    backgroundColor: 'rgba(0, 132, 90, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#073D32',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
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