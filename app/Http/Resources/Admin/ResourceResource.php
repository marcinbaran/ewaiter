<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\PhotoTrait;
use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceResource extends JsonResource
{
    use ResourceTrait,
        PhotoTrait;

    /**
     * @var int Default limit items per page
     */
    public const LIMIT = 20;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        $array = [
            'id' => $this->id,
            'mime_type' => $this->mime_type,
            'filename' => $this->filename,
            'path' => $this->getPhoto(),
        ];

        return $array;
    }
}
