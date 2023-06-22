<?php

$route = $_GET['route'] ?? '';

require_once 'app/models/Logo.php';
require_once 'app/models/MenuItems.php';
require_once 'app/models/NotificationMethod.php';
require_once 'app/models/ZipArchiveGenerator.php';
require_once 'app/controllers/MainController.php';

if (!isset($_SESSION['controller'])) {
    $_SESSION['controller'] = new MainController();
}
$controller = $_SESSION['controller'];

switch ($route) {
    case 'process-generating-form':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'generate') {
            $controller->generate();
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'preview') {
            if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die('Error: Invalid csrf token.');
            }
            $controller->preview();
            $controller->renderPreview();
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'payment') {
            $controller->payment();
            $controller->renderPayment();
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'existing-client-generate') {
            $controller->existingClientGenerate();
        }
        break;
    case 'payment-callback-form':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'payment-successful') {
            $controller->renderDownloadView();
        }
        else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'payment-failed') {
            $controller->renderGeneratorView();
        }
        break;
    default:
        $controller->renderGeneratorView();
        break;
}
?>