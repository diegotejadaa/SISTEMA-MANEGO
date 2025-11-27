<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Registrar Administrador</title><link rel="stylesheet" href="public/css/manego.css"></head><body>
<header class="header"><div class="logo"><img src="public/img/logoManego.png" alt="MANEGO" style="height:40px;border-radius:50%"></div>
<nav class="nav"><a href="index.php">Inicio</a><a href="index.php?controller=auth&action=login">Iniciar sesión</a><a href="index.php?controller=auth&action=register">Registro Cliente</a></nav></header>
<main style="max-width:700px;margin:40px auto;"><h1>Registrar Administrador</h1>
<form method="POST" action="index.php?controller=auth&action=registerAdmin">
  <label>Nombre</label><br><input type="text" name="nombre" required><br><br>
  <label>Apellido paterno</label><br><input type="text" name="apellidoPaterno" required><br><br>
  <label>Apellido materno</label><br><input type="text" name="apellidoMaterno" required><br><br>
  <label>Fecha de nacimiento</label><br><input type="date" name="fechaNac" required><br><br>
  <label>Correo electrónico</label><br><input type="email" name="email" required><br><br>
  <label>Número de teléfono</label><br><input type="text" name="numTelefono" required><br><br>
  <label>Contraseña</label><br><input type="password" name="pass" required><br><br>
  <button type="submit">Registrar Admin</button>
</form></main></body></html>
