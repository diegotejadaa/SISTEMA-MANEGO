<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'manego';

$connection = new mysqli($host, $user, $pass, $db);
if ($connection->connect_error) { die('Error de conexión: ' . $connection->connect_error); }
$connection->set_charset('utf8mb4');
