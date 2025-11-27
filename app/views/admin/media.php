<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="es">
<head>
  <?php include __DIR__ . '/../partials/head_assets.php'; ?>
  <meta charset="utf-8">
  <title>Entrega de multimedia</title>
  <link rel="stylesheet" href="public/css/manego.css">
  <style>
    .media-wrapper{
      max-width: 1100px;
      margin: 2.5rem auto 3rem;
      background:#faf7f3;
      padding:1.8rem 2rem 2.1rem;
      border-radius:18px;
      box-shadow:0 10px 28px rgba(0,0,0,.06);
    }
    .media-header{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:.75rem;
      margin-bottom:1rem;
    }
    .media-header h1{
      margin:0;
      font-size:1.6rem;
    }
    .media-header span{
      font-size:.9rem;
      color:#666;
    }
    .media-table{
      width:100%;
      border-collapse:collapse;
      font-size:.9rem;
    }
    .media-table th,
    .media-table td{
      padding:.55rem .6rem;
      text-align:left;
      border-bottom:1px solid #ddd;
      vertical-align:middle;
    }
    .media-input{
      width:100%;
      max-width:320px;
    }
    .media-actions{
      text-align:right;
    }
    @media (max-width: 768px){
      .media-wrapper{
        margin:1.7rem 1rem 2.4rem;
        padding:1.4rem 1.1rem 1.6rem;
      }
      .media-table{
        font-size:.85rem;
      }
      .media-table th,
      .media-table td{
        padding:.5rem .55rem;
      }
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="media-wrapper">
  <div class="media-header">
    <h1>Entrega de multimedia</h1>
    <?php if (!empty($eventos)): ?>
      <span><?= count($eventos) ?> evento<?= count($eventos) === 1 ? '' : 's' ?> finalizado<?= count($eventos) === 1 ? '' : 's' ?></span>
    <?php endif; ?>
  </div>

  <?php if (!empty($_SESSION['flash_ok'])): ?>
    <p class="flash-ok"><?= htmlspecialchars($_SESSION['flash_ok']); unset($_SESSION['flash_ok']); ?></p>
  <?php endif; ?>
  <?php if (!empty($_SESSION['flash_error'])): ?>
    <p class="flash-error"><?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></p>
  <?php endif; ?>

  <?php if (!empty($eventos)): ?>
    <table class="media-table">
      <thead>
        <tr>
          <th>Cliente</th>
          <th>Contacto</th>
          <th>Paquete</th>
          <th>Fecha</th>
          <th>Hora</th>
          <th>Ubicación</th>
          <th>Enlace de Google Drive</th>
          <th style="text-align:right;">Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($eventos as $e): ?>
        <tr>
          <td><?= htmlspecialchars($e['cliente_nombre'] ?? '') ?></td>
          <td>
            <?= htmlspecialchars($e['cliente_email'] ?? '') ?><br>
            <?= htmlspecialchars($e['cliente_telefono'] ?? '') ?>
          </td>
          <td><?= htmlspecialchars($e['paquete_nombre'] ?? '') ?></td>
          <td><?= htmlspecialchars($e['fecha_evento']) ?></td>
          <td><?= htmlspecialchars($e['hora_evento']) ?></td>
          <td><?= htmlspecialchars($e['ubicacion']) ?></td>
          <td>
            <form method="POST" action="index.php?controller=admin&action=mediaUpdate">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
              <input type="hidden" name="idEvento" value="<?= (int)$e['idEvento'] ?>">
              <input type="url"
                     name="media_link"
                     class="media-input"
                     placeholder="https://drive.google.com/..."
                     value="<?= htmlspecialchars($e['media_link'] ?? '') ?>">
          </td>
          <td class="media-actions">
              <button type="submit" class="btn">Guardar enlace</button>
              <button type="submit"
                      class="btn"
                      name="media_link"
                      value=""
                      onclick="return confirm('¿Deseas eliminar el enlace de este evento?');">
                Eliminar enlace
              </button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No hay eventos finalizados para entregar multimedia todavía.</p>
  <?php endif; ?>
</div>
</body>
</html>
