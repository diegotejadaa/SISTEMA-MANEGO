<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Perfil del Administrador</title><link rel="stylesheet" href="public/css/manego.css"></head><body>
<header class="header"><div class="logo"><img src="public/img/logoManego.png" alt="MANEGO" style="height:40px;border-radius:50%"></div>
<nav class="nav"><a href="index.php">Inicio</a><a href="index.php?controller=admin&action=profile">Perfil</a><a href="index.php?controller=auth&action=logout">Cerrar sesión</a></nav></header>
<main style="max-width:900px;margin:40px auto;"><h1>Perfil del Administrador</h1>
<?php if(!empty($admin)): ?>
<table>
<tr><th>ID</th><td><?= htmlspecialchars($admin['id']) ?></td></tr>
<tr><th>Nombre</th><td><?= htmlspecialchars($admin['nombre']) ?></td></tr>
<tr><th>Apellido Paterno</th><td><?= htmlspecialchars($admin['apellidoPaterno']) ?></td></tr>
<tr><th>Apellido Materno</th><td><?= htmlspecialchars($admin['apellidoMaterno']) ?></td></tr>
<tr><th>Fecha de Nacimiento</th><td><?= htmlspecialchars($admin['fechaNac']) ?></td></tr>
<tr><th>Email</th><td><?= htmlspecialchars($admin['email']) ?></td></tr>
<tr><th>Teléfono</th><td><?= htmlspecialchars($admin['numTelefono']) ?></td></tr>
<tr><th>Rol</th><td><?= htmlspecialchars($admin['role']) ?></td></tr>
</table>
<p style="margin-top:10px;color:#666;">(La contraseña no se muestra por seguridad)</p>
<?php else: ?><p>No se encontró información del administrador.</p><?php endif; ?>
</main></body></html>
