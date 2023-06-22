<?php

require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailNotificationService
{
    private $mailer;

    public function __construct($host, $username, $password, $port, $smtpSecure)
    {
        $this->mailer = new PHPMailer;
        $this->mailer->isSMTP();
        $this->mailer->Host = $host;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $username;
        $this->mailer->Password = $password;
        $this->mailer->SMTPSecure = $smtpSecure;
        $this->mailer->Port = $port;
    }

    public function sendEmailNotification($recipientEmail, $subject, $message)
    {
        $this->mailer->setFrom('coffeeshopnotification@ukr.net', 'Coffee order notification');
        $this->mailer->addAddress($recipientEmail);
        $this->mailer->Subject = $subject;
        $this->mailer->Body = $message;

        if ($this->mailer->send()) {
            return true;
        } else {
            return false;
        }
    }
}

class TelegramNotificationService
{
    private $botToken;
    private $chatId;

    public function __construct($botToken, $chatId)
    {
        $this->botToken = $botToken;
        $this->chatId = $chatId;
    }

    private function buildQueryString($data)
    {
        $query = '';
        foreach ($data as $key => $value) {
            $query .= rawurlencode($key) . '=' . rawurlencode($value) . '&';
        }
        return rtrim($query, '&');
    }

    public function sendTelegramNotification($message)
    {
        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";

        $queryData = [
            'chat_id' => $this->chatId,
            'text' => $message,
        ];

        $queryString = $this->buildQueryString($queryData);

        $fullUrl = $url . '?' . $queryString;

        $context = stream_context_create([
            'http' => [
                'ignore_errors' => true,
            ],
        ]);

        $response = file_get_contents($fullUrl, false, $context);

        if ($response === false) {
            return false;
        }

        $responseData = json_decode($response, true);

        if ($responseData['ok']) {
            return true;
        }

        return false;
    }
}

class NotificationService
{
    private $emailService;
    private $telegramService;

    public function __construct($emailService, $telegramService)
    {
        $this->emailService = $emailService;
        $this->telegramService = $telegramService;
    }

    public function sendEmailNotification($recipientEmail, $subject, $message)
    {
        return $this->emailService->sendEmailNotification($recipientEmail, $subject, $message);
    }

    public function sendTelegramNotification($message)
    {
        return $this->telegramService->sendTelegramNotification($message);
    }
}

$orderId = $_POST['orderId'];
$checkoutData = $_POST['checkoutData'];
$totalPrice = 0;
$message = "Order ID: " . $orderId . "\n";

foreach ($checkoutData as $itemData) {
    $itemName = $itemData[0];
    $itemQuantity = $itemData[1];
    $itemPrice = $itemData[2];
    $itemTotalPrice = $itemQuantity * $itemPrice;
    $totalPrice += $itemTotalPrice;
    $message .= "$itemName x $itemQuantity - $itemTotalPrice UAH\n";
}

$message .= "Total Price: $totalPrice UAH";

$emailHost = 'smtp.ukr.net';
$emailUsername = 'coffeeshopnotification@ukr.net';
$emailPassword = 'oFIXf7SSTVgNUgzW';
$emailPort = 465;
$emailSmtpSecure = 'ssl';

$emailService = new EmailNotificationService($emailHost, $emailUsername, $emailPassword, $emailPort, $emailSmtpSecure);

$telegramBotToken = '6120327317:AAHEw8osHhteeTP9zgRchRbHFmnzWCotegw';
$telegramChatId = -966563715;

$telegramService = new TelegramNotificationService($telegramBotToken, $telegramChatId);

$notificationService = new NotificationService($emailService, $telegramService);

$emailRecipient = 'daniil.volozhaninov@gmail.com';
$emailSubject = 'New order has arrived!';

//Email Notification

//TG Notification
?>