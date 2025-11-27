<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Reportes estadísticos</title>
  <?php include __DIR__ . '/../partials/head_assets.php'; ?>
  <link rel="stylesheet" href="public/css/manego.css">
  <style>
    .reports-wrapper{
      max-width: 1100px;
      margin: 2.5rem auto 3rem;
      background:#faf7f3;
      padding:1.8rem 2rem 2.1rem;
      border-radius:18px;
      box-shadow:0 10px 28px rgba(0,0,0,.06);
    }
    .reports-header{
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      gap:1rem;
      margin-bottom:1.4rem;
    }
    .reports-header h1{
      margin:0;
    }
    .reports-header span{
      font-size:.9rem;
      color:#666;
    }
    .reports-filter{
      display:flex;
      flex-wrap:wrap;
      gap:.6rem .9rem;
      align-items:flex-end;
      font-size:.9rem;
    }
    .reports-filter label{
      display:flex;
      flex-direction:column;
      gap:.25rem;
    }
    .reports-filter input[type="date"]{
      padding:.4rem .55rem;
      border-radius:8px;
      border:1px solid #ccc;
      font-size:.9rem;
    }
    .reports-filter button{
      padding:.45rem .9rem;
      border-radius:999px;
      border:none;
      background:#111;
      color:#fff;
      cursor:pointer;
      font-size:.9rem;
    }
    .reports-filter button:hover{
      opacity:.9;
    }
    .reports-grid{
      display:grid;
      grid-template-columns:minmax(0,1.3fr) minmax(0,1.1fr);
      gap:1.2rem;
      margin-top:1.5rem;
    }
    @media (max-width: 960px){
      .reports-grid{
        grid-template-columns:1fr;
      }
    }
    .report-card{
      background:#fff;
      border-radius:14px;
      padding:1rem 1.1rem 1.2rem;
      border:1px solid #eee;
      box-shadow:0 4px 12px rgba(0,0,0,.03);
    }
    .report-card h2{
      margin:.1rem 0 .6rem;
      font-size:1.05rem;
    }
    .report-card p{
      margin:0 0 .6rem;
      font-size:.85rem;
      color:#666;
    }
    .report-card canvas{
      width:100%;
      max-height:280px;
    }
  
    .reports-actions{
      display:flex;
      flex-wrap:wrap;
      gap:.6rem;
      align-items:center;
      margin-top:.4rem;
    }
    .reports-filter .btn-change-period{
      padding:.45rem .9rem;
      border-radius:999px;
      border:none;
      background:#111;
      color:#fff;
      cursor:pointer;
      font-size:.9rem;
    }
    .reports-filter .btn-change-period:hover{
      opacity:.9;
    }
    .reports-filter .btn-pdf{
      padding:.45rem .9rem;
      border-radius:999px;
      border:none;
      background:#b12704;
      color:#fff;
      cursor:pointer;
      font-size:.9rem;
    }
    .reports-filter .btn-pdf:hover{
      opacity:.9;
    }
    .report-section-label{
      font-size:.8rem;
      color:#777;
      margin-top:.7rem;
      margin-bottom:.2rem;
    }
    .report-table{
      width:100%;
      border-collapse:collapse;
      margin-top:.2rem;
      font-size:.85rem;
      background:#fff;
    }
    .report-table th,
    .report-table td{
      padding:.35rem .45rem;
      border-bottom:1px solid #e0e0e0;
      text-align:left;
    }
    .report-table th{
      background:#f1ece4;
      font-weight:600;
    }
    .report-empty{
      font-size:.85rem;
      color:#999;
      margin-top:.6rem;
    }
    @media print{
      body{background:#fff !important;}
      header, .header, .nav, .reports-filter .btn-change-period, .reports-filter .btn-pdf, .no-print{display:none !important;}
      .reports-wrapper{
        max-width:100%;
        margin:0;
        border-radius:0;
        box-shadow:none;
        padding:0;
      }
      .reports-grid{
        grid-template-columns:1fr;
      }
      .report-card{
        box-shadow:none;
        margin-bottom:1rem;
        page-break-inside:avoid;
      }
      @page{
        size:letter portrait;
        margin:10mm;
      }
    }

  </style>
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="reports-wrapper">
  <div class="reports-header">
    <div>
      <h1>Reportes estadísticos</h1>
      <span>Visualiza el comportamiento de tus eventos agendados en un periodo de tiempo.</span>
    </div>
    <form class="reports-filter" method="get" action="index.php">
      <input type="hidden" name="controller" value="admin">
      <input type="hidden" name="action" value="reports">
      <label>
        Desde
        <input type="date" name="start" value="<?php echo htmlspecialchars($startDate ?? ''); ?>">
      </label>
      <label>
        Hasta
        <input type="date" name="end" value="<?php echo htmlspecialchars($endDate ?? ''); ?>">
      </label>
      <button type="submit" class="btn-change-period">Actualizar</button>
      <button type="button" id="btnDownloadPdf" class="btn-pdf">Descargar PDF</button>
      <a
        href="index.php?controller=admin&action=acceptedEventsPdf&start=<?php echo urlencode($startDate ?? ''); ?>&end=<?php echo urlencode($endDate ?? ''); ?>"
        class="btn-pdf"
        target="_blank"
      >
        Descargar tabla de eventos aceptados
      </a>
    </form>
  </div>

  <div class="reports-grid">
    <section class="report-card">
      <h2>Gráfica 1. Eventos por mes</h2>
      <p>Número total de citas registradas por mes dentro del periodo seleccionado.</p>
      <canvas id="chartMonthly"></canvas>
      <?php if (!empty($statsByMonth)): ?>
      <div class="report-section-label">Tabla 1. Resumen mensual de eventos</div>
      <table class="report-table">
        <thead>
          <tr>
            <th>Mes</th>
            <th>Total de eventos</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($statsByMonth as $row): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['label']); ?></td>
            <td><?php echo (int)$row['total']; ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
      <p class="report-empty">No hay eventos registrados en este periodo.</p>
      <?php endif; ?>
    </section>

    <section class="report-card">
      <h2>Gráfica 2. Eventos por tipo de servicio</h2>
      <p>Cantidad de citas por paquete de servicio para identificar los más solicitados.</p>
      <canvas id="chartServices"></canvas>
      <?php if (!empty($statsByService)): ?>
      <div class="report-section-label">Tabla 2. Eventos por tipo de servicio</div>
      <table class="report-table">
        <thead>
          <tr>
            <th>Paquete</th>
            <th>Total de eventos</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($statsByService as $row): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['paquete'] ?: 'Sin paquete'); ?></td>
            <td><?php echo (int)$row['total']; ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
      <p class="report-empty">No hay eventos registrados en este periodo.</p>
      <?php endif; ?>
    </section>

    <section class="report-card" style="grid-column:1 / -1;">
      <h2>Gráfica 3. Estado de los eventos</h2>
      <p>Proporción de eventos finalizados y próximos dentro del periodo seleccionado.</p>
      <canvas id="chartStatus"></canvas>
      <?php
        $totalFinalizados = (int)($statsByStatus['finalizados'] ?? 0);
        $totalProximos    = (int)($statsByStatus['proximos'] ?? 0);
        $totalEventos     = $totalFinalizados + $totalProximos;
      ?>
      <?php if ($totalEventos > 0): ?>
      <div class="report-section-label">Tabla 3. Estado de los eventos</div>
      <table class="report-table">
        <thead>
          <tr>
            <th>Estado</th>
            <th>Cantidad</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Finalizados</td>
            <td><?php echo $totalFinalizados; ?></td>
          </tr>
          <tr>
            <td>Próximos</td>
            <td><?php echo $totalProximos; ?></td>
          </tr>
        </tbody>
      </table>
      <?php else: ?>
      <p class="report-empty">No hay eventos registrados en este periodo.</p>
      <?php endif; ?>
    </section>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  (function(){
    const monthlyLabels = <?php echo json_encode(array_map(function($r){ return $r['label']; }, $statsByMonth ?? [])); ?>;
    const monthlyValues = <?php echo json_encode(array_map(function($r){ return (int)$r['total']; }, $statsByMonth ?? [])); ?>;

    const serviceLabels = <?php echo json_encode(array_map(function($r){ return $r['paquete'] ?: 'Sin paquete'; }, $statsByService ?? [])); ?>;
    const serviceValues = <?php echo json_encode(array_map(function($r){ return (int)$r['total']; }, $statsByService ?? [])); ?>;

    const statusData = <?php echo json_encode($statsByStatus ?? ['finalizados'=>0,'proximos'=>0]); ?>;

    
    // Botón para descargar/imprimir en PDF
    var btnPdf = document.getElementById('btnDownloadPdf');
    if (btnPdf) {
      btnPdf.addEventListener('click', function(){
        window.print();
      });
    }

    // Chart: eventos por mes
    const ctxMonth = document.getElementById('chartMonthly').getContext('2d');
    new Chart(ctxMonth, {
      type: 'bar',
      data: {
        labels: monthlyLabels,
        datasets: [{
          label: 'Eventos agendados',
          data: monthlyValues,
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              precision: 0
            }
          }
        }
      }
    });

    // Chart: eventos por tipo de servicio
    const ctxService = document.getElementById('chartServices').getContext('2d');
    new Chart(ctxService, {
      type: 'bar',
      data: {
        labels: serviceLabels,
        datasets: [{
          label: 'Eventos por paquete',
          data: serviceValues,
          borderWidth: 1
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        plugins: {
          legend: { display: false }
        },
        scales: {
          x: {
            beginAtZero: true,
            ticks: {
              precision: 0
            }
          }
        }
      }
    });

    // Chart: estado de los eventos (pastel)
    const ctxStatus = document.getElementById('chartStatus').getContext('2d');
    new Chart(ctxStatus, {
      type: 'pie',
      data: {
        labels: ['Finalizados', 'Próximos'],
        datasets: [{
          data: [statusData.finalizados || 0, statusData.proximos || 0]
        }]
      },
      options: {
        responsive: true
      }
    });
  })();
</script>
</body>
</html>
