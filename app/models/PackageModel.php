<?php 
class PackageModel {
    private $conn;

    public function __construct($connection){
        $this->conn = $connection;
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    /* Obtener paquete por ID (para detalle de modal / agendar) */
    public function getById($id){
        $sql = "SELECT * FROM paquetes WHERE idPack = ?";
        if ($st = $this->conn->prepare($sql)) {
            $st->bind_param("i", $id);
            $st->execute();
            $res = $st->get_result();
            $row = $res ? $res->fetch_assoc() : null;
            $st->close();
            return $row;
        }
        return null;
    }

    /* Crear paquete */
    public function create($data){
        $sql = "INSERT INTO paquetes (nombrePack, descripcion, precio, detalles, imagen_url) VALUES (?, ?, ?, ?, ?)";
        if ($st = $this->conn->prepare($sql)) {
            $precio = (float)($data['precio'] ?? 0);
            $img    = $data['imagen_url'] ?? null;
            $st->bind_param(
                "ssdss",
                $data['nombrePack'],   // s
                $data['descripcion'],  // s
                $precio,               // d
                $data['detalles'],     // s
                $img                   // s (puede ser NULL)
            );
            $ok = $st->execute();
            $st->close();
            return $ok;
        }
        return false;
    }

    /* Listar paquetes */
    public function all(){
        $sql = "SELECT idPack, nombrePack, descripcion, precio, detalles, imagen_url FROM paquetes ORDER BY idPack DESC";
        $res = $this->conn->query($sql);
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        if ($res) $res->free();
        return $rows;
    }

    /* Buscar por id */
    public function find($id){
        $sql = "SELECT idPack, nombrePack, descripcion, precio, detalles, imagen_url FROM paquetes WHERE idPack = ?";
        if ($st = $this->conn->prepare($sql)) {
            $st->bind_param("i", $id);
            $st->execute();
            $result = $st->get_result();
            $row = $result ? $result->fetch_assoc() : null;
            $st->close();
            return $row;
        }
        return null;
    }

    /* Actualizar paquete */
    public function update($id, $data){
        $sql = "UPDATE paquetes SET nombrePack = ?, descripcion = ?, precio = ?, detalles = ?, imagen_url = ? WHERE idPack = ?";
        if ($st = $this->conn->prepare($sql)) {
            $precio = (float)($data['precio'] ?? 0);
            $img    = $data['imagen_url'] ?? null;
            // Orden de parámetros debe coincidir con el SQL:
            // nombrePack(s), descripcion(s), precio(d), detalles(s), imagen_url(s), id(i)
            $st->bind_param(
                "ssdssi",
                $data['nombrePack'],   // s
                $data['descripcion'],  // s
                $precio,               // d
                $data['detalles'],     // s
                $img,                  // s (puede ser NULL)
                $id                    // i
            );
            $ok = $st->execute();
            $st->close();
            return $ok;
        }
        return false;
    }

    /* Eliminar paquete */
    public function delete($id){
        $sql = "DELETE FROM paquetes WHERE idPack = ?";
        if ($st = $this->conn->prepare($sql)) {
            $st->bind_param("i", $id);
            $ok = $st->execute();
            $st->close();
            return $ok;
        }
        return false;
    }
}
