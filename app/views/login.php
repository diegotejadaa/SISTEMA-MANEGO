<?php $msg = $_GET['msg'] ?? null; ?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Iniciar sesión</title>
<link rel="stylesheet" href="public/css/manego.css"></head><body>
<header class="header">
  <div class="logo"><img src="public/img/logoManego.png" alt="MANEGO" style="height:40px;border-radius:50%"></div>
  <nav class="nav" style="padding:10px;text-align:right;">
    <a href="index.php">Inicio</a>
    <a href="index.php?controller=auth&action=login">Iniciar sesión</a>
    <a href="index.php?controller=auth&action=register">Registrarse</a>
    <a href="index.php?controller=auth&action=registerAdmin">Registrar Admin</a>
  </nav>
</header>
<main style="max-width:700px;margin:40px auto;">
<h1>Inicia sesión</h1>
<?php if(!empty($msg))   echo "<p style='color:green;'>".htmlspecialchars($msg)."</p>"; ?>
<?php if(!empty($error)) echo "<p style='color:red;'>".htmlspecialchars($error)."</p>"; ?>
<form action="index.php?controller=auth&action=login" method="POST">
  <label>Nombre:</label><br><input type="text" name="nombre" required><br><br>
  <label>Correo electrónico:</label><br><input type="email" name="email" required><br><br>
  <label>Contraseña:</label><br><input type="password" name="pass" required><br><br>
  <input type="submit" value="Ingresar" name="ingresar">
</form>
</main></body></html>
