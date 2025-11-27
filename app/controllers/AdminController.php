<?php
require_once __DIR__ . '/../models/UserModel.php';

class AdminController {
    private $conn;
    private $userModel;

    public function __construct($connection){
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->conn = $connection;
        $this->userModel = new UserModel($connection);
        if (empty($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','fotografo'])){
            http_response_code(403);
            echo "Acceso denegado.";
            exit;
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public function profile(){
        $id = $_SESSION['id'] ?? null;
        if (!$id){
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
        $admin = $this->userModel->getById($id);
        include __DIR__ . '/../views/admin/profile.php';
    }

    public function media(){
        require_once __DIR__ . '/../models/EventModel.php';
        $model = new EventModel($this->conn);
        $eventos = $model->finishedForMedia();
        include __DIR__ . '/../views/admin/media.php';
    }

    public function mediaUpdate(){
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','fotografo'])){
            http_response_code(403);
            echo "Acceso denegado.";
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header("Location: index.php?controller=admin&action=media");
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
            header("Location: index.php?controller=admin&action=media");
            exit;
        }
        $link = trim($_POST['media_link'] ?? '');
        if ($link === '') {
            $link = null;
        }

        require_once __DIR__ . '/../models/EventModel.php';
        $model = new EventModel($this->conn);
        $ok = $model->updateMediaLink($id, $link);
        if ($ok){
            $_SESSION['flash_ok'] = 'El enlace de material multimedia se ha actualizado.';
        } else {
            $_SESSION['flash_error'] = 'No se pudo actualizar el enlace de material multimedia.';
        }
        header("Location: index.php?controller=admin&action=media");
        exit;
    }

    public function events(){
        require_once __DIR__ . '/../models/EventModel.php';
        $model = new EventModel($this->conn);
        $eventos = $model->allForAdmin();
        $view_title = "Eventos agendados";
        include __DIR__ . '/../views/admin/events.php';
    }

    public function reports(){
        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo "Acceso denegado.";
            exit;
        }


        require_once __DIR__ . '/../models/EventModel.php';
        $model = new EventModel($this->conn);

        // Rango de fechas (periodo determinado)
        $start = isset($_GET['start']) && $_GET['start'] !== '' ? $_GET['start'] : null;
        $end   = isset($_GET['end']) && $_GET['end'] !== '' ? $_GET['end'] : null;

        // Valores por defecto: últimos 6 meses incluyendo el mes actual
        if (!$start || !$end){
            $endDateObj   = new DateTime();
            $end   = $endDateObj->format('Y-m-d');
            $startDateObj = (clone $endDateObj)->modify('-5 months')->modify('first day of this month');
            $start = $startDateObj->format('Y-m-d');
        }

        // Normalizar formato y asegurar que start <= end
        $startDate = date('Y-m-d', strtotime($start));
        $endDate   = date('Y-m-d', strtotime($end));
        if ($endDate < $startDate){
            $tmp = $startDate;
            $startDate = $endDate;
            $endDate = $tmp;
        }

        $statsByMonth   = $model->statsByMonth($startDate, $endDate);
        $statsByService = $model->statsByService($startDate, $endDate);
        $statsByStatus  = $model->statsByStatus($startDate, $endDate);

        $view_title = "Reportes estadísticos";
        include __DIR__ . '/../views/admin/reports.php';
    }

    /**
     * Genera una vista imprimible con la tabla de eventos ACEPTADOS
     * en el rango de fechas indicado, para que el navegador permita
     * guardarla como PDF.
     */
    public function acceptedEventsPdf(){
        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo "Acceso denegado.";
            exit;
        }


        require_once __DIR__ . '/../models/EventModel.php';
        $model = new EventModel($this->conn);

        // Mismos parámetros de periodo que en reports()
        $start = isset($_GET['start']) && $_GET['start'] !== '' ? $_GET['start'] : null;
        $end   = isset($_GET['end']) && $_GET['end'] !== '' ? $_GET['end'] : null;

        // Por defecto: últimos 6 meses (igual que reports)
        if (!$start || !$end){
            $endDateObj   = new DateTime();
            $end   = $endDateObj->format('Y-m-d');
            $startDateObj = (clone $endDateObj)->modify('-5 months')->modify('first day of this month');
            $start = $startDateObj->format('Y-m-d');
        }

        $startDate = date('Y-m-d', strtotime($start));
        $endDate   = date('Y-m-d', strtotime($end));
        if ($endDate < $startDate){
            $tmp = $startDate;
            $startDate = $endDate;
            $endDate   = $tmp;
        }

        $events = $model->acceptedEventsInRange($startDate, $endDate);

        // Vista preparada para impresión/guardar como PDF
        $view_title = "Eventos aceptados";
        include __DIR__ . '/../views/admin/accepted_events_pdf.php';
    }


    public function assignments(){
        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo "Acceso denegado.";
            exit;
        }


        $users = $this->userModel->getAll();
        $view_title = "Asignación administrativa";
        include __DIR__ . '/../views/admin/assignments.php';
    }

