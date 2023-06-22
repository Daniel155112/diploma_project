<?php
class ZipArchiveGenerator {
    private $zipFileName;

  public function __construct(string $zipFileName) {
      $this->zipFileName = $zipFileName;
  }

  public function createZipArchive(string $resultFolderPath) {
    $dirPath = $resultFolderPath;
    $zip = new ZipArchive();

    if ($zip->open($this->zipFileName, ZipArchive::CREATE) === true) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file;
                $relativePath = substr($filePath, strlen($dirPath));
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
        return true;
    } else {
        return false;
    }
}

public function downloadZipArchive(string $resultFolderPath) {
    if ($this->createZipArchive($resultFolderPath)) {
        header('Content-Type: application/zip');
        header("Content-Disposition: attachment; filename=\"$this->zipFileName\"");
        header('Content-Length: ' . filesize($this->zipFileName));
        readfile($this->zipFileName);
        unlink($this->zipFileName);
    } else {
        echo 'Failed to create zip archive';
    }
}
}
?>