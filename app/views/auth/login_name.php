<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="es">

  <?php include __DIR__ . '/../partials/head_assets.php'; ?>
<meta charset="utf-8">
  <title>Iniciar sesión</title>
  <link rel="stylesheet" href="public/css/manego.css">
  <style>
    .login-wrapper{
      max-width: 1100px;
      margin: 3rem auto 4rem;
      display:flex;
      justify-content:center;
      align-items:center;
    }
    .login-card{
      background:#ffffff;
      border-radius:18px;
      padding:2.2rem 2.6rem 2.4rem;
      box-shadow:0 18px 40px rgba(0,0,0,.12);
      max-width:420px;
      width:100%;
    }
    .login-header{
      text-align:center;
      margin-bottom:1.6rem;
    }
    .login-avatar{
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
    .login-header h1{
      margin:0;
      font-size:1.7rem;
    }
    .login-header p{
      margin:.2rem 0 0;
      font-size:.9rem;
      color:#666;
    }
    .login-form label{
      display:block;
      margin-bottom:.15rem;
      font-weight:600;
    }
    .login-form input[type="text"],
    .login-form input[type="password"]{
      width:100%;
      padding:.45rem .6rem;
      border-radius:8px;
      border:1px solid #ccc;
      font-size:.95rem;
      box-sizing:border-box;
    }
    .login-form .field-group{
      margin-bottom:1rem;
    }
    .login-form button.btn{
      width:100%;
      margin-top:.5rem;
    }
    @media (max-width: 700px){
      .login-wrapper{
        margin:2rem 1rem 3rem;
      }
      .login-card{
        padding:1.8rem 1.5rem 2rem;
      }
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="login-wrapper">
  <div class="login-card">
    <div class="login-header">
      <div class="login-avatar">👤</div>
      <h1>Iniciar sesión</h1>
    </div>

    <?php if (!empty($error)): ?>
      <p class="flash-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form class="login-form" method="POST" action="index.php?controller=auth&action=loginName">
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
        <label>Contraseña</label>
        <input type="password" name="pass" required>
      </div>

      <button type="submit" class="btn">Entrar</button>
    </form>
  </div>
</div>
</body>
</html>
