<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Mis eventos</title>
  <?php include __DIR__ . '/../partials/head_assets.php'; ?>
  <style>
    .events-wrapper{
      max-width: 1100px;
      margin: 2.5rem auto 3rem;
      background:#faf7f3;
      padding:1.8rem 2rem 2.1rem;
      border-radius:18px;
      box-shadow:0 10px 28px rgba(0,0,0,.06);
    }
    .events-header{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:1rem;
      margin-bottom:1.2rem;
    }
    .events-header h1{
      margin:0;
    }
    .events-header span{
      font-size:.9rem;
      color:#666;
    }
    .events-table{
      width:100%;
      border-collapse:collapse;
      font-size:.95rem;
    }
    .events-table thead{
      background:#111;
      color:#fff;
    }
    .events-table th,
    .events-table td{
      padding:.65rem .85rem;
      text-align:left;
    }
    .events-table tbody tr:nth-child(even){
      background:#f0ece6;
    }
    .events-table tbody tr:nth-child(odd){
      background:#fff;
    }
    .events-status{
      display:inline-block;
      padding:.25rem .7rem;
      border-radius:999px;
      font-size:.8rem;
      text-transform:capitalize;
      background:#ffe8c2;
      color:#8a5a00;
    }
    
    .events-status--aceptado{
      background:#d4f6d5;
      color:#216b29;
    }
    .events-status--rechazado{
      background:#fbd3d3;
      color:#992323;
    }
    .events-actions{
      text-align:right;
    }
    .events-actions form{
      display:inline;
    }
    .events-actions .btn{
      font-size:.8rem;
      padding:.25rem .7rem;
    }
@media (max-width: 800px){
      .events-wrapper{
        margin:1.5rem 1rem 2.5rem;
        padding:1.4rem 1.1rem 1.6rem;
      }
      .events-table{
        font-size:.85rem;
      }
      .events-table th,
      .events-table td{
        padding:.5rem .55rem;
      }
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="events-wrapper">
  <div class="events-header">
    <h1>Eventos agendados</h1>
    <?php if (!empty($eventos)): ?>
      <span><?= count($eventos) ?> evento<?= count($eventos) === 1 ? '' : 's' ?> en total</span>
    <?php endif; ?>
  </div>
  <?php if (!empty($_SESSION['flash_ok'])): ?>
    <p class="flash-ok"><?= htmlspecialchars($_SESSION['flash_ok']); unset($_SESSION['flash_ok']); ?></p>
  <?php endif; ?>
  <?php if (!empty($eventos)): ?>
    <table style="width:100%;border-collapse:collapse;">
      <thead><tr>
        <th style="text-align:left;border-bottom:1px solid #ccc;padding:6px;">Paquete</th>
        <th style="text-align:left;border-bottom:1px solid #ccc;padding:6px;">Fecha</th>
        <th style="text-align:left;border-bottom:1px solid #ccc;padding:6px;">Hora</th>
        <th style="text-align:left;border-bottom:1px solid #ccc;padding:6px;">Ubicación</th>
        <th style="text-align:left;border-bottom:1px solid #ccc;padding:6px;">Estado</th>
      </tr></thead>
      <tbody>
      <?php foreach($eventos as $e): ?>
        <tr>
        <?php
          $estadoRaw = trim((string)($e['estado'] ?? ''));
          if ($estadoRaw === '') { $estadoRaw = 'pendiente'; }
          $estadoLower = strtolower($estadoRaw);
          $estadoClass = 'events-status';
          if ($estadoLower === 'aceptado' || $estadoLower === 'aceptada') $estadoClass .= ' events-status--aceptado';
          elseif ($estadoLower === 'rechazado' || $estadoLower === 'rechazada') $estadoClass .= ' events-status--rechazado';
          $estadoLabel = ucfirst($estadoLower);
        ?>
          <td><?= htmlspecialchars($e['paquete_nombre'] ?? '') ?></td>
          <td><?= htmlspecialchars($e['fecha_evento']) ?></td>
          <td><?= htmlspecialchars($e['hora_evento']) ?></td>
          <td><?= htmlspecialchars($e['ubicacion']) ?></td>
          <td><span class="<?= $estadoClass ?>"><?= htmlspecialchars($estadoLabel) ?></span></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?><p>No tienes eventos agendados todavía.</p><?php endif; ?>
</div>
</body>
</html>
