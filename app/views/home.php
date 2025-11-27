<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Inicio</title>
  <?php include __DIR__ . '/partials/head_assets.php'; ?>
</head>
<body>
<?php include __DIR__ . '/partials/header.php'; ?>

<?php
  $showLoginEventModal = !empty($_SESSION['event_login_required']);
  if ($showLoginEventModal) {
    unset($_SESSION['event_login_required']);
  }
?>

<?php if ($showLoginEventModal): ?>
  <div id="modal-login-event" class="modal is-open" aria-hidden="false">
    <div class="modal__dialog" role="dialog" aria-modal="true">
      <button class="modal__close" type="button" aria-label="Cerrar" data-modal-close>&times;</button>
      <h2 style="margin-bottom:0.75rem;">Aviso</h2>
      <p style="margin-bottom:1.2rem;">
        Crea un usuario o inicia sesión para poder agendar un evento.
      </p>
      <div class="modal__actions">
        <a href="index.php?controller=auth&action=loginName" class="btn">Iniciar sesión</a>
        <a href="index.php?controller=user&action=register" class="btn" style="margin-left:.5rem;">Crear usuario</a>
      </div>
    </div>
    <div class="modal__backdrop" data-modal-close></div>
  </div>
<?php endif; ?>


<!-- Encabezado con menú 
  <header class="header">
    <div class="logo">
      <img src="public/img/logoManego.png" alt="Logo MANEGO" />
      <span>MANEGO</span>
    </div>
    <nav class="nav">
      <a href="index.php?controller=auth&action=login">Iniciar sesión</a>
      <a href="index.php?controller=auth&action=register">Registrarse</a>
    </nav>
  </header> 

  -->

  <!-- Hero principal -->
  <section class="hero">
    <div class="hero-texto">
      <h1>Sesiones fotográficas</h1>
      <p>
        ¿Buscas quién cubra tu evento? En <strong>MANEGO</strong> nos
        encargamos de capturar todos los ángulos de esos momentos inolvidables.
      </p>

      <a
        href="https://api.whatsapp.com/send?phone=7775403472&text=%C2%A1Hola!%20%C2%BFPodr%C3%ADan%20darme%20informes%20de%20las%20sesiones%20de%20fotos%3F"
        target="_blank"
        rel="noopener noreferrer"
        class="btn-whatsapp"
        aria-label="Escríbenos por WhatsApp"
      >
        <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
        <span>¡Escríbenos por WhatsApp!</span>
      </a>

      <a
        href="https://www.instagram.com/manego._/"
        target="_blank"
        rel="noopener noreferrer"
        class="btn-instagram"
        aria-label="Síguenos en Instagram"
      >
        <i class="fa-brands fa-instagram" aria-hidden="true"></i>
        <span>¡Síguenos en Instagram!</span>
      </a>
    </div>

    <div class="hero-stats">
      <div class="stat">
        <h2>+25</h2>
        <p>Sesiones realizadas</p>
      </div>
      <div class="stat">
        <h2>100%</h2>
        <p>Garantía de satisfacción</p>
      </div>
      <div class="stat">
        <h2>+3</h2>
        <p>Años de experiencia</p>
      </div>

      <div class="hero-discount">
        <p>
          🎉 ¡Regístrate ahora y obtén un <strong>10% de descuento</strong> en tu
          primera sesión! 🎉
        </p>
      </div>
    </div>
  </section>

  <!-- Carrusel fotos -->
  <div class="carousel" id="manegoCarousel">
    <div class="carousel-track">
      <div class="carousel-item">
        <img src="public/img/R8.2.jpg" alt="F1" />
      </div>
      <div class="carousel-item">
        <img src="public/img/zapatos.jpg" alt="F2" />
      </div>
      <div class="carousel-item">
        <img src="public/img/TTRS.jpg" alt="F3" />
      </div>
      <div class="carousel-item">
        <img src="public/img/R8.1.jpg" alt="F4" />
      </div>
      <div class="carousel-item">
        <img src="public/img/BMW.jpg" alt="F5" />
      </div>
      <div class="carousel-item">
        <img src="public/img/Comida.jpg" alt="F6" />
      </div>
      <div class="carousel-item">
        <img src="public/img/Miata.jpg" alt="F7" />
      </div>
      <div class="carousel-item">
        <img src="public/img/bautizoAlex.jpg" alt="F8" />
      </div>
    </div>
  </div>

  <!-- Script del carrusel -->
  <script>
    (function () {
      const wrap = document.querySelector("#manegoCarousel");
      const track = wrap.querySelector(".carousel-track");
      const items = track.children.length;
      let visible;

      function getVisible() {
        if (window.matchMedia("(max-width:600px)").matches) return 1;
        if (window.matchMedia("(max-width:900px)").matches) return 2;
        return 3;
      }

      function updateVisible() {
        visible = getVisible();
        Array.from(track.children).forEach((item) => {
          item.style.flex = `0 0 calc(100% / ${visible})`;
        });
      }

      window.addEventListener("resize", updateVisible);
      updateVisible();

      let index = 0;
      setInterval(() => {
        const maxIndex = Math.max(0, items - visible);
        index = index >= maxIndex ? 0 : index + 1;
        const shift = (100 / items) * index * visible;
        track.style.transform = `translateX(-${shift}%)`;
      }, 2000);
    })();
  </script>

  <!-- Tarjetas -->
  
