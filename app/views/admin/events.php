<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Gestión de eventos</title>
  <?php include __DIR__ . '/../partials/head_assets.php'; ?>
  <link rel="stylesheet" href="public/css/manego.css">
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
      align-items:flex-start;
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
      font-size:.9rem;
    }
    .events-table thead{
      background:#111;
      color:#fff;
    }
    .events-table th,
    .events-table td{
      padding:.55rem .7rem;
      text-align:left;
      vertical-align:top;
    }
    .events-table tbody tr:nth-child(even){
      background:#f0ece6;
    }
    .events-table tbody tr:nth-child(odd){
      background:#fff;
    }
    .events-status{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      padding:.2rem .6rem;
      border-radius:999px;
      font-size:.8rem;
      text-transform:capitalize;
      background:#ffe8c2;
      color:#8a5a00;
      white-space:nowrap;
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
      display:flex;
      flex-direction:column;
      gap:.3rem;
    }
    .events-actions button{
      font-size:.8rem;
      padding:.25rem .6rem;
    }
    .events-input{
      width:100%;
      box-sizing:border-box;
      font-size:.85rem;
      padding:.2rem .3rem;
    }
    .events-notas{
      min-width:160px;
      max-width:220px;
      min-height:2.2rem;
      resize:vertical;
    }
    @media (max-width: 900px){
      .events-wrapper{
        margin:1.5rem 1rem 2.5rem;
        padding:1.4rem 1.1rem 1.6rem;
      }
      .events-table{
        font-size:.8rem;
      }
      .events-table th,
      .events-table td{
        padding:.45rem .45rem;
      }
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="events-wrapper">
  <div class="events-header">
    <div>
      <h1>Eventos agendados</h1>
      <span>Panel de control para administrar citas de los clientes.</span>
    </div>
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
          <th>Cliente</th>
          <th>Contacto</th>
          <th>Paquete</th>
          <th>Fecha</th>
          <th>Hora</th>
          <th>Ubicación</th>
          <th>Estado</th>
          <th>Notas internas</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($eventos as $e): ?>
        <tr>
          <form method="POST" action="index.php?controller=event&action=adminUpdate">
            <td>
              <?= htmlspecialchars($e['cliente_nombre'] ?? '') ?>
            </td>
            <td>
              <?php if (!empty($e['cliente_email'])): ?>
                <div><?= htmlspecialchars($e['cliente_email']) ?></div>
              <?php endif; ?>
              <?php if (!empty($e['cliente_telefono'])): ?>
                <div><?= htmlspecialchars($e['cliente_telefono']) ?></div>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($e['paquete_nombre'] ?? '') ?></td>
            <td><input type="date" name="fecha_evento" class="events-input" value="<?= htmlspecialchars($e['fecha_evento']) ?>"></td>
            <td><input type="time" name="hora_evento" class="events-input" value="<?= htmlspecialchars(substr($e['hora_evento'],0,5)) ?>"></td>
            <td><input type="text" name="ubicacion" class="events-input" value="<?= htmlspecialchars($e['ubicacion']) ?>"></td>
            <td>
              <?php
                $estado = strtolower((string)($e['estado'] ?? 'pendiente'));
                $estadoClass = 'events-status';
                if ($estado === 'aceptado' || $estado === 'aceptada') $estadoClass .= ' events-status--aceptado';
                elseif ($estado === 'rechazado' || $estado === 'rechazada') $estadoClass .= ' events-status--rechazado';
              ?>
              <select name="estado" class="events-input">
                <option value="pendiente" <?= $estado === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                <option value="aceptado" <?= $estado === 'aceptado' || $estado === 'aceptada' ? 'selected' : '' ?>>Aceptado</option>
                <option value="rechazado" <?= $estado === 'rechazado' || $estado === 'rechazada' ? 'selected' : '' ?>>Rechazado</option>
              </select>
            </td>
            <td>
              <textarea name="notas" class="events-input events-notas"><?= htmlspecialchars($e['notas'] ?? '') ?></textarea>
            </td>
            <td>
                            <div class="events-actions">
                <input type="hidden" name="idEvento" value="<?= (int)$e['idEvento'] ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <button type="submit" class="btn">Guardar cambios</button>
                <button type="submit"
                        class="btn"
                        name="finalizar"
                        value="1"
                        onclick="return confirm('Una vez finalizado el evento, no se podrá revertir esta acción. ¿Deseas continuar?');">
                  Finalizar
                </button>
              </div>
            </td>
          </form>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No hay eventos agendados por el momento.</p>
  <?php endif; ?>
</div>
</body>
</html>
