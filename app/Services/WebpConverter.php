<?php

namespace App\Services;

use App\Models\Resource;
use App\Models\ResourceSystem;

class WebpConverter
{
    use ImageTrait;

    public function __construct(
        private UploadService $uploadService
    ) {
    }

    public function convertResourceToWebpById(int $id)
    {
        $resource = Resource::find($id);
        if (! $resource) {
            return;
        }
        $this->convertResourceToWebp($resource);
    }

    public function convertAllResourcesToWebp()
    {
        $resources = Resource::where('mime_type', 'like', 'image/%')->get();
        $this->comment('Found '.$resources->count().' resources to convert');
        foreach ($resources as $resource) {
            if ($resource->mime_type == 'image/webp') {
                continue;
            }
            $this->convertResourceToWebp($resource);
        }
    }

    public function convertResourceToWebp(Resource|ResourceSystem $resource)
    {
        if ($this->canConvert($resource->mime_type)) {
            return;
        }
        $newFilename = $this->changeNameToWebp($resource->filename);

        $path = $this->uploadService->getPathToSave($resource->resourcetable_type, $resource->resourcetable_id, $resource->filename);
        $webpPath = $this->uploadService->getPathToSave($resource->resourcetable_type, $resource->resourcetable_id, $newFilename);

        if (! file_exists($path)) {
            return;
        }

        $this->convertImageToWebp($path, $webpPath, $resource->mime_type);

        $resource->filename = $newFilename;
        $resource->mime_type = 'image/webp';
        $resource->save();
    }

    public function convertImageToWebp(string $source, string $destination, string $mimeType = 'image/jpeg', $quality = 100, $deleteSource = true)
    {
        try {
            $img = $this->getImageFromPath($source);
        } catch (\Throwable $e) {
            return;
        }

        imagepalettetotruecolor($img);
        imagealphablending($img, true);
        imagesavealpha($img, true);
        imagewebp($img, $destination, $quality);
        imagedestroy($img);

        if ($deleteSource) {
            unlink($source);
        }

        return $destination;
    }

    private function canConvert(string $mimeType)
    {
        return
            ! in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif']) ||
            ! extension_loaded('gd') ||
            ! function_exists('imagewebp') ||
            ! config('upload.enable_webp_conversion', false);
    }

    private function changeNameToWebp(string $name, string $extenstion = ''): string
    {
        if (! $extenstion) {
            if (file_exists($name)) {
                $extenstion = pathinfo($name, PATHINFO_EXTENSION);
            } else {
                $arr = explode('.', $name);
                $extenstion = '.'.array_pop($arr);
            }
        }

        return str_replace($extenstion, '.webp', $name);
    }
}
