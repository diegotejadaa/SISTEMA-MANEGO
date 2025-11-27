<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<header class="header">
  <div class="logo"><a href="index.php"><img src="public/img/logoManego.png" alt="MANEGO" style="height:40px;border-radius:50%"></a></div>
  <nav class="nav">
    <a href="index.php">Inicio</a>
    <?php if (empty($_SESSION['role'])): ?>
      <a href="index.php?controller=user&action=register">Registrarse</a>
            <a class="btn" href="index.php?controller=auth&action=loginName">Mi cuenta</a>
    <?php elseif ($_SESSION['role'] === 'admin'): ?>

      <a href="index.php?controller=adminpkg&action=options">Opciones de administrador</a>
      <a class="btn" href="index.php?controller=user&action=account">Mi cuenta</a>
      <a href="index.php?controller=auth&action=logout">Salir</a>
    <?php elseif ($_SESSION['role'] === 'fotografo'): ?>

      <a href="index.php?controller=adminpkg&action=options">Opciones de fotógrafo</a>
      <a class="btn" href="index.php?controller=user&action=account">Mi cuenta</a>
      <a href="index.php?controller=auth&action=logout">Salir</a>
    <?php else: ?>

      <a class="btn" href="index.php?controller=user&action=account">Mi cuenta</a>
      <a href="index.php?controller=auth&action=logout">Salir</a>
    <?php endif; ?>
  </nav>
</header>