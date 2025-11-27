<?php
class UserModel {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    /* ====== Helpers ====== */
    public function findByEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE LOWER(email)=LOWER(?) LIMIT 1";
        if ($st = $this->conn->prepare($sql)) {
            $st->bind_param("s", $email);
            $st->execute();
            return $st->get_result()->fetch_assoc();
        }
        return null;
    }

    public function findByNameAndApellido($nombre, $apellidoPaterno) {
        $sql = "SELECT * FROM usuarios WHERE LOWER(nombre)=LOWER(?) AND LOWER(apellidoPaterno)=LOWER(?) LIMIT 1";
        if ($st = $this->conn->prepare($sql)) {
            $st->bind_param("ss", $nombre, $apellidoPaterno);
            $st->execute();
            return $st->get_result()->fetch_assoc();
        }
        return null;
    }

    public function verifyLoginByName($nombre, $apellidoPaterno, $password) {
        $u = $this->findByNameAndApellido($nombre, $apellidoPaterno);
        if (!$u) return null;
        if (password_get_info($u['pass'])['algo'] === 0) {
            if ($u['pass'] === $password) {
                $newHash = password_hash($password, PASSWORD_BCRYPT);
                $this->updatePassword($u['id'], $newHash);
            } else {
                return null;
            }
        } elseif (!password_verify($password, $u['pass'])) {
            return null;
        }
        return $u;
    }

    /* ====== CRUD Usuarios (role=cliente) ====== */
    public function createClient($data) {
        // Evita duplicado por email si lo envían
        if (!empty($data['email']) && $this->findByEmail($data['email'])) {
            throw new Exception("El correo ya está registrado.");
        }
        $sql = "INSERT INTO usuarios (nombre, apellidoPaterno, apellidoMaterno, fechaNac, email, numTelefono, pass, role)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'cliente')";
        if ($st = $this->conn->prepare($sql)) {
            $hash = password_hash($data['pass'], PASSWORD_BCRYPT);
            $st->bind_param(
                "sssssss",
                $data['nombre'],
                $data['apellidoPaterno'],
                $data['apellidoMaterno'],
                $data['fechaNac'],
                $data['email'],
                $data['numTelefono'],
                $hash
            );
            return $st->execute();
        }
        return false;
    }

    public function getById($id) {
        $sql = "SELECT id, nombre, apellidoPaterno, apellidoMaterno, fechaNac, email, numTelefono, role, created_at
                FROM usuarios WHERE id=? LIMIT 1";
        if ($st = $this->conn->prepare($sql)) {
            $st->bind_param("i", $id);
            $st->execute();
            return $st->get_result()->fetch_assoc();
        }
        return null;
    }

    public function updateClient($id, $data) {
        $sql = "UPDATE usuarios SET nombre=?, apellidoPaterno=?, apellidoMaterno=?, fechaNac=?, email=?, numTelefono=? WHERE id=?";
        if ($st = $this->conn->prepare($sql)) {
            $st->bind_param(
                "ssssssi",
                $data['nombre'],
                $data['apellidoPaterno'],
                $data['apellidoMaterno'],
                $data['fechaNac'],
                $data['email'],
                $data['numTelefono'],
                $id
            );
            $ok = $st->execute();
            if (!$ok) return false;
        } else {
            return false;
        }
        if (!empty($data['pass'])) {
            $hash = password_hash($data['pass'], PASSWORD_BCRYPT);
            if (!$this->updatePassword($id, $hash)) return false;
        }
        return true;
    }

    public function updatePassword($id, $hash) {
        $sql = "UPDATE usuarios SET pass=? WHERE id=?";
        if ($st = $this->conn->prepare($sql)) {
            $st->bind_param("si", $hash, $id);
            return $st->execute();
        }
        return false;
    }

    public function deleteClient($id) {
        $sql = "DELETE FROM usuarios WHERE id=?";
        if ($st = $this->conn->prepare($sql)) {
            $st->bind_param("i", $id);
            return $st->execute();
        }
        return false;
    }


    public function getAll(){
        $sql = "SELECT id, nombre, apellidoPaterno, apellidoMaterno, fechaNac, email, numTelefono, role, created_at
                FROM usuarios
                ORDER BY created_at ASC";
        $rows = [];
        if ($res = $this->conn->query($sql)) {
            while ($row = $res->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public function updateRole($id, $role){
        // Solo permitimos roles conocidos
        if (!in_array($role, ['admin','cliente','fotografo'], true)) {
            return false;
        }
        $sql = "UPDATE usuarios SET role=? WHERE id=?";
        if ($st = $this->conn->prepare($sql)) {
            $st->bind_param("si", $role, $id);
            $ok = $st->execute();
            $st->close();
            return $ok;
        }
        return false;
    }

}
