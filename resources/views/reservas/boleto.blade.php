<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleto - EJE del Golfo</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; display: flex; justify-content: center; }
        .boleto { width: 600px; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { background: #073D32; color: white; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; letter-spacing: 1px; }
        .body { padding: 30px; }
        .row { display: flex; justify-content: space-between; border-bottom: 1px dashed #ddd; padding: 10px 0; }
        .row:last-child { border-bottom: none; }
        .label { color: #666; font-size: 12px; text-transform: uppercase; }
        .value { font-weight: bold; color: #333; font-size: 16px; }
        .total { background: #EAF5F0; padding: 20px; text-align: right; border-top: 2px solid #00845A; }
        .total h2 { margin: 0; color: #00845A; }
        .footer { background: #073D32; color: white; padding: 15px; text-align: center; font-size: 12px; }
        .btn-print { display: block; width: 100%; padding: 15px; background: #00845A; color: white; border: none; font-size: 16px; font-weight: bold; cursor: pointer; margin-top: 20px; }
        @media print {
            .btn-print { display: none; }
            body { background: white; padding: 0; }
            .boleto { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="boleto">
        <div class="header">
            <h1>EJE DEL GOLFO</h1>
            <p style="margin: 5px 0 0; font-size: 14px;">Boleto de Pasajero</p>
        </div>
        <div class="body">
            <div class="row">
                <div>
                    <div class="label">Pasajero</div>
                    <div class="value">{{ $pasajero->name }}</div>
                </div>
                <div style="text-align: right;">
                    <div class="label">Fecha de Viaje</div>
                    <div class="value">{{ \Carbon\Carbon::parse($salida->fecha)->format('d/m/Y') }}</div>
                </div>
            </div>
            <div class="row">
                <div>
                    <div class="label">Origen</div>
                    <div class="value">{{ $ruta->origen }}</div>
                </div>
                <div style="text-align: right;">
                    <div class="label">Destino</div>
                    <div class="value">{{ $ruta->destino }}</div>
                </div>
            </div>
            <div class="row">
                <div>
                    <div class="label">Hora de Salida</div>
                    <div class="value">{{ \Carbon\Carbon::parse($salida->schedule->hora_salida)->format('H:i') }} hrs</div>
                </div>
                <div style="text-align: right;">
                    <div class="label">Unidad</div>
                    <div class="value">{{ $salida->unit->numero_unidad ?? 'Por asignar' }}</div>
                </div>
            </div>
            <div class="total">
                <div class="label">Total Pagado</div>
                <h2>${{ $ruta->precio }}</h2>
            </div>
        </div>
        <div class="footer">
            Presenta este boleto al abordar. Llega 30 minutos antes de tu salida.<br>
            ¡Gracias por viajar con nosotros!
        </div>
    </div>
    
    <script>
        // Abre automáticamente la ventana de impresión al cargar el boleto
        window.onload = function() { window.print(); }
    </script>
</body>
</html>