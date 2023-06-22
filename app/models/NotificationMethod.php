<?php
class NotificationMethod {

    private $method;

    public function __construct($method = '') {
        $this->method = $method;
    }

    public function getMethod() {
        return $this->method;
    }

    public function setMethod($method) {
        $this->method = $method;
    }

    public function setEmailNotifications(string $resultFolderPath, string $newEmail) : void {
        $fileContents = file_get_contents(getcwd() . '/source/php/notification.php');
        $fileContents = str_replace("'daniil.volozhaninov@gmail.com'", "'$newEmail'", $fileContents);

        $variables = '($notificationService->sendEmailNotification($emailRecipient, $emailSubject, $message)';

        $emailNotification = <<<EOT
        if $variables) {
          echo 'Email notification sent successfully.';
      } else {
          echo 'Failed to send email notification.';
      }
EOT;
        $fileContents = str_replace(['//Email Notification'], [$emailNotification], $fileContents);
        file_put_contents($resultFolderPath . 'php/notification.php', $fileContents);

        $this->copyFiles(getcwd() . '/source/phpmailer', $resultFolderPath . 'php');
}

    public function setTGNotifications(string $resultFolderPath, string $telegramGroupId) : void {
        $fileContents = file_get_contents(getcwd() . '/source/php/notification.php');
        $fileContents = str_replace('-966563715', "'$telegramGroupId'", $fileContents);
    
        $variables = '($notificationService->sendTelegramNotification($message)';

        $telegramNotification = <<<EOT
        if $variables) {
            echo 'Telegram notification sent successfully.';
          } else {
            echo 'Failed to send Telegram notification.';
          }
EOT;
    
        $fileContents = str_replace(['//TG Notification'], [$telegramNotification], $fileContents);
        file_put_contents($resultFolderPath . 'php/notification.php', $fileContents);

        $this->copyFiles(getcwd() . '/source/phpmailer', $resultFolderPath . 'php');
    }

    public function isEmailNotification() {
        return $this->method === 'email';
    }

    public function isTelegramNotification() {
        return $this->method === 'telegram';
    }

    private function copyFiles($sourceDir, $destDir) {
        if (!file_exists($destDir)) {
            mkdir($destDir, 0755, true);
        }
      
        $dirIterator = new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::SELF_FIRST);
      
        foreach ($iterator as $item) {
            $target = $destDir . '/' . $iterator->getSubPathName();
            
            if ($item->isDir()) {
                if (!file_exists($target)) {
                    mkdir($target);
                }
            } else {
                copy($item, $target);
            }
        }
      }
}
?>