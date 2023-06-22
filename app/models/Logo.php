<?php
class Logo {
    private $image;
    private const ALLOWED_FILE_TYPES = ['jpg', 'jpeg', 'png'];
    private const MAX_FILE_SIZE = 2 * 1024 * 1024;

    public function __construct($imagePath = '') {
        $this->image = $imagePath;
    }

    public function getImage() {
        return $this->image;
    }

    public function setImage($image) {
        $this->image = $image;
    }

    public function uploadLogo($file, string $resultFolderPath, string $resultFolderName, $isPreview) {
        $input_content = file_get_contents(getcwd() . '/source/index.html');
        $logo_tmp = $file['tmp_name'];
        $logo_type = pathinfo($file['name'], PATHINFO_EXTENSION);
        $logo_size = $file['size'];
        if (!in_array($logo_type, self::ALLOWED_FILE_TYPES) && !empty($file['tmp_name'])) {
            die('Error: Invalid file type.');
        }

        if ($logo_size > self::MAX_FILE_SIZE && !empty($file['tmp_name'])) {
            die('Error: File size exceeds the limit.');
        }
        move_uploaded_file($logo_tmp, $resultFolderPath . 'images/logo.' . $logo_type);
        if ($isPreview) {
        $this->setImage($resultFolderName . 'images/logo.' . $logo_type);
        }
        else {
        $input_content = str_replace('images/logo.png', 'images/logo.' . $logo_type, $input_content);
        }
        file_put_contents($resultFolderPath . 'index.html', $input_content);
        
        $this->setImage($resultFolderName . 'images/logo.' . $logo_type);
    }
}
?>