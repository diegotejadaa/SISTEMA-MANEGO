<?php
// $events, $startDate, $endDate vienen del controlador
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eventos aceptados</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        body {
            margin: 0;
            padding: 16px;
            background: #ffffff;
            color: #111827;
        }
        h1 {
            font-size: 20px;
            margin-bottom: 4px;
        }
        .subtitle {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 12px;
        }
        .period {
            font-size: 12px;
            margin-bottom: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background: #f3f4f6;
            font-weight: 600;
        }
        .no-data {
            margin-top: 16px;
            font-size: 12px;
            color: #6b7280;
        }
        @page {
            size: letter;
            margin: 10mm;
        }
        @media print {
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <h1>Eventos aceptados</h1>
    <div class="subtitle">
        Tabla de eventos aceptados dentro del periodo seleccionado.
    </div>
    <div class="period">
        Periodo: 
        <strong><?php echo htmlspecialchars($startDate); ?></strong>
        &nbsp;al&nbsp;
        <strong><?php echo htmlspecialchars($endDate); ?></strong>
    </div>

    <?php if (!empty($events)): ?>
        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Paquete</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Ubicación</th>
                    <th>Notas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $ev): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ev['cliente_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($ev['paquete_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($ev['fecha_evento']); ?></td>
                        <td><?php echo htmlspecialchars(substr($ev['hora_evento'], 0, 5)); ?></td>
                        <td><?php echo htmlspecialchars($ev['ubicacion']); ?></td>
                        <td><?php echo htmlspecialchars($ev['notas']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-data">
            No se encontraron eventos aceptados en el periodo indicado.
        </div>
    <?php endif; ?>
</body>
</html>