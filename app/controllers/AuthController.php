<?php
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    private $conn;
    private $userModel;

    public function __construct($connection){
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->conn = $connection;
        $this->userModel = new UserModel($connection);
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public function loginName(){
        $error = null;
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if(!$this->validCsrf($_POST['csrf_token'] ?? '')){
                $error = "Token inválido, recarga la página.";
            } else {
                $nombre = trim($_POST['nombre'] ?? '');
                $apellidoPaterno = trim($_POST['apellidoPaterno'] ?? '');
                $pass = trim($_POST['pass'] ?? '');
                if($nombre === '' || $apellidoPaterno === '' || $pass === ''){
                    $error = "Completa todos los campos.";
                } else {
                    $u = $this->userModel->verifyLoginByName($nombre, $apellidoPaterno, $pass);
                    if($u){
                        $_SESSION['id'] = $u['id'];
                        $_SESSION['nombre'] = $u['nombre'];
                        $_SESSION['role'] = $u['role'];
                        header("Location: index.php?controller=user&action=account"); exit;
                    } else {
                        $error = "Datos incorrectos.";
                    }
                }
            }
        }
        include __DIR__ . '/../views/auth/login_name.php';
    }

    public function logout(){
        session_destroy();
        header("Location: index.php"); exit;
    }

    private function validCsrf($t){
        return hash_equals($_SESSION['csrf_token'] ?? '', $t);
    }
}
