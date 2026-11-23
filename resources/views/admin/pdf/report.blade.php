<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Mantenimientos - EJE del Golfo</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #00845A; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #073D32; margin: 0; }
        .header p { margin: 5px 0 0; color: #666; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px; }
        th { background-color: #EAF5F0; color: #073D32; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #999; }
        .btn-print { background: #00845A; color: white; padding: 10px 20px; border: none; cursor: pointer; font-weight: bold; border-radius: 5px; margin-bottom: 20px; }
        @media print {
            .btn-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">🖨️ Imprimir / Guardar como PDF</button>

    <div class="header">
        <h1>Transportes EJE del Golfo</h1>
        <p>Reporte General de Mantenimientos Finalizados</p>
        <p>Generado el: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Unidad</th>
                <th>Tipo</th>
                <th>Diagnóstico</th>
                <th>Trabajo Realizado</th>
                <th>Fecha Fin</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mantenimientos as $m)
            <tr>
                <td>{{ $m->unit->numero_unidad ?? 'N/A' }}</td>
                <td>{{ ucfirst($m->tipo) }}</td>
                <td>{{ $m->diagnostico }}</td>
                <td>{{ $m->trabajo_realizado ?? 'Sin observaciones' }}</td>
                <td>{{ \Carbon\Carbon::parse($m->fecha_fin)->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">No hay mantenimientos finalizados registrados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Este documento es un reporte interno generado automáticamente por el sistema de gestión de EJE del Golfo.
    </div>
</body>
</html>