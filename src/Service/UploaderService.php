<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderService extends AbstractService
{
    public function uploadImage(UploadedFile $file): string
    {
        try {
            $fileName = uniqid('image-') . '.' . $file->guessExtension();
            $file->move($this->parameter->get('uploads_images_directory'), $fileName);

            return $fileName;
        } catch (\Exception $e) {
            throw new \Exception('An error occurred while uploading the image: ' . $e->getMessage());
        }
    }

    public function deleteImage(string $fileName): void
    {
        if ($fileName === 'default.png') {
            return;
        }
        try {
            $filePath = $this->parameter->get('uploads_images_directory') . '/' . $fileName;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        } catch (\Exception $e) {
            throw new \Exception('An error occurred while deleting the image: ' . $e->getMessage());
        }
    }
}
