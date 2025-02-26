<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="PlayerIdRequestGET",
 *     type="object",
 *     @OA\Property(property="itemsPerPage", type="integer", example=10, description="Number of items per page."),
 *     @OA\Property(property="page", type="integer", example=1, description="Page number for pagination."),
 *     @OA\Property(property="id", type="array", @OA\Items(type="integer", example=1, description="Array of player IDs.")),
 *     @OA\Property(property="user", type="array", @OA\Items(type="integer", example=1, description="Array of user IDs associated with the players.")),
 *     @OA\Property(property="table", type="array", @OA\Items(type="integer", example=1, description="Array of table IDs associated with the players.")),
 *     @OA\Property(property="withTable", type="boolean", example=true, description="Whether to include table information."),
 *     @OA\Property(property="order", type="object",
 *         @OA\Property(property="id", type="string", enum={"asc", "desc"}, description="Order by player ID."),
 *         @OA\Property(property="createdAt", type="string", enum={"asc", "desc"}, description="Order by creation date.")
 *     ),
 *     description="Request schema for the GET method of PlayerIdRequest."
 * )
 * @OA\Schema(
 *     schema="PlayerIdRequestPOST",
 *     type="object",
 *     @OA\Property(property="playerId", type="string", maxLength=250, description="Unique identifier for the player."),
 *     @OA\Property(property="user.id", type="integer", example=1, description="User ID associated with the player."),
 *     @OA\Property(property="deviceInfo", type="string", description="Information about the device."),
 *     description="Request schema for the POST method of PlayerIdRequest."
 * )
 * @OA\Schema(
 *     schema="PlayerIdRequestPUT",
 *     type="object",
 *     @OA\Property(property="playerId", type="string", maxLength=250, description="Unique identifier for the player."),
 *     @OA\Property(property="user.id", type="integer", example=1, description="User ID associated with the player."),
 *     @OA\Property(property="deviceInfo", type="string", description="Information about the device."),
 *     description="Request schema for the PUT method of PlayerIdRequest."
 * )
 * @OA\Schema(
 *     schema="PlayerIdRequestDELETE",
 *     type="object",
 *     description="Request schema for the DELETE method of PlayerIdRequest."
 * )
 */
class PlayerIdRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        self::METHOD_GET => [
            'itemsPerPage' => 'integer|min:1|max:50',
            'page' => 'integer|min:1',
            'id' => 'array|min:1',
            'id.*' => 'integer|min:1',
            'user' => 'array|min:1',
            'user.*' => 'integer|min:1|exists:tenant.users,id',
            'table' => 'array|min:1',
            'table.*' => 'integer|min:1|exists:tenant.tables,id',
            'withTable' => 'boolean',
            'order.id' => 'string|in:asc,desc',
            'order.createdAt' => 'string|in:asc,desc',
        ],
        self::METHOD_POST => [
            'playerId' => 'required|string|max:250',
            'user.id' => 'integer|exists:tenant.users,id',
            'deviceInfo' => 'string',
        ],
        self::METHOD_PUT => [
            'playerId' => 'string|max:250',
            'user.id' => 'integer|exists:tenant.users,id',
            'deviceInfo' => 'string',
        ],
        self::METHOD_DELETE => [
        ],
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return self::$rules[$this->getMethod()] ?? [];
    }
}
