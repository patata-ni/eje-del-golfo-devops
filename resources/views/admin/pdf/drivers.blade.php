<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Directorio de Choferes - EJE del Golfo</title>
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
        .badge { padding: 3px 8px; border-radius: 12px; font-size: 10px; font-weight: bold; }
        .bg-success { background: #d4edda; color: #155724; }
        .bg-warning { background: #fff3cd; color: #856404; }
        .bg-danger { background: #f8d7da; color: #721c24; }
        .bg-secondary { background: #e2e3e5; color: #383d41; }
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
        <p>Directorio Oficial de Choferes</p>
        <p>Generado el: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre Completo</th>
                <th>Correo Electrónico</th>
                <th>No. Empleado</th>
                <th>Estado Actual</th>
                <th>Última Actualización</th>
            </tr>
        </thead>
        <tbody>
            @foreach($choferes as $ch)
            <tr>
                <td>{{ $ch->personal->nombre ?? '' }} {{ $ch->personal->apellido_paterno ?? '' }}</td>
                <td>{{ $ch->personal->correo ?? 'N/A' }}</td>
                <td>{{ $ch->numero_empleado }}</td>
                <td>
                    @php
                        $badgeClass = match($ch->estado) {
                            'disponible' => 'bg-success',
                            'en_ruta' => 'bg-warning',
                            'de_baja', 'enfermo' => 'bg-danger',
                            default => 'bg-secondary'
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $ch->estado)) }}</span>
                </td>
                <td>{{ $ch->updated_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Este documento es un reporte interno generado automáticamente por el sistema de gestión de EJE del Golfo.
    </div>
</body>
</html>