    public function toggleRole(){
        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo "Acceso denegado.";
            exit;
        }


        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?controller=admin&action=assignments");
            exit;
        }
        $token = $_POST['csrf_token'] ?? '';
        if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(400);
            echo "Token CSRF inválido.";
            exit;
        }
        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        if ($userId <= 0) {
            $_SESSION['flash_error'] = 'Usuario no válido.';
            header("Location: index.php?controller=admin&action=assignments");
            exit;
        }
        $currentId = $_SESSION['id'] ?? null;
        if ($currentId !== null && (int)$currentId === $userId) {
            $_SESSION['flash_error'] = 'No puedes modificar tus propios privilegios de administrador.';
            header("Location: index.php?controller=admin&action=assignments");
            exit;
        }

        // Obtener datos actuales del usuario
        $userData = $this->userModel->getById($userId);
        $currentRole = $userData['role'] ?? 'cliente';

        // Evaluar interruptores recibidos
        $makeAdmin        = isset($_POST['make_admin']) && $_POST['make_admin'] === '1';
        $makePhotographer = isset($_POST['make_photographer']) && $_POST['make_photographer'] === '1';

        if ($makeAdmin) {
            $newRole = 'admin';
        } elseif ($makePhotographer) {
            $newRole = 'fotografo';
        } else {
            // Si se desactivan ambos interruptores para un usuario que era admin
            // o fotógrafo, regresa a cliente
            if ($currentRole === 'admin' || $currentRole === 'fotografo') {
                $newRole = 'cliente';
            } else {
                $newRole = $currentRole;
            }
        }

        if ($newRole === $currentRole) {
            $_SESSION['flash_ok'] = 'No se realizaron cambios en el rol del usuario.';
            header("Location: index.php?controller=admin&action=assignments");
            exit;
        }

        $ok = $this->userModel->updateRole($userId, $newRole);
        if ($ok) {
            if ($newRole === 'admin') {
                $_SESSION['flash_ok'] = 'Se ha asignado el rol de administrador.';
            } elseif ($newRole === 'fotografo') {
                $_SESSION['flash_ok'] = 'Se ha asignado el rol de fotógrafo.';
            } elseif ($newRole === 'cliente') {
                $_SESSION['flash_ok'] = 'Se ha asignado el rol de cliente.';
            } else {
                $_SESSION['flash_ok'] = 'Rol actualizado correctamente.';
            }
        } else {
            $_SESSION['flash_error'] = 'No se pudo actualizar el rol del usuario.';
        }
        header("Location: index.php?controller=admin&action=assignments");
        exit;
    }

    public function backupDb(){
        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo "Acceso denegado.";
            exit;
        }


        require_once __DIR__ . '/../models/BackupModel.php';
        $backupModel = new BackupModel($this->conn);
        $sqlDump = $backupModel->createBackupSql();
        $fileName = 'manego_backup_' . date('Ymd_His') . '.sql';

        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . strlen($sqlDump));
        echo $sqlDump;
        exit;
    }

    public function restoreDb(){
        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo "Acceso denegado.";
            exit;
        }


        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?controller=admin&action=assignments");
            exit;
        }
        $token = $_POST['csrf_token'] ?? '';
        if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(400);
            echo "Token CSRF inválido.";
            exit;
        }
        if (empty($_FILES['backup_file']['tmp_name']) || !is_uploaded_file($_FILES['backup_file']['tmp_name'])) {
            $_SESSION['flash_error'] = 'No se ha subido ningún archivo de respaldo.';
            header("Location: index.php?controller=admin&action=assignments");
            exit;
        }
        $sql = file_get_contents($_FILES['backup_file']['tmp_name']);
        require_once __DIR__ . '/../models/BackupModel.php';
        $backupModel = new BackupModel($this->conn);
        $ok = $backupModel->restoreFromSql($sql);

        if ($ok) {
            $_SESSION['flash_ok'] = 'La restauración de la base de datos se ha completado.';
        } else {
            $_SESSION['flash_error'] = 'Ocurrió un error al restaurar la base de datos.';
        }
        header("Location: index.php?controller=admin&action=assignments");
        exit;
    }

}
