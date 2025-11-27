<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Paquetes</title>
  <?php include __DIR__ . '/../partials/head_assets.php'; ?>
  <style>
    /* Controles */
    .toolbar { display:flex; justify-content:space-between; align-items:center; gap:12px; margin:10px 0 18px; }
    .toolbar .title { margin:0; }
    .toolbar .actions a.btn { white-space:nowrap; }

    /* Tabla estilizada */
    .table-wrap { overflow:auto; border-radius:14px; box-shadow:0 6px 20px rgba(0,0,0,.12); }
    table.pkg { width:100%; border-collapse:separate; border-spacing:0; background:#111; color:#eee; }
    .pkg thead th {
      text-align:left; font-weight:700; padding:14px 16px; font-size:14px; letter-spacing:.02em;
      position:sticky; top:0; background:#0f0f0f; border-bottom:1px solid #2a2a2a;
    }
    .pkg tbody td { padding:14px 16px; border-bottom:1px solid #1f1f1f; vertical-align:top; }
    .pkg tbody tr:hover { background:#151515; }

    /* Celdas específicas */
    .col-id { width:90px; color:#9aa0a6; }
    .col-price { width:140px; font-weight:600; }
    .col-actions { width:220px; }

    /* Botones */
    .btn.btn-danger { background:#b11; border-color:#b11; color:#fff; }
    .badge { display:inline-block; padding:4px 8px; border-radius:999px; background:#1f2937; color:#d1d5db; font-size:12px; }

    /* Descripción corta */
    .desc { color:#cfcfcf; opacity:.9; max-width:540px; }
    .desc small { color:#9aa0a6; }

    /* Responsive: tarjetas en móvil */
    @media (max-width: 720px){
      .table-wrap { box-shadow:none; }
      table.pkg, .pkg thead, .pkg tbody, .pkg th, .pkg td, .pkg tr { display:block; }
      .pkg thead { display:none; }
      .pkg tr { margin:0 0 14px; background:#111; border:1px solid #1f1f1f; border-radius:12px; overflow:hidden; }
      .pkg td { border:none; border-bottom:1px solid #1f1f1f; }
      .pkg td:last-child { border-bottom:none; }
      .pkg td[data-label]::before {
        content: attr(data-label);
        display:block; font-size:12px; color:#9aa0a6; margin-bottom:6px; text-transform:uppercase; letter-spacing:.05em;
      }
      .col-actions { width:auto; }
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="container">

  <div class="toolbar">
    <h1 class="title">Paquetes fotográficos</h1>
    <div class="actions">
      <a class="btn" href="index.php?controller=adminpkg&action=create">+ Crear paquete</a>
    </div>
  </div>

  <?php if (!empty($_SESSION['flash_ok'])): ?>
    <p class="flash-ok"><?= htmlspecialchars($_SESSION['flash_ok']); unset($_SESSION['flash_ok']); ?></p>
  <?php endif; ?>
  <?php if (!empty($_SESSION['flash_error'])): ?>
    <p class="flash-error"><?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></p>
  <?php endif; ?>

  <?php if (empty($paquetes)): ?>
    <p class="badge">No hay paquetes creados aún.</p>
  <?php else: ?>
    <div class="table-wrap">
      <table class="pkg">
        <thead>
          <tr>
            <th class="col-id">ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th class="col-price">Precio</th>
            <th class="col-actions">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($paquetes as $p): ?>
            <?php
              $id      = (int)($p['idPack'] ?? 0);
              $nombre  = $p['nombrePack'] ?? '';
              $desc    = $p['descripcion'] ?? '';
              $precio  = isset($p['precio']) ? (float)$p['precio'] : 0;
              $det     = $p['detalles'] ?? '';
              // recorte bonito de descripción / detalles
              $snippet = trim($desc) !== '' ? $desc : $det;
              if (mb_strlen($snippet) > 120) { $snippet = mb_substr($snippet, 0, 120) . '…'; }
            ?>
            <tr>
              <td class="col-id" data-label="ID">#<?= $id ?></td>
              <td data-label="Nombre">
                <strong><?= htmlspecialchars($nombre) ?></strong>
              </td>
              <td data-label="Descripción" class="desc">
                <?= htmlspecialchars($snippet) ?>
              </td>
              <td class="col-price" data-label="Precio">$<?= number_format($precio, 2) ?></td>
              <td class="col-actions" data-label="Acciones">
                <a class="btn" href="index.php?controller=adminpkg&action=edit&id=<?= $id ?>">Editar</a>
                <form method="POST" action="index.php?controller=adminpkg&action=delete"
                      style="display:inline" onsubmit="return confirm('¿Eliminar paquete?');">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                  <input type="hidden" name="id" value="<?= $id ?>">
                  <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

</div>
</body>
</html>
