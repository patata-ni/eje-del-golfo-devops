<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignaciones de Unidades - EJE del Golfo</title>
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
        <p>Reporte de Asignaciones: Unidad y Chofer</p>
        <p>Generado el: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha / Hora</th>
                <th>Ruta</th>
                <th>Unidad (Placas)</th>
                <th>Chofer Asignado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($salidas as $salida)
            <tr>
                <td>{{ \Carbon\Carbon::parse($salida->fecha)->format('d/m/Y') }} {{ $salida->schedule->hora_salida ?? '' }}</td>
                <td>{{ $salida->schedule->route->origen ?? 'N/A' }} a {{ $salida->schedule->route->destino ?? 'N/A' }}</td>
                <td>
                    @if($salida->unit)
                        <strong>{{ $salida->unit->numero_unidad }}</strong> <br><small>({{ $salida->unit->placas }})</small>
                    @else
                        Sin asignar
                    @endif
                </td>
                <td>
                    @if($salida->driver && $salida->driver->personal)
                        {{ $salida->driver->personal->nombre }} {{ $salida->driver->personal->apellido_paterno }}
                    @else
                        Sin chofer
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center;">No hay asignaciones registradas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Este documento es un reporte interno generado automáticamente por el sistema de gestión de EJE del Golfo.
    </div>
</body>
</html>