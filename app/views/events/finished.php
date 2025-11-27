<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Eventos finalizados</title>
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
      gap:.75rem;
      margin-bottom:1rem;
    }
    .events-header h1{
      margin:0;
      font-size:1.6rem;
    }
    .events-header span{
      font-size:.9rem;
      color:#666;
    }
    .events-table{
      width:100%;
      border-collapse:collapse;
      font-size:.9rem;
    }
    .events-table th,
    .events-table td{
      padding:.55rem .6rem;
      text-align:left;
      border-bottom:1px solid #ddd;
    }
    .events-actions{
      text-align:right;
    }
    .media-alert-backdrop{
      position:fixed;
      inset:0;
      background:rgba(0,0,0,0.5);
      display:flex;
      align-items:center;
      justify-content:center;
      z-index:999;
    }
    .media-alert-box{
      background:#fff;
      padding:1.5rem 1.75rem;
      border-radius:16px;
      max-width:380px;
      text-align:center;
      box-shadow:0 10px 30px rgba(0,0,0,0.18);
    }
    .media-alert-box p{
      margin:0 0 1rem 0;
    }
    .media-alert-close{
      margin-top:.25rem;
    }

    @media (max-width: 768px){
      .events-wrapper{
        margin:1.7rem 1rem 2.4rem;
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
    <h1>Eventos finalizados</h1>
    <?php if (!empty($eventos)): ?>
      <span><?= count($eventos) ?> evento<?= count($eventos) === 1 ? '' : 's' ?> en total</span>
    <?php endif; ?>
  </div>

  <?php if (!empty($_SESSION['flash_ok'])): ?>
    <p class="flash-ok"><?= htmlspecialchars($_SESSION['flash_ok']); unset($_SESSION['flash_ok']); ?></p>
  <?php endif; ?>
  <?php if (!empty($_SESSION['flash_error'])): ?>
    <p class="flash-error"><?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></p>
  <?php endif; ?>

  <?php if (!empty($eventos)): ?>
    <table class="events-table">
      <thead>
        <tr>
          <th>Paquete</th>
          <th>Fecha</th>
          <th>Hora</th>
          <th>Ubicación</th>
          <th style="text-align:right;">Acción</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($eventos as $e): ?>
        <tr>
          <td><?= htmlspecialchars($e['paquete_nombre'] ?? '') ?></td>
          <td><?= htmlspecialchars($e['fecha_evento']) ?></td>
          <td><?= htmlspecialchars($e['hora_evento']) ?></td>
          <td><?= htmlspecialchars($e['ubicacion']) ?></td>
          <td class="events-actions">
            <?php
              $link = isset($e['media_link']) ? trim((string)$e['media_link']) : '';
            ?>
            <button type="button"
                    class="btn"
                    onclick="return handleMediaClick('<?= htmlspecialchars($link, ENT_QUOTES, 'UTF-8') ?>');">
              Material multimedia
            </button>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No tienes eventos finalizados todavía.</p>
  <?php endif; ?>
</div>

<script>
  function handleMediaClick(link){
    if (!link){
      // Mensaje cuando aún no hay material
      var existing = document.getElementById('media-alert');
      if (existing){
        existing.remove();
      }
      var backdrop = document.createElement('div');
      backdrop.id = 'media-alert';
      backdrop.className = 'media-alert-backdrop';
      backdrop.innerHTML = '<div class="media-alert-box">'
        + '<p>El material multimedia aún está en proceso.<br>La entrega estimada es de 1 a 2 semanas después de la fecha del evento.</p>'
        + '<button type="button" class="btn media-alert-close" onclick="document.getElementById(\'media-alert\').remove();">Cerrar</button>'
        + '</div>';
      document.body.appendChild(backdrop);
      return false;
    }
    // Abrir enlace en nueva pestaña cuando existe
    window.open(link, '_blank');
    return false;
  }
</script>
</body>
</html>
