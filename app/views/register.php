<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="es">
  <?php include __DIR__ . '/partials/head_assets.php'; ?>
<meta charset="utf-8">
  <title><?= isset($view_title)?htmlspecialchars($view_title):'Registro' ?></title>
  <link rel="stylesheet" href="public/css/manego.css">
  <style>
    .register-wrapper{
      max-width: 1100px;
      margin: 3rem auto 4rem;
      display:flex;
      justify-content:center;
      align-items:center;
    }
    .register-card{
      background:#ffffff;
      border-radius:18px;
      padding:2.2rem 2.6rem 2.4rem;
      box-shadow:0 18px 40px rgba(0,0,0,.12);
      max-width:520px;
      width:100%;
    }
    .register-header{
      text-align:center;
      margin-bottom:1.6rem;
    }
    .register-avatar{
      width:72px;
      height:72px;
      border-radius:50%;
      margin:0 auto 0.8rem;
      border:3px solid #111;
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:2.4rem;
      background:#f5f5f5;
    }
    .register-header h1{
      margin:0;
      font-size:1.7rem;
    }
    .register-header p{
      margin:.2rem 0 0;
      font-size:.9rem;
      color:#666;
    }
    .register-form label{
      display:block;
      margin-bottom:.15rem;
      font-weight:600;
    }
    .register-form input[type="text"],
    .register-form input[type="email"],
    .register-form input[type="password"],
    .register-form input[type="date"]{
      width:100%;
      padding:.45rem .6rem;
      border-radius:8px;
      border:1px solid #ccc;
      font-size:.95rem;
      box-sizing:border-box;
    }
    .register-form .field-group{
      margin-bottom:1rem;
    }
    .register-form button.btn{
      width:100%;
      margin-top:.5rem;
    }
    @media (max-width: 700px){
      .register-wrapper{
        margin:2rem 1rem 3rem;
      }
      .register-card{
        padding:1.8rem 1.5rem 2rem;
      }
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/partials/header.php'; ?>
<div class="register-wrapper">
  <div class="register-card">
    <div class="register-header">
      <div class="register-avatar">📝</div>
      <h1>Crear cuenta</h1>
      <p>Regístrate para agendar tus sesiones de fotografía.</p>
    </div>

    <?php if (!empty($msg)): ?><p class="flash-ok"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
    <?php if (!empty($error)): ?><p class="flash-error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <form class="register-form" method="POST" action="index.php?controller=user&action=store">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

      <div class="field-group">
        <label>Nombre</label>
        <input type="text" name="nombre" required>
      </div>

      <div class="field-group">
        <label>Apellido paterno</label>
        <input type="text" name="apellidoPaterno" required>
      </div>

      <div class="field-group">
        <label>Apellido materno</label>
        <input type="text" name="apellidoMaterno" required>
      </div>

      <div class="field-group">
        <label>Fecha de nacimiento</label>
        <input type="date" name="fechaNac" required>
      </div>

      <div class="field-group">
        <label>Correo electrónico</label>
        <input type="email" name="email" required>
      </div>

      <div class="field-group">
        <label>Número de teléfono</label>
        <input type="text" name="numTelefono" required>
      </div>

      <div class="field-group">
        <label>Contraseña</label>
        <input type="password" name="pass" required>
      </div>

      <button type="submit" class="btn">Registrarme</button>
    </form>
  </div>
</div>
</body>
</html>
