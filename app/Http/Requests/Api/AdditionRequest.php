<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="Addition",
 *     type="object",
 *     title="Addition",
 *     description="Request schema for Addition"
 * )
 * @OA\Schema(
 *     schema="AdditionPost",
 *     type="object",
 *     title="Addition (POST)",
 *     description="Request schema for creating a new Addition",
 *     required={"name", "price"},
 *     @OA\Property(property="name", type="string", maxLength=255, description="Name of the addition"),
 *     @OA\Property(property="price", type="number", format="float", description="Price of the addition"),
 *     @OA\Property(property="addition_addition_group", type="array", @OA\Items(type="object",
 *         @OA\Property(property="id", type="integer", description="Addition group ID")
 *     ))
 * )
 * @OA\Schema(
 *     schema="AdditionPut",
 *     type="object",
 *     title="Addition (PUT)",
 *     description="Request schema for updating an existing Addition",
 *     required={"name", "price"},
 *     @OA\Property(property="name", type="string", maxLength=255, description="Name of the addition"),
 *     @OA\Property(property="price", type="number", format="float", description="Price of the addition"),
 *     @OA\Property(property="addition_addition_group", type="array", @OA\Items(type="object",
 *         @OA\Property(property="id", type="integer", description="Addition group ID")
 *     ))
 * )
 * @OA\Schema(
 *     schema="AdditionDelete",
 *     type="object",
 *     title="Addition (DELETE)",
 *     description="Request schema for deleting an Addition"
 * )
 */
class AdditionRequest extends FormRequest
{
    use RequestTrait;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     *
     * @OA\Parameter(
     *     name="itemsPerPage",
     *     in="query",
     *     required=false,
     *     @OA\Schema(type="integer", format="int32", minimum=1, maximum=50),
     *     description="Number of items per page"
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     required=false,
     *     @OA\Schema(type="integer", format="int32", minimum=1),
     *     description="Page number"
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="query",
     *     required=false,
     *     @OA\Schema(type="array", @OA\Items(type="integer")),
     *     description="Array of IDs"
     * )
     * @OA\Parameter(
     *     name="order.id",
     *     in="query",
     *     required=false,
     *     @OA\Schema(type="string", enum={"asc", "desc"}),
     *     description="Sort order for ID"
     * )
     * @OA\Parameter(
     *     name="order.price",
     *     in="query",
     *     required=false,
     *     @OA\Schema(type="string", enum={"asc", "desc"}),
     *     description="Sort order for price"
     * )
     * @OA\RequestBody(
     *     request="AdditionPostBody",
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/AdditionPost")
     * )
     * @OA\RequestBody(
     *     request="AdditionPutBody",
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/AdditionPut")
     * )
     * @OA\RequestBody(
     *     request="AdditionDeleteBody",
     *     required=false,
     *     @OA\JsonContent(ref="#/components/schemas/AdditionDelete")
     * )
     */
    private static $rules = [
        self::METHOD_GET => [
            'itemsPerPage' => 'integer|min:1|max:50',
            'page' => 'integer|min:1',
            'id' => 'array|min:1',
            'id.*' => 'integer|min:1',
            'order.id' => 'string|in:asc,desc',
            'order.price' => 'string|in:asc,desc',
        ],
        self::METHOD_POST => [
            'name' => 'required|string|max:255',
            'price' => 'numeric|min:0',
            'addition_addition_group.*.id' => 'nullable|min:1|exists:tenant.additions_groups,id',
        ],
        self::METHOD_PUT => [
            'name' => 'string|max:255',
            'price' => 'numeric|min:0',
            'addition_addition_group.*.id' => 'nullable|min:1|exists:tenant.additions_groups,id',
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
