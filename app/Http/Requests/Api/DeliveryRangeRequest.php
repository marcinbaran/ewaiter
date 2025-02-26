<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="DeliveryRange_GET",
 *     type="object",
 *     @OA\Property(property="itemsPerPage", type="integer", example=20, description="Number of items per page"),
 *     @OA\Property(property="page", type="integer", example=1, description="Page number"),
 *     @OA\Property(property="id", type="array", @OA\Items(type="integer"), description="Array of Delivery Range IDs"),
 *     @OA\Property(property="order.id", type="string", enum={"asc", "desc"}, description="Order by ID"),
 *     @OA\Property(property="order.name", type="string", enum={"asc", "desc"}, description="Order by name"),
 * )
 *
 * @OA\Schema(
 *     schema="DeliveryRange_POST",
 *     type="object",
 *     @OA\Property(property="name", type="string", maxLength=255, description="Name of the delivery range"),
 *     @OA\Property(property="range_from", type="integer", description="Starting range for delivery"),
 *     @OA\Property(property="range_to", type="integer", description="Ending range for delivery"),
 *     @OA\Property(property="min_value", type="number", format="float", description="Minimum value for delivery"),
 *     @OA\Property(property="free_from", type="number", format="float", description="Free delivery from this value"),
 *     @OA\Property(property="cost", type="number", format="float", description="Cost of delivery"),
 *     @OA\Property(property="km_cost", type="number", format="float", description="Cost per kilometer"),
 *     @OA\Property(property="range_polygon", type="string", maxLength=5000, description="Polygon defining the range area"),
 * )
 *
 * @OA\Schema(
 *     schema="DeliveryRange_PUT",
 *     type="object",
 *     @OA\Property(property="name", type="string", maxLength=255, description="Name of the delivery range"),
 *     @OA\Property(property="range_from", type="integer", description="Starting range for delivery"),
 *     @OA\Property(property="range_to", type="integer", description="Ending range for delivery"),
 *     @OA\Property(property="min_value", type="number", format="float", description="Minimum value for delivery"),
 *     @OA\Property(property="free_from", type="number", format="float", description="Free delivery from this value"),
 *     @OA\Property(property="cost", type="number", format="float", description="Cost of delivery"),
 *     @OA\Property(property="km_cost", type="number", format="float", description="Cost per kilometer"),
 *     @OA\Property(property="range_polygon", type="string", maxLength=5000, description="Polygon defining the range area"),
 * )
 *
 * @OA\Schema(
 *     schema="DeliveryRange_DELETE",
 *     type="object"
 * )
 */
class DeliveryRangeRequest extends FormRequest
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
            'order.id' => 'string|in:asc,desc',
            'order.name' => 'string|in:asc,desc',
        ],
        self::METHOD_POST => [
            'name' => 'nullable|string|max:255',
            'range_from' => 'nullable|integer',
            'range_to' => 'nullable|integer',
            'min_value' => 'nullable|numeric',
            'free_from' => 'nullable|numeric',
            'cost' => 'nullable|numeric',
            'km_cost' => 'nullable|numeric',
            'range_polygon' => 'string|max:5000',
        ],
        self::METHOD_PUT => [
            'name' => 'nullable|string|max:255',
            'range_from' => 'nullable|integer',
            'range_to' => 'nullable|integer',
            'min_value' => 'nullable|numeric',
            'free_from' => 'nullable|numeric',
            'cost' => 'nullable|numeric',
            'km_cost' => 'nullable|numeric',
            'range_polygon' => 'string|max:5000',
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
        if ('PUT' == $this->getMethod()) {
            $rules['tag'][] = Rule::unique('tags', 'tag')->ignore($this->id);
        }

        return self::$rules[$this->getMethod()] ?? [];
    }
}
