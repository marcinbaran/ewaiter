<?php

namespace App\Services;

use App\Models\Resource;
use App\Models\ResourceSystem;
use Intervention\Image\Image;

class ThumbnailsGenerator
{
    use ImageTrait;

    public function __construct(
        private UploadService $uploadService
    ) {
    }

    public function generateThumbnailsForResource(Resource|ResourceSystem $resource)
    {
        $this->generateThumbnails($resource->resourcetable_type, $resource->resourcetable_id, $resource->filename);
    }

    public function generateThumbnailsForFile(string $path)
    {
        foreach (config('upload.thumbnails') as $key => $value) {
            $this->resizeImage($path, $value['width'], $value['height'], $value['is_main'], $key);
        }
    }

    public function generateThumbnails(string $namespace, string $id, string $filename)
    {
        $webpPath = $this->uploadService->getPathToSave($namespace, $id, $filename);
        $this->generateThumbnailsForFile($webpPath);
    }

    /**
     * Resize an image and keep the proportions.
     * @param string $filename
     * @param int $max_width
     * @param int $max_height
     * @return void|mixed
     */
    public function resizeImage($filename, $max_width, $max_height, $isMain, $suffix = '')
    {
        [$orig_width, $orig_height] = getimagesize($filename);

        $width = $orig_width;
        $height = $orig_height;

        // taller
        if ($height > $max_height) {
            $width = ($max_height / $height) * $width;
            $height = $max_height;
        }

        // wider
        if ($width > $max_width) {
            $height = ($max_width / $width) * $height;
            $width = $max_width;
        }

        $image_p = imagecreatetruecolor($width, $height);

        try {
            $image = $this->getImageFromPath($filename);
        } catch(\Throwable $e) {
            return;
        }

        $res = imagecopyresampled(
            $image_p,
            $image,
            0,
            0,
            0,
            0,
            $width,
            $height,
            $orig_width,
            $orig_height
        );

        if (! $isMain) {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $filename = str_replace('.'.$ext, '', $filename).'-'.$suffix.'.'.$ext;
            if (file_exists($filename)) {
                return;
            }
        }

        imagewebp($image_p, $filename, 100);

        return $image_p;
    }
}
