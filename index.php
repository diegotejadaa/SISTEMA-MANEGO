<?php
require_once __DIR__ . '/config/db_connection.php';

$controller = $_GET['controller'] ?? 'home';
$action     = $_GET['action'] ?? 'index';

switch ($controller) {
    case 'auth':
        require_once __DIR__ . '/app/controllers/AuthController.php';
        $c = new AuthController($connection);
        $allowed = ['loginName','logout'];
        if (!in_array($action, $allowed)) { http_response_code(404); exit('Not found'); }
        $c->{$action}();
        break;

    case 'user':
        require_once __DIR__ . '/app/controllers/UserController.php';
        $c = new UserController($connection);
        $allowed = ['register','store','account','update','delete'];
        if (!in_array($action, $allowed)) { http_response_code(404); exit('Not found'); }
        $c->{$action}();
        break;

    case 'adminpkg':
        require_once __DIR__ . '/app/controllers/AdminPackageController.php';
        $c = new AdminPackageController($connection);
        $allowed = ['options','opcionesAdmin','list','create','edit','delete','store','update','destroy'];
        if (!in_array($action, $allowed)) { http_response_code(404); exit('Not found'); }
        if ($action === 'opcionesAdmin') { $action = 'options'; }
        $c->{$action}();
        break;

    case 'admin':
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $c = new AdminController($connection);
        $allowed = ['profile','media','mediaUpdate','events','reports','acceptedEventsPdf','assignments','toggleRole','backupDb','restoreDb'];
        if (!in_array($action, $allowed)) { http_response_code(404); exit('Not found'); }
        $c->{$action}();
        break;

    case 'event':
        require_once __DIR__ . '/app/controllers/EventController.php';
        $c = new EventController($connection);
        $allowed = ['create','store','my','finished','adminPanel','adminUpdate','delete'];
        if (!in_array($action, $allowed)) { http_response_code(404); exit('Not found'); }
        $c->{$action}();
        break;

    case 'home':
        require_once __DIR__ . '/app/controllers/HomeController.php';
        $c = new HomeController($connection);
        if ($action !== 'index') { http_response_code(404); exit('Not found'); }
        $c->index();
        break;

    default:
        require_once __DIR__ . '/app/controllers/HomeController.php';
        $c = new HomeController($connection);
        $c->index();
}
