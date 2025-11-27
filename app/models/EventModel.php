<?php
class EventModel {
    private $conn;

    public function __construct($connection){
        $this->conn = $connection;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function create($data){
        $sql = "INSERT INTO eventos (usuario_id, paquete_id, fecha_evento, hora_evento, ubicacion, notas, estado)
                VALUES (?, ?, ?, ?, ?, ?, 'pendiente')";
        if ($st = $this->conn->prepare($sql)){
            $uid   = (int)$data['usuario_id'];
            $pid   = (int)$data['paquete_id'];
            $fecha = $data['fecha_evento'];
            $hora  = $data['hora_evento'];
            $ubic  = $data['ubicacion'];
            $notas = $data['notas'];

            $st->bind_param("iissss", $uid, $pid, $fecha, $hora, $ubic, $notas);
            $ok = $st->execute();
            if (!$ok){
                $_SESSION['db_error'] = 'MySQL: '.$st->error;
            }
            $st->close();
            return $ok;
        } else {
            $_SESSION['db_error'] = 'MySQL: '.$this->conn->error;
        }
        return false;
    }

    /**
     * Verifica si una fecha ya está ocupada por algún evento
     * (exceptuando los cancelados / rechazados).
     */
    public function isDateTaken($fecha){
        $sql = "SELECT COUNT(*) AS total
                FROM eventos
                WHERE fecha_evento = ?
                  AND (estado IS NULL OR estado NOT IN ('cancelado','rechazado'))";
        if ($st = $this->conn->prepare($sql)){
            $st->bind_param('s', $fecha);
            $st->execute();
            $res = $st->get_result();
            $row = $res ? $res->fetch_assoc() : null;
            $st->close();
            return $row && (int)$row['total'] > 0;
        }
        return false;
    }

    /**
     * Mantiene compatibilidad: por defecto regresamos solo eventos no finalizados.
     */
    public function byUser($userId){
        return $this->byUserActive($userId);
    }

    /**
     * Eventos del usuario que aún están activos (no finalizados).
     */
    public function byUserActive($userId){
        $sql = "SELECT e.*, p.nombrePack AS paquete_nombre, p.precio, e.media_link
                FROM eventos e
                LEFT JOIN paquetes p ON p.idPack = e.paquete_id
                WHERE e.usuario_id = ?
                  AND (e.estado IS NULL OR e.estado <> 'finalizado')
                ORDER BY e.created_at DESC";
        if ($st = $this->conn->prepare($sql)){
            $st->bind_param('i', $userId);
            $st->execute();
            $res  = $st->get_result();
            $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
            $st->close();
            return $rows;
        }
        return [];
    }

    /**
     * Eventos del usuario que ya fueron finalizados.
     */
    public function byUserFinished($userId){
        $sql = "SELECT e.*, p.nombrePack AS paquete_nombre, p.precio, e.media_link
                FROM eventos e
                LEFT JOIN paquetes p ON p.idPack = e.paquete_id
                WHERE e.usuario_id = ?
                  AND e.estado = 'finalizado'
                ORDER BY e.fecha_evento DESC, e.hora_evento DESC, e.created_at DESC";
        if ($st = $this->conn->prepare($sql)){
            $st->bind_param('i', $userId);
            $st->execute();
            $res  = $st->get_result();
            $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
            $st->close();
            return $rows;
        }
        return [];
    }

    /**
     * Eventos para panel de administración (solo activos).
     */
    public function allForAdmin(){
        $sql = "SELECT e.*,
                       CONCAT(u.nombre, ' ', u.apellidoPaterno, ' ', u.apellidoMaterno) AS cliente_nombre,
                       u.email AS cliente_email,
                       u.numTelefono AS cliente_telefono,
                       p.nombrePack AS paquete_nombre
                FROM eventos e
                LEFT JOIN usuarios u ON u.id = e.usuario_id
                LEFT JOIN paquetes p ON p.idPack = e.paquete_id
                WHERE e.estado IS NULL OR e.estado <> 'finalizado'
                ORDER BY e.fecha_evento ASC, e.hora_evento ASC, e.created_at DESC";
        if ($st = $this->conn->prepare($sql)){
            $st->execute();
            $res  = $st->get_result();
            $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
            $st->close();
            return $rows;
        }
        return [];
    }

    /**
     * Eventos finalizados para módulo de multimedia (admin).
     */
    public function finishedForMedia(){
        $sql = "SELECT e.*,
                       CONCAT(u.nombre, ' ', u.apellidoPaterno, ' ', u.apellidoMaterno) AS cliente_nombre,
                       u.email AS cliente_email,
                       u.numTelefono AS cliente_telefono,
                       p.nombrePack AS paquete_nombre
                FROM eventos e
                LEFT JOIN usuarios u ON u.id = e.usuario_id
                LEFT JOIN paquetes p ON p.idPack = e.paquete_id
                WHERE e.estado = 'finalizado'
                ORDER BY e.fecha_evento DESC, e.hora_evento DESC, e.created_at DESC";
        if ($st = $this->conn->prepare($sql)){
            $st->execute();
            $res  = $st->get_result();
            $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
            $st->close();
            return $rows;
        }
        return [];
    }

    public function updateAdmin($id, $data){
        $sql = "UPDATE eventos
                   SET fecha_evento = ?, hora_evento = ?, ubicacion = ?, notas = ?, estado = ?, updated_at = NOW()
                 WHERE idEvento = ?";
        if ($st = $this->conn->prepare($sql)){
            $fecha  = $data['fecha_evento'];
            $hora   = $data['hora_evento'];
            $ubic   = $data['ubicacion'];
            $notas  = $data['notas'];
            $estado = $data['estado'];

            if (!in_array($estado, ['pendiente','aceptado','rechazado','finalizado'])) {
                $estado = 'pendiente';
            }

            $idEv = (int)$id;
            $st->bind_param('sssssi', $fecha, $hora, $ubic, $notas, $estado, $idEv);
            $ok = $st->execute();
            $st->close();
            return $ok;
        }
        return false;
    }

    /**
     * Actualiza / borra enlace de material multimedia para un evento.
     */
    public function updateMediaLink($id, $link){
        $sql = "UPDATE eventos
                   SET media_link = ?, updated_at = NOW()
                 WHERE idEvento = ?";
        if ($st = $this->conn->prepare($sql)){
            $idEv   = (int)$id;
            $linkVal = $link !== null ? $link : null;
            $st->bind_param('si', $linkVal, $idEv);
            $ok = $st->execute();
            $st->close();
            return $ok;
        }
        return false;
    }


    /**
     * Estadísticas: número de eventos por mes en un periodo.
     */
    public function statsByMonth($startDate, $endDate){
        $sql = "SELECT DATE_FORMAT(fecha_evento, '%Y-%m') AS ym,
                       DATE_FORMAT(fecha_evento, '%b %Y') AS label,
                       COUNT(*) AS total
                FROM eventos
                WHERE fecha_evento BETWEEN ? AND ?
                  AND estado IN ('aceptado','finalizado')
                GROUP BY ym, label
                ORDER BY ym ASC";
        if ($st = $this->conn->prepare($sql)){
            $st->bind_param('ss', $startDate, $endDate);
            $st->execute();
            $res  = $st->get_result();
            $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
            $st->close();
            return $rows;
        }
        return [];
    }

    /**
     * Estadísticas: número de eventos por paquete en un periodo.
     */
    public function statsByService($startDate, $endDate){
        $sql = "SELECT p.nombrePack AS paquete,
                       COUNT(*) AS total
                FROM eventos e
                LEFT JOIN paquetes p ON p.idPack = e.paquete_id
                WHERE e.fecha_evento BETWEEN ? AND ?
                  AND e.estado IN ('aceptado','finalizado')
                GROUP BY e.paquete_id, paquete
                ORDER BY total DESC";
        if ($st = $this->conn->prepare($sql)){
            $st->bind_param('ss', $startDate, $endDate);
            $st->execute();
            $res  = $st->get_result();
            $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
            $st->close();
            return $rows;
        }
        return [];
    }

    /**
     * Estadísticas: cantidad de eventos finalizados vs. próximos en un periodo.
     * Se consideran "próximos" todos los que NO están finalizados.
     */
    public function statsByStatus($startDate, $endDate){
        $sql = "SELECT
                    SUM(CASE WHEN estado = 'finalizado' THEN 1 ELSE 0 END) AS finalizados,
                    SUM(CASE WHEN estado IS NULL OR estado <> 'finalizado' THEN 1 ELSE 0 END) AS proximos
                FROM eventos
                WHERE fecha_evento BETWEEN ? AND ?
                  AND estado IN ('aceptado','finalizado')";
        if ($st = $this->conn->prepare($sql)){
            $st->bind_param('ss', $startDate, $endDate);
            $st->execute();
            $res  = $st->get_result();
            $row  = $res ? $res->fetch_assoc() : null;
            $st->close();
            if ($row) {
                return $row;
            }
        }
        return ['finalizados' => 0, 'proximos' => 0];
    }

    /**
     * Devuelve los eventos ACEPTADOS dentro de un rango de fechas,
     * con nombre del cliente, nombre del paquete, fecha, hora,
     * ubicación y notas.
     */
    public function acceptedEventsInRange($startDate, $endDate){
        $sql = "SELECT 
                    CONCAT(u.nombre, ' ', u.apellidoPaterno, ' ', u.apellidoMaterno) AS cliente_nombre,
                    p.nombrePack AS paquete_nombre,
                    e.fecha_evento,
                    e.hora_evento,
                    e.ubicacion,
                    e.notas
                FROM eventos e
                LEFT JOIN usuarios u ON u.id = e.usuario_id
                LEFT JOIN paquetes p ON p.idPack = e.paquete_id
                WHERE e.estado = 'aceptado'
                  AND e.fecha_evento BETWEEN ? AND ?
                ORDER BY e.fecha_evento ASC, e.hora_evento ASC, e.created_at ASC";
        if ($st = $this->conn->prepare($sql)){
            $st->bind_param('ss', $startDate, $endDate);
            $st->execute();
            $res  = $st->get_result();
            $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
            $st->close();
            return $rows;
        }
        return [];
    }


    public function deleteForUser($idEvento, $userId){
        $sql = "DELETE FROM eventos WHERE idEvento = ? AND usuario_id = ?";
        if ($st = $this->conn->prepare($sql)){
            $id  = (int)$idEvento;
            $uid = (int)$userId;
            $st->bind_param('ii', $id, $uid);
            $ok = $st->execute();
            $st->close();
            return $ok;
        }
        return false;
    }
}
