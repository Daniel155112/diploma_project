<?php
class MainController {
    private $logoModel;
    private $menuItemsModel;
    private $notificationMethodModel;
    private $zipArchiveModel;
    private $resultFolderPath;
    private $resultFolderName;

    public function __construct() {
        session_start();
        if (!isset($_SESSION['initialized'])) {
        $_SESSION['initialized'] = true;
        $this->generateCsrfToken();

        $this->setResultFolder();
        $this->copyOtherFiles();

        $this->logoModel = new Logo();
        $this->menuItemsModel = new MenuItems();
        $this->notificationMethodModel = new NotificationMethod();

        $_SESSION['logo'] = $this->logoModel;
        $_SESSION['menuItems'] = $this->menuItemsModel;
        $_SESSION['notificationMethod'] = $this->notificationMethodModel;
        } else {
        $this->resultFolderName = $_SESSION['resultFolderName'];
        $this->resultFolderPath = $_SESSION['resultFolderPath'];
        $this->logoModel = $_SESSION['logo'];
        $this->menuItemsModel = $_SESSION['menuItems'];
        $this->notificationMethodModel = $_SESSION['notificationMethod'];
        }
    }

    private function generateCsrfToken() : void {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
        }
    }

    public function setResultFolder() : void {
        $this->resultFolderName = '/result_' . time() . '_' . rand(1, 9999999) . '/';
        $this->resultFolderPath = getcwd() . $this->resultFolderName;
        $_SESSION['resultFolderPath'] = $this->resultFolderPath;
        $_SESSION['resultFolderName'] = $this->resultFolderName;
        if (!file_exists($this->resultFolderPath)) {
            mkdir($this->resultFolderPath, 0755);
            mkdir($this->resultFolderPath . 'css', 0755);
            mkdir($this->resultFolderPath . 'js', 0755);
            mkdir($this->resultFolderPath . 'php', 0755);
            mkdir($this->resultFolderPath . 'images/', 0755);
            mkdir($this->resultFolderPath . 'images/menu', 0755);
            }
    }

    public function generate() : void {
        $this->zipArchiveModel = new ZipArchiveGenerator('coffee-shop.zip');
        $this->zipArchiveModel->downloadZipArchive($this->resultFolderPath);
    }

    public function existingClientGenerate() : void {
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die('Error: Invalid csrf token.');
            }

        $host = 'localhost';
        $username = 'diploma';
        $password = 'diplomX';
        $database = 'orders';

        $mysqli = new mysqli($host, $username, $password, $database);

        if ($mysqli->connect_error) {
            die('Connection failed: ' . $mysqli->connect_error);
        }

        $paymentId = $_POST['payment_id'];

        $query = "SELECT * FROM order_list WHERE payment_id = '$paymentId'";

        $result = $mysqli->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $remainingFreeGens = $row['remaining_free_gens'];
            if ($remainingFreeGens > 0) {
            $newRemainingFreeGens = $remainingFreeGens - 1;

            $updateQuery = "UPDATE order_list SET remaining_free_gens = $newRemainingFreeGens WHERE payment_id = '$paymentId'";
            $updateResult = $mysqli->query($updateQuery);

            $this->logoModel->uploadLogo($_FILES['logo'], $this->resultFolderPath, $this->resultFolderName, false);
    
            $this->menuItemsModel->removeItems();
            $this->menuItemsModel->processMenuItems($_POST['menu_items'], $_FILES['menu_items'], $this->resultFolderPath, $this->resultFolderName, false);
    
            $notificationType = $_POST['notificationType'];
            if ($notificationType === 'email') {
            $this->notificationMethodModel->setMethod('email');
            $this->notificationMethodModel->setEmailNotifications($this->resultFolderPath, $_POST['email']);
            } else if ($notificationType === 'telegram') {
            $this->notificationMethodModel->setMethod('telegram');
            $this->notificationMethodModel->setTGNotifications($this->resultFolderPath, $_POST['telegramGroupId']);
            }

            $this->zipArchiveModel = new ZipArchiveGenerator('coffee-shop.zip');
            $this->zipArchiveModel->downloadZipArchive($this->resultFolderPath);

            $mysqli->close();
        } else {
            die('Error: no free usages available (0/3). Please use checkout option.');
        }
    } else {
        die('Error: entered payment ID is not found');
    }
    }
    public function payment() : void {
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die('Error: Invalid csrf token.');
            }
            $this->logoModel->uploadLogo($_FILES['logo'], $this->resultFolderPath, $this->resultFolderName, false);
    
            $this->menuItemsModel->removeItems();
            $this->menuItemsModel->processMenuItems($_POST['menu_items'], $_FILES['menu_items'], $this->resultFolderPath, $this->resultFolderName, false);
    
            $notificationType = $_POST['notificationType'];
            if ($notificationType === 'email') {
            $this->notificationMethodModel->setMethod('email');
            $this->notificationMethodModel->setEmailNotifications($this->resultFolderPath, $_POST['email']);
            } else if ($notificationType === 'telegram') {
            $this->notificationMethodModel->setMethod('telegram');
            $this->notificationMethodModel->setTGNotifications($this->resultFolderPath, $_POST['telegramGroupId']);
            }
    }

    public function preview() : void {
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die('Error: Invalid csrf token.');
        }

        $this->logoModel->uploadLogo($_FILES['logo'], $this->resultFolderPath, $this->resultFolderName, true);

        $this->menuItemsModel->removeItems();
        $this->menuItemsModel->processMenuItems($_POST['menu_items'], $_FILES['menu_items'], $this->resultFolderPath, $this->resultFolderName, true);

        $notificationType = $_POST['notificationType'];
        if ($notificationType === 'email') {
        $this->notificationMethodModel->setMethod('email');
        } else if ($notificationType === 'telegram') {
        $this->notificationMethodModel->setMethod('telegram');
        }
    }

    private function copyOtherFiles() : void {
        copy(getcwd() . '/source/css/coffee.css', $this->resultFolderPath . 'css/coffee.css');
        copy(getcwd() . '/source/images/background.png', $this->resultFolderPath . 'images/background.png');
        copy(getcwd() . '/source/js/checkout.js', $this->resultFolderPath . 'js/checkout.js');
    }


    public function renderGeneratorView() : void {
        $notificationMethod = $this->notificationMethodModel;
        $menuItems = $this->menuItemsModel;
        include 'app/views/generator_view.php';
    }

    public function renderPayment() : void {
        include 'app/views/payment_view.php';
    }

    public function renderDownloadView() : void {
        include 'app/views/download_view.php';
    }

    public function renderPreview() : void {
        $logo = $this->logoModel;
        $menuItems = $this->menuItemsModel;

        include 'app/views/coffee_shop_view.php';
    }
}
?>