<section class="cards-row">
  <?php if (!empty($paquetes)): ?>
    <?php $i = 0; foreach ($paquetes as $p): 
      $id      = (int)($p['idPack'] ?? 0);
      $nombre  = $p['nombrePack'] ?? '';
      $desc    = $p['descripcion'] ?? '';
      $precio  = isset($p['precio']) ? (float)$p['precio'] : 0;
      $det     = $p['detalles'] ?? '';
      $delay   = 100 + ($i * 100);
      $i++;
    ?>
      <article class="card news-card" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
        <h3><?= htmlspecialchars($nombre) ?></h3>
        <p><?= htmlspecialchars($desc) ?></p>
        <a href="#modal-pack-<?= $id ?>" class="btn" data-modal-open="modal-pack-<?= $id ?>">Conoce más</a>
      </article>

      <div id="modal-pack-<?= $id ?>" class="modal" aria-hidden="true">
        <div class="modal__dialog" role="dialog" aria-modal="true" aria-labelledby="modal-title-<?= $id ?>">
          <button class="modal__close" type="button" aria-label="Cerrar" data-modal-close>&times;</button>
          <?php if (!empty($p['imagen_url'])): ?>
            <img src="<?= htmlspecialchars($p['imagen_url']) ?>" alt="<?= htmlspecialchars($nombre) ?>" style="width:100%;max-height:280px;object-fit:cover;border-radius:12px;border:1px solid #2a2a2a;margin:10px 0;">
          <?php endif; ?>
          <?php if($desc !== ''): ?><p><strong>Descripción:</strong> <?= htmlspecialchars($desc) ?></p><?php endif; ?>
          <?php if($det !== ''): ?><p><strong>Detalles:</strong> <?= nl2br(htmlspecialchars($det)) ?></p><?php endif; ?>
          <p><strong>Precio:</strong> $<?= number_format($precio, 2, '.', ',') ?> MXN</p>
        <?php if (empty($_SESSION['role']) || $_SESSION['role'] === 'cliente'): ?>
        <div class="modal__actions">
          <a href="index.php?controller=event&action=create&pack=<?= $id ?>" class="btn">Agendar evento</a>
        </div>
        <?php endif; ?>
        
        </div>
        <div class="modal__backdrop" data-modal-close></div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <article class="card news-card" data-aos="fade-up" data-aos-delay="100">
      <h3>Pronto más paquetes</h3>
      <p>Aún no hay paquetes publicados.</p>
      <span class="btn" style="pointer-events:none;opacity:.6">Conoce más</span>
    </article>
  <?php endif; ?>
</section>

<style>
  .modal{position:fixed;inset:0;display:none;z-index:60}
  .modal.is-open{display:block}
  .modal__backdrop{position:absolute;inset:0;background:rgba(0,0,0,.55)}
  .modal__dialog{
    position:relative; z-index:61; max-width:680px; margin:8vh auto; background:#111; color:#eee;
    border-radius:16px; padding:22px; box-shadow:0 10px 30px rgba(0,0,0,.35); border:1px solid #2a2a2a
  }
  .modal__close{
    position:absolute; top:8px; right:12px; background:#222; color:#fff; border:1px solid #333;
    border-radius:10px; padding:6px 10px; cursor:pointer; font-size:18px
  }
  .modal__actions{
    margin-top:1.2rem;
    display:flex;
    justify-content:flex-start;
  }
  .modal__actions .btn{
    padding:0.7rem 1.6rem;
    border-radius:999px;
    font-size:0.95rem;
  }
</style>

<script>
  document.addEventListener('click', function(e){
    const openBtn = e.target.closest('[data-modal-open]');
    if(openBtn){
      e.preventDefault();
      const id = openBtn.getAttribute('data-modal-open');
      const modal = document.getElementById(id);
      if(modal){ modal.classList.add('is-open'); modal.setAttribute('aria-hidden','false'); }
    }
    const closeBtn = e.target.closest('[data-modal-close]');
    if(closeBtn){
      const modal = closeBtn.closest('.modal');
      if(modal){ modal.classList.remove('is-open'); modal.setAttribute('aria-hidden','true'); }
    }
  });
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape'){
      document.querySelectorAll('.modal.is-open').forEach(m=>{
        m.classList.remove('is-open'); m.setAttribute('aria-hidden','true');
      });
    }
  });
</script>

</body>
</html>
