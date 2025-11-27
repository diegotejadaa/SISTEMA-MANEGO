<!-- Campos según la tabla paquetes: idPack, nombrePack, descripcion, precio, detalles -->
<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Crear paquete</title>
  <?php include __DIR__ . '/../partials/head_assets.php'; ?>
  <style>
    .pkg-create-wrapper{
      max-width:760px;
      margin:24px auto 40px;
    }
    .pkg-create-title{
      margin-bottom:12px;
    }
    .pkg-create-subtitle{
      margin:0 0 18px;
      color:#6b7280;
      font-size:.95rem;
    }
    .pkg-create-card{
      background:#111;
      color:#f9fafb;
      border-radius:18px;
      padding:22px 24px 24px;
      box-shadow:0 14px 35px rgba(0,0,0,.35);
    }
    .pkg-create-grid{
      display:grid;
      grid-template-columns:1.2fr .8fr;
      gap:18px 20px;
    }
    .pkg-create-field{
      display:flex;
      flex-direction:column;
      gap:4px;
      margin-bottom:10px;
    }
    .pkg-create-field label{
      font-weight:600;
      font-size:.9rem;
    }
    .pkg-create-field input[type="text"],
    .pkg-create-field input[type="number"],
    .pkg-create-field textarea{
      border-radius:10px;
      border:1px solid #374151;
      padding:8px 10px;
      font-size:.9rem;
      background:#020617;
      color:#f9fafb;
    }
    .pkg-create-field textarea{
      min-height:88px;
      resize:vertical;
    }
    .pkg-create-aside{
      display:flex;
      flex-direction:column;
      gap:12px;
    }
    .pkg-create-aside small{
      color:#9ca3af;
      font-size:.8rem;
    }
    .pkg-create-actions{
      margin-top:12px;
      display:flex;
      align-items:center;
      gap:14px;
      flex-wrap:wrap;
    }
    .pkg-create-actions .btn{
      padding-inline:22px;
    }
    @media (max-width: 720px){
      .pkg-create-card{
        padding:18px 16px 20px;
      }
      .pkg-create-grid{
        grid-template-columns:1fr;
      }
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="container">
  <?php include __DIR__ . '/../partials/back.php'; ?>

  <div class="pkg-create-wrapper">
    <h1 class="pkg-create-title">Crear paquete</h1>
    <p class="pkg-create-subtitle">Define los detalles principales del paquete que estará disponible para tus clientes.</p>

    <?php if (!empty($ok)): ?><p class="flash-ok"><?= htmlspecialchars($ok) ?></p><?php endif; ?>
    <?php if (!empty($error)): ?><p class="flash-error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <div class="pkg-create-card">
      <form enctype="multipart/form-data" method="POST" action="index.php?controller=adminpkg&action=create">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <div class="pkg-create-grid">
          <div>
            <div class="pkg-create-field">
              <label>Nombre*</label>
              <input type="text" name="nombrePack" required>
            </div>

            <div class="pkg-create-field">
              <label>Descripción*</label>
              <textarea name="descripcion" required></textarea>
            </div>

            <div class="pkg-create-field">
              <label>Precio (MXN)</label>
              <input type="number" step="0.01" name="precio" value="0">
            </div>
          </div>

          <div class="pkg-create-aside">
            <div class="pkg-create-field">
              <label>Detalles adicionales</label>
              <textarea name="detalles"></textarea>
            </div>

            <div class="pkg-create-field">
              <label>Imagen principal (opcional)</label>
              <input type="file" name="imagen" accept="image/*">
              <small>Formatos recomendados: JPG o PNG. Tamaño máximo 2&nbsp;MB.</small>
            </div>
          </div>
        </div>

        <div class="pkg-create-actions">
          <button type="submit" class="btn">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
