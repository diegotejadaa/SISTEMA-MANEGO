<?php
class BackupModel {
    private $conn;

    public function __construct($connection){
        $this->conn = $connection;
    }

    /**
     * Genera un respaldo completo de la base de datos actual en formato SQL.
     */
    public function createBackupSql(){
        $dbName = '';
        if ($res = $this->conn->query("SELECT DATABASE() AS dbname")) {
            if ($row = $res->fetch_assoc()) {
                $dbName = $row['dbname'];
            }
        }

        $dump  = "-- Respaldo de base de datos {$dbName}\n";
        $dump .= "-- Generado: " . date('Y-m-d H:i:s') . "\n\n";
        $dump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        if ($tablesRes = $this->conn->query("SHOW TABLES")) {
            while ($tRow = $tablesRes->fetch_array()) {
                $table = $tRow[0];

                // Estructura
                if ($createRes = $this->conn->query("SHOW CREATE TABLE `{$table}`")) {
                    if ($createRow = $createRes->fetch_assoc()) {
                        $createSql = $createRow['Create Table'];
                        $dump .= "DROP TABLE IF EXISTS `{$table}`;\n";
                        $dump .= $createSql . ";\n\n";
                    }
                }

                // Datos
                if ($dataRes = $this->conn->query("SELECT * FROM `{$table}`")) {
                    while ($dataRow = $dataRes->fetch_assoc()) {
                        $columns = array_keys($dataRow);
                        $colsEscaped = array_map(function($c){
                            return "`" . str_replace("`", "``", $c) . "`";
                        }, $columns);
                        $values = array_values($dataRow);
                        $valsEscaped = array_map(function($v){
                            if ($v === null) {
                                return "NULL";
                            }
                            return "'" . $this->conn->real_escape_string($v) . "'";
                        }, $values);

                        $dump .= "INSERT INTO `{$table}` (" . implode(",", $colsEscaped) . ") VALUES (" . implode(",", $valsEscaped) . ");\n";
                    }
                    $dump .= "\n";
                }
            }
        }

        $dump .= "SET FOREIGN_KEY_CHECKS=1;\n";
        return $dump;
    }

    /**
     * Restaura la base de datos ejecutando el SQL dado.
     */
    public function restoreFromSql($sql){
        if ($sql === '' || $sql === null) {
            return false;
        }
        // Usamos multi_query para ejecutar múltiples sentencias.
        if (!$this->conn->multi_query($sql)) {
            return false;
        }
        // Vaciar todos los resultados intermedios.
        do {
            if ($result = $this->conn->store_result()) {
                $result->free();
            }
        } while ($this->conn->more_results() && $this->conn->next_result());

        if ($this->conn->error) {
            return false;
        }
        return true;
    }
}
