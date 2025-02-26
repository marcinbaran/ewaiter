<?php

namespace App\Http\Resources;

use App\Http\Resources\Admin\ResourceResource;
use App\Models\Resource;
use App\Services\UploadService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\File;

trait PhotoTrait
{
    /**
     * @return string|null
     */
    public function getPhoto($public = true, array $croppaOptions = null)
    {
        $file = ($this instanceof Resource || $this instanceof Model || $this instanceof ResourceResource) ? $this->filename : $this->photo;

        return ! empty($file) ? ($this->isUrl($file) ? $file : $this->makeUrl($file, $this->resourcetable_type, $this->resourcetable_id, $croppaOptions, $public)) : null;
    }

    /**
     * @param mixed $file
     *
     * @return bool
     */
    protected function isUrl($file): bool
    {
        if (! is_string($file)) {
            return false;
        }

        return filter_var($file, FILTER_VALIDATE_URL);
    }

    /**
     * @param File|string $file
     * @param string $namespace
     *
     * @return string
     */
    protected function makeUrl($file, string $namespace, int $id, array $croppaOptions = null, $public = false): string
    {
        return (new UploadService())->makeUrl($file, $namespace, $this->resourcetable_id, $croppaOptions, $public);
    }

    public function getFileUrl(): string
    {
        return env('APP_URL').(new UploadService())->getPublicFolder($this->resourcetable_type, $this->resourcetable_id, true).'/'.$this->filename;
    }
}
