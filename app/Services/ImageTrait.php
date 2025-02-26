<?php

namespace App\Services;

trait ImageTrait
{
    public function getImageFromPath(string $path)
    {
        switch (mime_content_type($path)) {
            case 'image/jpeg':
                return imagecreatefromjpeg($path);
            case 'image/png':
                return imagecreatefrompng($path);
            case 'image/gif':
                return imagecreatefromgif($path);
            case 'image/webp':
                return imagecreatefromwebp($path);
            default:
                throw new \Exception('Unsupported image type');
        }
    }
}
