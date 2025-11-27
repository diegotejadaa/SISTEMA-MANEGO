<?php
class EventController {
  private $connection;

  public function __construct($connection){
    if (session_status() === PHP_SESSION_NONE) session_start();
    $this->connection = $connection;
    if (empty($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
  }

  public function create(){
    if (session_status() === PHP_SESSION_NONE) session_start();
    // Si el usuario no ha iniciado sesión, mostramos mensaje en inicio
    if (empty($_SESSION['id'])){
      $_SESSION['event_login_required'] = true;
      header("Location: index.php?controller=home&action=index");
      exit;
    }

    $packId = isset($_GET['pack']) ? (int)$_GET['pack'] : 0;
    $paquete = null;
    if (file_exists(__DIR__ . '/../models/PackageModel.php')){
      require_once __DIR__ . '/../models/PackageModel.php';
      $pkg = new PackageModel($this->connection);
      if ($packId > 0){
        $paquete = $pkg->getById($packId);
      }
      $paquetes = $pkg->all();
    } else {
      $paquetes = [];
    }
    include __DIR__ . '/../views/events/create.php';
  }


  public function store(){
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['id'])){
      $_SESSION['flash_error'] = 'Debes iniciar sesión para agendar un evento.';
      header("Location: index.php?controller=auth&action=loginName");
      exit;
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
      header("Location: index.php?controller=event&action=create");
      exit;
    }
    $token = $_POST['csrf_token'] ?? '';
    if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
      http_response_code(400);
      echo "Token CSRF inválido.";
      exit;
    }

    $usuarioId = (int)($_SESSION['id'] ?? 0);
    $paqueteId = (int)($_POST['paquete_id'] ?? 0);
    $fecha     = trim($_POST['fecha_evento'] ?? '');
    $hora      = trim($_POST['hora_evento'] ?? '');
    $ubic      = trim($_POST['ubicacion'] ?? '');
    $notas     = trim($_POST['notas'] ?? '');

    require_once __DIR__ . '/../models/EventModel.php';
    $model = new EventModel($this->connection);

    // Validación de fecha: no puede ser pasada ni estar ocupada por otro evento
    $today = date('Y-m-d');
    if (empty($fecha) || $fecha < $today || $model->isDateTaken($fecha)) {
      $_SESSION['flash_error'] = 'Fecha no disponible, elija otra diferente.';
      $redirect = 'index.php?controller=event&action=create';
      if ($paqueteId > 0) {
        $redirect .= '&pack=' . $paqueteId;
      }
      header("Location: " . $redirect);
      exit;
    }

    $ok = $model->create([
      'usuario_id'    => $usuarioId,
      'paquete_id'    => $paqueteId,
      'fecha_evento'  => $fecha,
      'hora_evento'   => $hora,
      'ubicacion'     => $ubic,
      'notas'         => $notas
    ]);
    if ($ok){
      $_SESSION['flash_ok'] = 'Evento agendado correctamente.';
      header("Location: index.php?controller=event&action=my");
    } else {
      $_SESSION['flash_error'] = 'No se pudo agendar el evento.';
      $redirect = 'index.php?controller=event&action=create';
      if ($paqueteId > 0) {
        $redirect .= '&pack=' . $paqueteId;
      }
      header("Location: " . $redirect);
    }
    exit;
  }

  public function my(){
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['id'])){ header("Location: index.php?controller=auth&action=loginName"); exit; }
    require_once __DIR__ . '/../models/EventModel.php';
    $model = new EventModel($this->connection);
    $eventos = $model->byUserActive((int)$_SESSION['id']);
    include __DIR__ . '/../views/events/my.php';
  }

  /**
   * Vista de eventos finalizados para el cliente.
   */
  public function finished(){
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['id'])){ header("Location: index.php?controller=auth&action=loginName"); exit; }
    require_once __DIR__ . '/../models/EventModel.php';
    $model = new EventModel($this->connection);
    $eventos = $model->byUserFinished((int)$_SESSION['id']);
    include __DIR__ . '/../views/events/finished.php';
  }

  public function adminPanel(){
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','fotografo'])){
      http_response_code(403);
      echo "Acceso denegado.";
      exit;
    }
    require_once __DIR__ . '/../models/EventModel.php';
    $model = new EventModel($this->connection);
    $eventos = $model->allForAdmin();
    $view_title = "Eventos agendados";
    include __DIR__ . '/../views/admin/events.php';
  }

  public function adminUpdate(){
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','fotografo'])){
      http_response_code(403);
      echo "Acceso denegado.";
      exit;
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
      header("Location: index.php?controller=event&action=adminPanel");
      exit;
    }
    $token = $_POST['csrf_token'] ?? '';
    if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
      http_response_code(400);
      echo "Token CSRF inválido.";
      exit;
    }
    $id = isset($_POST['idEvento']) ? (int)$_POST['idEvento'] : 0;
    if ($id <= 0){
      $_SESSION['flash_error'] = 'Evento no válido.';
      header("Location: index.php?controller=event&action=adminPanel");
      exit;
    }

    $fecha = trim($_POST['fecha_evento'] ?? '');
    $hora  = trim($_POST['hora_evento'] ?? '');
    $ubic  = trim($_POST['ubicacion'] ?? '');
    $notas = trim($_POST['notas'] ?? '');

    // Si viene el botón de "Finalizar", forzamos el estado a 'finalizado'
    $estado = trim($_POST['estado'] ?? '');
    $isFinalize = !empty($_POST['finalizar']);
    if ($isFinalize){
      $estado = 'finalizado';
    } else {
      if ($estado === ''){ $estado = 'pendiente'; }
      $estado = strtolower($estado);
      if (!in_array($estado, ['pendiente','aceptado','rechazado'])) { $estado = 'pendiente'; }
    }

    require_once __DIR__ . '/../models/EventModel.php';
    $model = new EventModel($this->connection);
    $ok = $model->updateAdmin($id, [
      'fecha_evento' => $fecha,
      'hora_evento'  => $hora,
      'ubicacion'    => $ubic,
      'notas'        => $notas,
      'estado'       => $estado
    ]);
    if ($ok){
      $_SESSION['flash_ok'] = $isFinalize
        ? 'El evento ha sido marcado como finalizado.'
        : 'Evento actualizado correctamente.';
    } else {
      $_SESSION['flash_error'] = 'No se pudo actualizar el evento.';
    }
    header("Location: index.php?controller=event&action=adminPanel");
    exit;
  }

  public function delete(){
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['id'])){
      http_response_code(403);
      echo "Acceso denegado.";
      exit;
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
      header("Location: index.php?controller=event&action=my");
      exit;
    }
    $token = $_POST['csrf_token'] ?? '';
    if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
      http_response_code(400);
      echo "Token CSRF inválido.";
      exit;
    }
    $id = isset($_POST['idEvento']) ? (int)$_POST['idEvento'] : 0;
    if ($id <= 0){
      $_SESSION['flash_error'] = 'Evento no válido.';
      header("Location: index.php?controller=event&action=my");
      exit;
    }
    require_once __DIR__ . '/../models/EventModel.php';
    $model = new EventModel($this->connection);
    $ok = $model->deleteForUser($id, $_SESSION['id']);
    if ($ok){
      $_SESSION['flash_ok'] = 'Evento cancelado correctamente.';
    } else {
      $_SESSION['flash_error'] = 'No se pudo cancelar el evento.';
    }
    header("Location: index.php?controller=event&action=my");
    exit;
  }

}
