<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Asignación administrativa</title>
  <?php include __DIR__ . '/../partials/head_assets.php'; ?>
  <link rel="stylesheet" href="public/css/manego.css">
  <style>
    .assign-wrapper{
      max-width:1100px;
      margin:2.5rem auto 3rem;
      background:#faf7f3;
      padding:1.8rem 2rem 2.1rem;
      border-radius:18px;
      box-shadow:0 10px 28px rgba(0,0,0,.06);
    }
    .assign-header{
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      gap:1.2rem;
      flex-wrap:wrap;
      margin-bottom:1.2rem;
    }
    .assign-header h1{
      margin:0;
    }
    .assign-header span{
      font-size:.9rem;
      color:#666;
    }
    .assign-actions{
      display:flex;
      flex-wrap:wrap;
      gap:.6rem;
      align-items:flex-start;
    }
    .assign-btn{
      padding:.45rem .9rem;
      border-radius:999px;
      border:none;
      background:#111;
      color:#fff;
      cursor:pointer;
      font-size:.85rem;
    }
    .assign-btn--secondary{
      background:#333;
    }
    .assign-btn:hover{
      opacity:.9;
    }
    .assign-file{
      display:inline-flex;
      align-items:center;
      gap:.4rem;
      font-size:.8rem;
    }
    .assign-file input[type="file"]{
      font-size:.8rem;
    }
    .assign-table{
      width:100%;
      border-collapse:collapse;
      margin-top:.6rem;
      font-size:.9rem;
      background:#fff;
      border-radius:14px;
      overflow:hidden;
    }
    .assign-table th,
    .assign-table td{
      padding:.55rem .6rem;
      border-bottom:1px solid #e0e0e0;
      text-align:left;
      vertical-align:middle;
    }
    .assign-table th{
      background:#f1ece4;
      font-weight:600;
    }
    .assign-role-badge{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      padding:.2rem .6rem;
      border-radius:999px;
      font-size:.8rem;
      text-transform:capitalize;
      background:#ffe8c2;
      color:#8a5a00;
      white-space:nowrap;
    }
    .assign-role-badge--admin{
      background:#d4f6d5;
      color:#216b29;
    }
    .assign-role-badge--photographer{
      background:#ffe0e0;
      color:#a12a2a;
    }
    .assign-alert{
      padding:.6rem .8rem;
      border-radius:10px;
      margin-bottom:.8rem;
      font-size:.85rem;
    }
    .assign-alert--ok{
      background:#d4f6d5;
      color:#1c5a23;
    }
    .assign-alert--error{
      background:#ffe0e0;
      color:#a12a2a;
    }
    .switch{
      position:relative;
      display:inline-block;
      width:42px;
      height:22px;
    }
    .switch input{
      opacity:0;
      width:0;
      height:0;
    }
    .slider{
      position:absolute;
      cursor:pointer;
      top:0;
      left:0;
      right:0;
      bottom:0;
      background-color:#ccc;
      transition:.2s;
      border-radius:999px;
    }
    .slider:before{
      position:absolute;
      content:"";
      height:16px;
      width:16px;
      left:3px;
      top:3px;
      background-color:#fff;
      transition:.2s;
      border-radius:50%;
    }
    .switch input:checked + .slider{
      background-color:#111;
    }
    .switch input:checked + .slider:before{
      transform:translateX(18px);
    }
    .switch input:disabled + .slider{
      opacity:.5;
      cursor:not-allowed;
    }
    @media (max-width:768px){
      .assign-header{
        align-items:flex-start;
      }
      .assign-actions{
        flex-direction:column;
        align-items:flex-start;
      }
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="assign-wrapper">
  <div class="assign-header">
    <div>
      <h1>Asignación administrativa</h1>
      <span>Configura qué usuarios pueden acceder como administradores.</span>
    </div>
    <div class="assign-actions no-print">
      <form method="get" action="index.php">
        <input type="hidden" name="controller" value="admin">
        <input type="hidden" name="action" value="backupDb">
        <button type="submit" class="assign-btn">Respaldo de la base de datos</button>
      </form>
      <form method="post" action="index.php?controller=admin&action=restoreDb" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
        <label class="assign-file">
          <span>Archivo de respaldo (.sql)</span>
          <input type="file" name="backup_file" accept=".sql" required>
        </label>
        <button type="submit" class="assign-btn assign-btn--secondary">Restauración de la base de datos</button>
      </form>
    </div>
  </div>

  <?php if (!empty($_SESSION['flash_ok'])): ?>
    <div class="assign-alert assign-alert--ok">
      <?php echo htmlspecialchars($_SESSION['flash_ok']); unset($_SESSION['flash_ok']); ?>
    </div>
  <?php endif; ?>
  <?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="assign-alert assign-alert--error">
      <?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
    </div>
  <?php endif; ?>

  <table class="assign-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>Teléfono</th>
        <th>Rol actual</th>
        <th>Permiso administrador</th>
        <th>Permiso fotógrafo</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($users)): ?>
        <?php $currentId = $_SESSION['id'] ?? null; ?>
        <?php foreach ($users as $u): ?>
          <?php $isAdmin = ($u['role'] === 'admin'); ?>
          <?php $isPhotographer = ($u['role'] === 'fotografo'); ?>
          <tr>
            <td><?php echo (int)$u['id']; ?></td>
            <td><?php echo htmlspecialchars($u['nombre'] . ' ' . $u['apellidoPaterno'] . ' ' . $u['apellidoMaterno']); ?></td>
            <td><?php echo htmlspecialchars($u['email']); ?></td>
            <td><?php echo htmlspecialchars($u['numTelefono']); ?></td>
            <td>
              <span class="assign-role-badge <?php echo $isAdmin ? 'assign-role-badge--admin' : ($isPhotographer ? 'assign-role-badge--photographer' : ''); ?>">
                <?php echo htmlspecialchars($u['role']); ?>
              </span>
            </td>
            <td>
              <form method="post" action="index.php?controller=admin&action=toggleRole" class="assign-toggle-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
                <label class="switch">
                  <input type="checkbox" name="make_admin" value="1"
                    <?php if ($isAdmin) echo 'checked'; ?>
                    <?php if ((int)$currentId === (int)$u['id']) echo 'disabled'; ?>>
                  <span class="slider"></span>
                </label>
              </form>
            </td>
            <td>
              <form method="post" action="index.php?controller=admin&action=toggleRole" class="assign-toggle-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
                <label class="switch">
                  <input type="checkbox" name="make_photographer" value="1"
                    <?php if ($isPhotographer) echo 'checked'; ?>
                    <?php if ((int)$currentId === (int)$u['id']) echo 'disabled'; ?>>
                  <span class="slider"></span>
                </label>
              </form>
            </td>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="7">No hay usuarios registrados.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<script>
  document.querySelectorAll('.assign-toggle-form input[type="checkbox"]').forEach(function(chk){
    chk.addEventListener('change', function(){
      if (this.disabled) return;
      this.form.submit();
    });
  });
</script>
</body>
</html>
