<?php

namespace App\Tests\FileManagement;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TestImage
{
    private $data;
    private $path;
    private $name;
    private $size;
    private $extension;

    public function __construct($path, $isRandom = false)
    {
        if (!file_exists($path) && !$isRandom) {
           throw new FileNotFoundException();
        }

        if ($isRandom) {
            $path = $this->getRandomImage($path);
        }

        $this->path = $path;

        try {
            $this->size = filesize($path);
            $this->name = pathinfo($path, PATHINFO_FILENAME);
            $this->extension = pathinfo($path, PATHINFO_EXTENSION);
            $contentType = mime_content_type($path);

            $this->data = new UploadedFile($path, $this->name, $contentType);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    private function getRandomImage($path): string
    {
        if (!file_exists($path) || !is_dir($path)) {
            throw new FileNotFoundException();
        }

        $directoryFiles = array_values(array_diff(scandir($path), array('..', '.')));;
        $randomIndex = array_rand($directoryFiles, 1);

        return $path . $directoryFiles[$randomIndex];
    }

    public function getImageUpload()
    {
        return $this->data;
    }

    public function setImageUpload($data)
    {
        $this->data = $data;

        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getExtension()
    {
        return $this->extension;
    }

    public function setExtension($extension)
    {
        return $this->extension = $extension;
    }
}