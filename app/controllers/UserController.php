<?php
require_once __DIR__ . '/../models/UserModel.php';

class UserController {
    private $conn;
    private $userModel;

    public function __construct($connection) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->conn = $connection;
        $this->userModel = new UserModel($connection);
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public function register() {
        $error = null; $msg = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validCsrf($_POST['csrf_token'] ?? '')) {
                $error = "Token inválido, recarga la página.";
            } else {
                $required = ['nombre','apellidoPaterno','apellidoMaterno','fechaNac','email','numTelefono','pass'];
                $data = [];
                foreach ($required as $k) { $data[$k] = trim($_POST[$k] ?? ''); }
                if (in_array('', $data, true)) {
                    $error = "Completa todos los campos.";
                } else {
                    try {
                        if ($this->userModel->createClient($data)) {
                            // No auto-login: exige iniciar sesión antes de ver "Mi cuenta"
                            $msg = "Registro exitoso. Ahora inicia sesión para ver tu cuenta.";
                        } else {
                            $error = "No se pudo registrar. Intenta de nuevo.";
                        }
                    } catch (Exception $e) {
                        $error = $e->getMessage();
                    }
                }
            }
        }
        $view_title = "Registro de cliente";
        include __DIR__ . '/../views/register.php';
    }


    // Método auxiliar para mantener la ruta 'store' usada en el formulario original
    public function store() {
        // Reutilizamos toda la lógica de register() para procesar el POST
        $this->register();
    }

    public function account() {
        if (!$this->requireLoginCliente()) return;
        $user = $this->userModel->getById($_SESSION['id']);
        $view_title = "Mi cuenta";
        include __DIR__ . '/../views/account.php';
    }

    public function update() {
        if (!$this->requireLoginCliente()) return;
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?controller=user&action=account"); exit;
        }
        if (!$this->validCsrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = "Token inválido, intenta de nuevo.";
            header("Location: index.php?controller=user&action=account"); exit;
        }
        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellidoPaterno' => trim($_POST['apellidoPaterno'] ?? ''),
            'apellidoMaterno' => trim($_POST['apellidoMaterno'] ?? ''),
            'fechaNac' => trim($_POST['fechaNac'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'numTelefono' => trim($_POST['numTelefono'] ?? ''),
            'pass' => trim($_POST['pass'] ?? '')
        ];
        if ($this->userModel->updateClient($_SESSION['id'], $data)) {
            $_SESSION['flash_ok'] = "Datos actualizados.";
        } else {
            $_SESSION['flash_error'] = "No se pudieron actualizar los datos.";
        }
        header("Location: index.php?controller=user&action=account");
    }

    public function delete() {
        if (!$this->requireLoginCliente()) return;
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?controller=user&action=account"); exit;
        }
        if (!$this->validCsrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = "Token inválido, intenta de nuevo.";
            header("Location: index.php?controller=user&action=account"); exit;
        }
        if ($this->userModel->deleteClient($_SESSION['id'])) {
            session_destroy();
            session_start();
            $_SESSION['flash_ok'] = "Cuenta eliminada.";
            header("Location: index.php");
        } else {
            $_SESSION['flash_error'] = "No se pudo eliminar la cuenta.";
            header("Location: index.php?controller=user&action=account");
        }
    }

    private function requireLoginCliente() {
        if (empty($_SESSION['id']) || empty($_SESSION['role']) || !in_array($_SESSION['role'], ['cliente','admin','fotografo'])) {
            header("Location: index.php?controller=auth&action=loginName");
            return false;
        }
        return true;
    }
    private function validCsrf($token) {
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }
}