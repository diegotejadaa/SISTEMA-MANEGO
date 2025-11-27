<?php
require_once __DIR__ . '/../models/PackageModel.php';

class HomeController {
    private $conn;
    public function __construct($connection){ $this->conn = $connection; }
    public function index(){ $pkg = new PackageModel($this->conn); $paquetes = $pkg->all(); include __DIR__ . '/../views/home.php'; }
}
