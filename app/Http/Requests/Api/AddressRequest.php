<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="AddressRequest_GET",
 *     type="object",
 *     @OA\Property(property="itemsPerPage", type="integer", example=20, description="Number of items per page"),
 *     @OA\Property(property="page", type="integer", example=1, description="Page number"),
 *     @OA\Property(property="noLimit", type="boolean", example=false, description="Retrieve all items without pagination"),
 *     @OA\Property(property="id", type="array", @OA\Items(type="integer"), description="Array of Address IDs"),
 *     @OA\Property(property="order[id]", type="string", enum={"asc", "desc"}, description="Order by ID"),
 *     @OA\Property(property="order.createdAt", type="string", enum={"asc", "desc"}, description="Order by creation date"),
 * )
 *
 * @OA\Schema(
 *     schema="AddressRequest_POST",
 *     type="object",
 *     required={"name", "city", "postcode"},
 *     @OA\Property(property="company_name", type="string", maxLength=100, description="Company name"),
 *     @OA\Property(property="nip", type="integer", example=1234567890, description="NIP number"),
 *     @OA\Property(property="name", type="string", maxLength=100, description="First name"),
 *     @OA\Property(property="surname", type="string", maxLength=100, description="Last name"),
 *     @OA\Property(property="city", type="string", maxLength=100, description="City"),
 *     @OA\Property(property="postcode", type="string", maxLength=10, description="Postal code"),
 *     @OA\Property(property="street", type="string", maxLength=100, description="Street"),
 *     @OA\Property(property="building_number", type="string", maxLength=50, description="Building number"),
 *     @OA\Property(property="house_number", type="string", maxLength=50, description="House number"),
 *     @OA\Property(property="floor", type="string", maxLength=50, description="Floor"),
 *     @OA\Property(property="phone", type="string", maxLength=15, description="Phone number"),
 * )
 *
 * @OA\Schema(
 *     schema="AddressRequest_PUT",
 *     type="object",
 *     required={"name", "city", "postcode"},
 *     @OA\Property(property="company_name", type="string", maxLength=100, description="Company name"),
 *     @OA\Property(property="nip", type="integer", example=1234567890, description="NIP number"),
 *     @OA\Property(property="name", type="string", maxLength=100, description="First name"),
 *     @OA\Property(property="surname", type="string", maxLength=100, description="Last name"),
 *     @OA\Property(property="city", type="string", maxLength=100, description="City"),
 *     @OA\Property(property="postcode", type="string", maxLength=10, description="Postal code"),
 *     @OA\Property(property="street", type="string", maxLength=100, description="Street"),
 *     @OA\Property(property="building_number", type="string", maxLength=50, description="Building number"),
 *     @OA\Property(property="house_number", type="string", maxLength=50, description="House number"),
 *     @OA\Property(property="floor", type="string", maxLength=50, description="Floor"),
 *     @OA\Property(property="phone", type="string", maxLength=15, description="Phone number"),
 * )
 *
 * @OA\Schema(
 *     schema="AddressRequest_DELETE",
 *     type="object"
 * )
 */
class AddressRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        self::METHOD_GET => [
            'itemsPerPage' => 'integer|min:1|max:50',
            'page' => 'integer|min:1',
            'noLimit' => 'boolean',
            'id' => 'array|min:1',
            'id.*' => 'integer|min:1',
            'order.id' => 'string|in:asc,desc',
            'order.createdAt' => 'string|in:asc,desc',
        ],
        self::METHOD_POST => [
            'company_name' => 'nullable|string|max:100',
            'nip' => 'nullable|integer|digits:10',
            'name' => 'required|string|max:100',
            'surname' => 'nullable|string|max:100',
            'city' => 'required|string|max:100',
            'postcode' => 'required|string|max:10',
            'street' => 'nullable|string|max:100',
            'building_number' => 'nullable|string|max:50',
            'house_number' => 'nullable|string|max:50',
            'floor' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:15',
        ],
        self::METHOD_PUT => [
            'company_name' => 'nullable|string|max:100',
            'nip' => 'nullable|integer|digits:10',
            'name' => 'required|string|max:100',
            'surname' => 'nullable|string|max:100',
            'city' => 'required|string|max:100',
            'postcode' => 'required|string|max:10',
            'street' => 'nullable|string|max:100',
            'building_number' => 'nullable|string|max:50',
            'house_number' => 'nullable|string|max:50',
            'floor' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:15',
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
        $rules = self::$rules[$this->getMethod()] ?? [];

        return $rules;
    }
}
