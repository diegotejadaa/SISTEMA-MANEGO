<?php
require_once __DIR__ . '/../models/PackageModel.php';

class AdminPackageController {
    private $conn;
    private $pkg;
    public function __construct($connection){
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->conn = $connection;
        $this->pkg = new PackageModel($connection);
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        if (empty($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','fotografo'])) {
            http_response_code(403);
            echo "Acceso denegado."; exit;
        }
    }

    public function options(){
        $view_title = "Opciones de administrador";
        include __DIR__ . '/../views/admin/opcionesAdmin.php';
    }

    public function list(){
        $view_title = "Paquetes fotográficos";
        $paquetes = $this->pkg->all();
        include __DIR__ . '/../views/admin/package_list.php';
    }

    public function create(){
        $view_title = "Crear paquete";
        $error = null; $ok = null;
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if(!$this->validCsrf($_POST['csrf_token'] ?? '')){
                $error = "Token inválido, recarga la página.";
            } else {
                $data = [
                    'nombrePack' => trim($_POST['nombrePack'] ?? ''),
                    'descripcion' => trim($_POST['descripcion'] ?? ''),
                    'precio' => trim($_POST['precio'] ?? '0'),
                    'detalles' => trim($_POST['detalles'] ?? ''),
                    'imagen_url' => trim($_POST['imagen_url'] ?? ''),
                    'activo' => isset($_POST['activo']) ? 1 : 0
                ];
                
                // Manejo de imagen subida (opcional)
                if (!empty($_FILES['imagen']['name'])) {
                    $targetDir = "uploads/paquetes/";
                    if (!is_dir($targetDir)) { @mkdir($targetDir, 0777, true); }
                    $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                    $base = pathinfo($_FILES['imagen']['name'], PATHINFO_FILENAME);
                    $safe = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $base);
                    $filename = time() . "_" . $safe . "." . strtolower($ext);
                    $dest = $targetDir . $filename;
                    if (is_uploaded_file($_FILES['imagen']['tmp_name']) && move_uploaded_file($_FILES['imagen']['tmp_name'], $dest)) {
                        $data['imagen_url'] = $dest;
                    }
                }
    if($data['nombrePack']==='' or $data['descripcion']===''){
                    $error = "Nombre y descripción son obligatorios.";
                } else {
                    if($this->pkg->create($data)){
                        $ok = "Paquete creado correctamente.";
                    } else {
                        $error = "No se pudo crear el paquete.";
                    }
                }
            }
        }
        include __DIR__ . '/../views/admin/package_create.php';
    }

    public function edit(){
        $view_title = "Editar paquete";
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $error = null; $ok = null;
        $paquete = $this->pkg->find($id);
        if(!$paquete){ http_response_code(404); echo "No encontrado"; exit; }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if(!$this->validCsrf($_POST['csrf_token'] ?? '')){
                $error = "Token inválido, recarga la página.";
            } else {
                $data = [
                    'nombrePack' => trim($_POST['nombrePack'] ?? ''),
                    'descripcion' => trim($_POST['descripcion'] ?? ''),
                    'precio' => trim($_POST['precio'] ?? '0'),
                    'detalles' => trim($_POST['detalles'] ?? ''),
                    'imagen_url' => trim($_POST['imagen_url'] ?? ''),
                    'activo' => isset($_POST['activo']) ? 1 : 0
                ];
                // Imagen (opcional): si se sube nueva, reemplaza; si no, mantiene la existente
                if (!empty($_FILES['imagen']['name'])) {
                    $targetDir = "uploads/paquetes/";
                    if (!is_dir($targetDir)) { @mkdir($targetDir, 0777, true); }
                    $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                    $base = pathinfo($_FILES['imagen']['name'], PATHINFO_FILENAME);
                    $safe = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $base);
                    $filename = time() . "_" . $safe . "." . strtolower($ext);
                    $dest = $targetDir . $filename;
                    if (is_uploaded_file($_FILES['imagen']['tmp_name']) && move_uploaded_file($_FILES['imagen']['tmp_name'], $dest)) {
                        $data['imagen_url'] = $dest;
                    } else {
                        $data['imagen_url'] = $paquete['imagen_url'] ?? null;
                    }
                } else {
                    $data['imagen_url'] = $paquete['imagen_url'] ?? null;
                }
    
                if($data['nombrePack']==='' or $data['descripcion']===''){
                    $error = "Nombre y descripción son obligatorios.";
                } else {
                    if($this->pkg->update($id, $data)){
                        $ok = "Paquete actualizado.";
                        $paquete = $this->pkg->find($id);
                    } else {
                        $error = "No se pudo actualizar.";
                    }
                }
            }
        }
        include __DIR__ . '/../views/admin/package_edit.php';
    }

    public function delete(){
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){ http_response_code(405); exit('Método no permitido'); }
        if(!$this->validCsrf($_POST['csrf_token'] ?? '')){
            $_SESSION['flash_error'] = "Token inválido.";
        } else {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if($this->pkg->delete($id)){
                $_SESSION['flash_ok'] = "Paquete eliminado.";
            } else {
                $_SESSION['flash_error'] = "No se pudo eliminar.";
            }
        }
        header("Location: index.php?controller=adminpkg&action=list"); 
    }

    private function validCsrf($t){
        return hash_equals($_SESSION['csrf_token'] ?? '', $t);
    }
}
