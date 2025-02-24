<?php
session_start();

// Load Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
use App\Config\Env;

// Instead of loading Dotenv directly, we create our Env instance.
// This loads the .env file (if present) and stores the variables.
$env = new Env(__DIR__."/..");

// Optionally, you can make $ENV globally available:
// $GLOBALS['config'] = $ENV;

// Autoload models and controllers
spl_autoload_register(function ($className) {
    $paths = [
        '../app/config/' . $className . '.php',
        '../app/models/' . $className . '.php',
        '../app/controllers/' . $className . '.php'
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Load configuration and database
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../app/helpers/Utils.php';

// Simple routing based on 'page' parameter anf 'action' parameter
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? null;

switch ($page) {
    // case '/':
    //     require_once '../app/controllers/HomeController.php';
    //     break;
    case null || '' || '/' || 'login':
        require_once '../app/controllers/AuthController.php';
        AuthController::loginForm();
        break;
    case 'google_signin':
        require_once '../app/controllers/AuthController.php';
        AuthController::signInWithGoogle(env: $env);
        break;
    case 'google_callback':
        require_once '../app/controllers/AuthController.php';
        AuthController::googleCallback(pdo: $pdo, env: $env);
        break;
    case 'login_process':
        require_once '../app/controllers/AuthController.php';
        AuthController::processLogin(pdo: $pdo);
        break;
    case 'logout':
        require_once '../app/controllers/AuthController.php';
        AuthController::logout();
        break;
    // case 'upload':
    //     require_once '../app/controllers/UploadController.php';
    //     UploadController::uploadForm();
    //     break;
    // case 'upload_process':
    //     require_once '../app/controllers/UploadController.php';
    //     UploadController::processUpload(pdo: $pdo);
    //     break;
    default:
        echo "404 - Page not found";
        break;
}
?>
