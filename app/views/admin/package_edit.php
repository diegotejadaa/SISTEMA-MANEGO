<!-- Campos según la tabla paquetes: idPack, nombrePack, descripcion, precio, detalles -->
<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Editar paquete</title>
  <?php include __DIR__ . '/../partials/head_assets.php'; ?>
  <style>
    body.pkg-edit-page{
      background:#f4f1ec;
    }
    body.pkg-edit-page .container{
      max-width:900px;
      margin:2.5rem auto 3rem;
      background:#faf7f3;
      padding:1.8rem 2rem 2.1rem;
      border-radius:18px;
      box-shadow:0 10px 28px rgba(0,0,0,.06);
    }
    body.pkg-edit-page h1{
      margin:0 0 1.4rem;
      font-size:1.7rem;
    }
    body.pkg-edit-page form{
      max-width:520px;
      display:flex;
      flex-direction:column;
      gap:.75rem;
    }
    body.pkg-edit-page label{
      font-size:.9rem;
      font-weight:600;
      margin-bottom:.2rem;
    }
    body.pkg-edit-page input[type='text'],
    body.pkg-edit-page input[type='number'],
    body.pkg-edit-page textarea,
    body.pkg-edit-page input[type='file']{
      width:100%;
      border-radius:10px;
      border:1px solid #d4d4d4;
      padding:.5rem .7rem;
      font-size:.9rem;
    }
    body.pkg-edit-page textarea{
      min-height:80px;
      resize:vertical;
    }
    body.pkg-edit-page .btn{
      display:inline-block;
      padding:.5rem 1.2rem;
      border-radius:999px;
      border:none;
      background:#111;
      color:#fff;
      font-size:.9rem;
      cursor:pointer;
      margin-top:.4rem;
    }
    body.pkg-edit-page .btn:hover{
      opacity:.9;
    }
    body.pkg-edit-page .flash-ok,
    body.pkg-edit-page .flash-error{
      padding:.5rem .8rem;
      border-radius:10px;
      font-size:.85rem;
      margin-bottom:.8rem;
      display:inline-block;
    }
    body.pkg-edit-page .flash-ok{
      background:#e0f7ec;
      color:#166534;
    }
    body.pkg-edit-page .flash-error{
      background:#fee2e2;
      color:#991b1b;
    }
  </style>
</head>
<body class="pkg-edit-page">
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="container">
  <h1>Editar paquete</h1>

  <?php if (!empty($ok)): ?><p class="flash-ok"><?= htmlspecialchars($ok) ?></p><?php endif; ?>
  <?php if (!empty($error)): ?><p class="flash-error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

  <form enctype="multipart/form-data" method="POST" action="index.php?controller=adminpkg&action=edit&id=<?= (int)$paquete['idPack'] ?>">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
    <label>Nombre*</label><br>
    <input type="text" name="nombrePack" value="<?= htmlspecialchars($paquete['nombrePack']) ?>" required><br><br>

    <label>Descripción*</label><br>
    <textarea name="descripcion" required><?= htmlspecialchars($paquete['descripcion']) ?></textarea><br><br>

    <label>Precio (MXN)</label><br>
    <input type="number" step="0.01" name="precio" value="<?= htmlspecialchars($paquete['precio']) ?>"><br><br>

    <label>Detalles adicionales</label><br>
    <textarea name="detalles"><?= htmlspecialchars($paquete['detalles']) ?></textarea><br><br>

    <button type="submit" class="btn">Guardar cambios</button>
  
    <label>Imagen principal (opcional)</label><br>
    <input type="file" name="imagen" accept="image/*"><br><br>
    </form>
</div>
</body>
</html>
