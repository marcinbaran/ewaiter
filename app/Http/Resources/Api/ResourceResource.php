<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\PhotoTrait;
use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="ResourceResource",
 *     type="object",
 *     title="Resource",
 *     description="Resource representing a media file"
 * )
 */
class ResourceResource extends JsonResource
{
    use ResourceTrait,
        PhotoTrait;

    /**
     * @var int Default limit items per page
     */
    public const LIMIT = 20;

    /**
     * @var array|null
     */
    private $croppaOptions = null;

    /**
     * @var array
     */


    private static $defaultCroppaOptions = [
        'dishes' => ['width' => 800, 'height' => null, 'resize'],
        'food_categories' => ['width' => null, 'height' => null, 'resize'],
        'promotions' => ['width' => 800, 'height' => 450, 'resize'],
        'restaurants' => ['width' => 800, 'height' => 450, 'resize'],
    ];

    /**
     * Set options to crop.
     *
     * @param array $params
     */
    public function init($params = null)
    {
        if (null === $params || is_array($params)) {
            $this->croppaOptions = empty($params) ? null : $params[0];
        }
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */


    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     *
     * @OA\Property(
     *     property="id",
     *     type="integer",
     *     description="The ID of the resource"
     * )
     * @OA\Property(
     *     property="mime_type",
     *     type="string",
     *     description="The MIME type of the resource"
     * )
     * @OA\Property(
     *     property="filename",
     *     type="string",
     *     description="The filename of the resource"
     * )
     * @OA\Property(
     *     property="path",
     *     type="string",
     *     description="The URL path to access the resource"
     * )
     */
    public function toArray($request): array
    {
        $array = [
            'id' => $this->id,
            'mime_type' => $this->mime_type,
            'filename' => $this->filename,
            'path' => env('APP_URL').$this->getPhoto(true, $this->getCroppaOptions()),
        ];

        return $array;
    }

    /**
     * @param string $resourcetableType
     *
     * @return array|null
     */
    public static function returnDefaultCroppaOptions(string $resourcetableType)
    {
        return self::$defaultCroppaOptions[$resourcetableType] ?? null;
    }

    /**
     * @return array|null
     */
    private function getCroppaOptions()
    {
        return $this->croppaOptions ? $this->croppaOptions : self::returnDefaultCroppaOptions($this->resourcetable_type);
    }
}
