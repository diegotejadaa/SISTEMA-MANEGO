<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="es">
  <?php include __DIR__ . '/../partials/head_assets.php'; ?>
  <meta charset="utf-8">
  <title>Opciones de administrador</title>
  <link rel="stylesheet" href="public/css/manego.css">
  <style>
    .admin-section{margin-top:8px;padding:8px 0 24px;}
    .admin-title-row{display:flex;justify-content:space-between;align-items:flex-end;gap:16px;flex-wrap:wrap;}
    .admin-title-row h1{margin:0;}
    .admin-subtitle{color:#b3b3b3;margin:4px 0 0 0;font-size:0.95rem;}
    .admin-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:20px;margin-top:20px;}
    .admin-card{background:#121212;border:1px solid #2a2a2a;border-radius:18px;padding:20px 20px 18px;box-shadow:0 10px 25px rgba(0,0,0,.25);transition:transform .18s ease,box-shadow .18s ease,border-color .18s ease;}
    .admin-card:hover{transform:translateY(-3px);box-shadow:0 20px 40px rgba(0,0,0,.35);border-color:#3b3b3b;}
    .admin-card h3{margin:0 0 8px 0;color:#fff;font-size:1.15rem;}
    .admin-card p{margin:0 0 14px 0;color:#d9d9d9;font-size:.95rem;}
    .admin-card a.btn{display:inline-block;margin-top:4px;}
  </style>
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="container admin-section">
  <div class="admin-title-row">
    <div>
      <h1 class="admin-title"><?php echo (($_SESSION['role'] ?? '') === 'fotografo') ? 'Opciones de fotógrafo' : 'Opciones de administrador'; ?></h1>
      <p class="admin-subtitle">Gestiona paquetes, multimedia, eventos y reportes desde un solo panel.</p>
    </div>
  </div>

  <div class="admin-grid">
    <div class="admin-card">
      <h3>Paquetes de sesiones</h3>
      <p>Crear, editar y administrar paquetes.</p>
      <a class="btn" href="index.php?controller=adminpkg&action=list">Administrar paquetes</a>
    </div>

    <div class="admin-card">
      <h3>Entrega de multimedia</h3>
      <p>Gestiona la entrega de fotos y videos a tus clientes.</p>
      <a class="btn" href="index.php?controller=admin&action=media">Abrir módulo</a>
    </div>

    <div class="admin-card">
      <h3>Eventos próximos</h3>
      <p>Calendario y recordatorios de sesiones agendadas.</p>
      <a class="btn" href="index.php?controller=event&action=adminPanel">Ver eventos</a>
    </div>

<?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
    <div class="admin-card">
      <h3>Reportes estadísticos</h3>
      <p>Consulta métricas de eventos finalizados y pendientes.</p>
      <a class="btn" href="index.php?controller=admin&action=reports">Ver reportes</a>
    </div>

    <div class="admin-card">
      <h3>Asignación administrativa</h3>
      <p>Gestiona qué administradores tienen acceso a cada módulo.</p>
      <a class="btn" href="index.php?controller=admin&action=assignments">Configurar accesos</a>
    </div>
<?php endif; ?>
  </div>
</div>
</body>
</html>
