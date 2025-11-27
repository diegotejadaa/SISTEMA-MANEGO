<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Agendar evento</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <?php if (file_exists(__DIR__ . '/../partials/head_assets.php')) include __DIR__ . '/../partials/head_assets.php'; ?>
  <style>
    .agendar-wrap{
      max-width: 1060px;
      margin: 2.5rem auto;
      background: #faf7f3;
      padding: 2rem 2.5rem;
      border-radius: 18px;
      box-shadow: 0 10px 30px rgba(0,0,0,.06);
    }
    .agendar-wrap h2{
      margin-top: 0;
      margin-bottom: .75rem;
    }
    .agendar-subtitle{
      margin: 0 0 1.5rem;
      color: #555;
    }
    .agendar-grid{
      display: grid;
      grid-template-columns: minmax(260px, 1.4fr) minmax(220px, 1fr);
      gap: 1.5rem;
      align-items: flex-start;
    }
    .agendar-col{
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }
    .agendar-field{
      display: flex;
      flex-direction: column;
      gap: .35rem;
    }
    label{
      font-weight: 600;
    }
    input, textarea, select{
      width: 100%;
      padding: .7rem .85rem;
      border: 1px solid #ced4da;
      border-radius: 10px;
      font-size: .95rem;
    }
    textarea{
      min-height: 140px;
      resize: vertical;
    }
    .agendar-calendar{
      padding: 1rem 1.25rem;
      background: #fff;
      border-radius: 14px;
      border: 1px solid #e0e0e0;
      box-shadow: 0 4px 14px rgba(0,0,0,.04);
    }
    .agendar-calendar input[type="date"]{
      font-size: 1.05rem;
      padding: .9rem 1rem;
    }
    .agendar-calendar small{
      display:block;
      margin-top:.4rem;
      color:#777;
    }
    .agendar-actions{
      margin-top: 1.5rem;
      display: flex;
      gap: .75rem;
      flex-wrap: wrap;
    }
    .agendar-actions .btn{
      display:inline-block;
      background:#111;
      color:#fff;
      border:none;
      border-radius:999px;
      padding:.65rem 1.6rem;
      text-decoration:none;
      cursor:pointer;
    }
    .agendar-actions .btn:nth-child(2){
      background:#e0e0e0;
      color:#111;
    }
    @media (max-width: 800px){
      .agendar-wrap{
        margin: 1.5rem 1rem 2.5rem;
        padding: 1.5rem 1.25rem;
      }
      .agendar-grid{
        grid-template-columns: 1fr;
      }
    }

  </style>
</head>
<body>
<?php if (file_exists(__DIR__ . '/../partials/header.php')) include __DIR__ . '/../partials/header.php'; ?>
<main class="agendar-wrap">
  <h2>Agendar evento</h2>
  <?php if (!empty($_SESSION['flash_error'])): ?>
    <p class="flash-error"><?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></p>
  <?php endif; ?>
  <?php if (!empty($_SESSION['flash_ok'])): ?>
    <p class="flash-ok"><?= htmlspecialchars($_SESSION['flash_ok']); unset($_SESSION['flash_ok']); ?></p>
  <?php endif; ?>
  <?php if(!empty($paquete)): ?>
    <p>Paquete seleccionado: <strong><?= htmlspecialchars($paquete['nombrePack']) ?></strong></p>
  <?php endif; ?>
  <form method="post" action="index.php?controller=event&action=store">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
    <input type="hidden" name="paquete_id" value="<?= (int)($packId ?? ($paquete['idPack'] ?? 0)) ?>">
    <div class="agendar-grid">
      <div>
        <label>Fecha del evento</label>
        <input type="date" name="fecha_evento" required>
      </div>
      <div>
        <label>Hora del evento</label>
        <input type="time" name="hora_evento" required>
      </div>
      <div class="agendar-grid-1">
        <label>Ubicación</label>
        <input type="text" name="ubicacion" placeholder="Dirección o punto de referencia" required>
      </div>
      <div class="agendar-grid-1">
        <label>Notas adicionales</label>
        <textarea name="notas" rows="4" placeholder="Indica detalles importantes (opcional)"></textarea>
      </div>
    </div>
    <div style="margin-top:12px">
      <button class="btn" type="submit">Enviar solicitud</button>
      <a href="index.php" class="btn">Cancelar</a>
    </div>
  </form>
</main>
</body>
</html>
