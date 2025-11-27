<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="es">
<head>
  <?php include __DIR__ . '/partials/head_assets.php'; ?>
  <meta charset="utf-8">
  <title><?= isset($view_title)?htmlspecialchars($view_title):'Mi cuenta' ?></title>
  <link rel="stylesheet" href="public/css/manego.css">
  <style>
    .account-page{
      max-width: 1080px;
      margin: 2.5rem auto 3rem;
      padding: 0 1.5rem;
    }
    .account-grid{
      display: grid;
      grid-template-columns: minmax(260px, 0.9fr) minmax(320px, 1.1fr);
      gap: 1.8rem;
      align-items: flex-start;
    }
    .account-card{
      background:#faf7f3;
      border-radius:18px;
      padding:1.6rem 1.9rem;
      box-shadow:0 10px 28px rgba(0,0,0,.06);
    }
    .account-header{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:1rem;
      margin-bottom:1rem;
    }
    .account-title{
      display:flex;
      align-items:center;
      gap:.75rem;
    }
    .account-avatar{
      width:42px;
      height:42px;
      border-radius:50%;
      background:#111;
      display:flex;
      align-items:center;
      justify-content:center;
      color:#fff;
      font-size:1.2rem;
      font-weight:600;
    }
    .account-name{
      margin:0;
      font-size:1.4rem;
    }
    .account-role{
      font-size:.9rem;
      font-weight:600;
    }
    .account-role--admin{
      color:#0a0;
    }
    .account-role--photographer{
      color:#c01818;
    }
    .account-meta{
      list-style:none;
      padding:0;
      margin:0;
      font-size:.92rem;
    }
    .account-meta li{
      margin-bottom:.3rem;
    }
    .account-actions{
      display:flex;
      flex-direction:column;
      align-items:flex-end;
      gap:.35rem;
    }
    .account-actions .btn{
      border-radius:999px;
      font-size:.9rem;
      padding:.4rem 1rem;
    }
    .account-section-title{
      margin-top:0;
      margin-bottom:1rem;
    }
    .account-form-group{
      display:flex;
      flex-direction:column;
      gap:.25rem;
      margin-bottom:.7rem;
    }
    .account-form-group label{
      font-weight:600;
      font-size:.9rem;
    }
    .account-delete{
      margin-top:1.5rem;
      padding-top:1rem;
      border-top:1px solid #e3ded5;
    }
    @media (max-width: 900px){
      .account-grid{
        grid-template-columns:1fr;
      }
      .account-page{
        margin:2rem auto 2.6rem;
      }
      .account-card{
        padding:1.4rem 1.4rem;
      }
      .account-actions{
        align-items:flex-start;
      }
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/partials/header.php'; ?>

<main class="account-page">
  <h1 style="margin:0 0 1.3rem;">Mi cuenta</h1>

  <?php if (!empty($_SESSION['flash_ok'])): ?>
    <p class="flash-ok"><?= htmlspecialchars($_SESSION['flash_ok']); unset($_SESSION['flash_ok']); ?></p>
  <?php endif; ?>
  <?php if (!empty($_SESSION['flash_error'])): ?>
    <p class="flash-error"><?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></p>
  <?php endif; ?>

  <?php if (!empty($user)): ?>
    <div class="account-grid">
      <!-- Panel de datos actuales -->
      <section class="account-card">
        <div class="account-header">
          <div class="account-title">
            <div class="account-avatar">
              <?= strtoupper(substr($user['nombre'] ?? 'U',0,1)) ?>
            </div>
            <div>
              <p class="account-name">
                <?= htmlspecialchars($user['nombre']) ?>
                <?= htmlspecialchars($user['apellidoPaterno']) ?>
              </p>
              <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <span class="account-role account-role--admin">Administrador</span>
              <?php elseif (!empty($_SESSION['role']) && $_SESSION['role'] === 'fotografo'): ?>
                <span class="account-role account-role--photographer">Fotógrafo</span>
              <?php else: ?>
                <span style="font-size:.86rem;color:#777;">Cliente registrado</span>
              <?php endif; ?>
            </div>
          </div>
          <?php if (($_SESSION['role'] ?? '') === 'cliente'): ?>
            <div class="account-actions">
              <a class="btn" href="index.php?controller=event&action=my">Eventos agendados</a>
              <a class="btn" href="index.php?controller=event&action=finished">Eventos finalizados</a>
            </div>
          <?php endif; ?>
        </div>

        <ul class="account-meta">
          <li><strong>Nombre completo:</strong>
            <?= htmlspecialchars($user['nombre']) ?>
            <?= htmlspecialchars($user['apellidoPaterno']) ?>
            <?= htmlspecialchars($user['apellidoMaterno']) ?>
          </li>
          <li><strong>Fecha de nacimiento:</strong> <?= htmlspecialchars($user['fechaNac']) ?></li>
          <li><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></li>
          <li><strong>Teléfono:</strong> <?= htmlspecialchars($user['numTelefono']) ?></li>
          <li><strong>Creado:</strong> <?= htmlspecialchars($user['created_at']) ?></li>
        </ul>
      </section>

      <!-- Panel de edición -->
      <section class="account-card">
        <h2 class="account-section-title">Editar información</h2>
        <form method="POST" action="index.php?controller=user&action=update">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

          <div class="account-form-group">
            <label>Nombre</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($user['nombre']) ?>" required>
          </div>

          <div class="account-form-group">
            <label>Apellido paterno</label>
            <input type="text" name="apellidoPaterno" value="<?= htmlspecialchars($user['apellidoPaterno']) ?>" required>
          </div>

          <div class="account-form-group">
            <label>Apellido materno</label>
            <input type="text" name="apellidoMaterno" value="<?= htmlspecialchars($user['apellidoMaterno']) ?>" required>
          </div>

          <div class="account-form-group">
            <label>Fecha de nacimiento</label>
            <input type="date" name="fechaNac" value="<?= htmlspecialchars($user['fechaNac']) ?>" required>
          </div>

          <div class="account-form-group">
            <label>Correo electrónico</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
          </div>

          <div class="account-form-group">
            <label>Número de teléfono</label>
            <input type="text" name="numTelefono" value="<?= htmlspecialchars($user['numTelefono']) ?>" required>
          </div>

          <div class="account-form-group">
            <label>Nueva contraseña (opcional)</label>
            <input type="password" name="pass" placeholder="Déjalo vacío si no deseas cambiarla">
          </div>

          <button type="submit" class="btn">Guardar cambios</button>
        </form>

        <div class="account-delete">
          <h3 style="margin-top:0;font-size:1rem;">Eliminar cuenta</h3>
          <form method="POST" action="index.php?controller=user&action=delete" onsubmit="return confirm('¿Seguro que deseas eliminar tu cuenta? Esta acción no se puede deshacer.');">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            <button type="submit" class="btn" style="background:#b11;color:#fff;border-color:#b11">Eliminar mi cuenta</button>
          </form>
        </div>
      </section>
    </div>
  <?php else: ?>
    <p>No se encontró tu información.</p>
  <?php endif; ?>
</main>
</body>
</html>
