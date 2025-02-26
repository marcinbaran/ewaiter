<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="ReservationRequestGET",
 *     @OA\Property(property="itemsPerPage", type="integer", example=10),
 *     @OA\Property(property="page", type="integer", example=1),
 *     @OA\Property(property="id", type="array", @OA\Items(type="integer")),
 *     @OA\Property(property="fromDate", type="string", format="date", example="2024-01-01"),
 *     @OA\Property(property="toDate", type="string", format="date", example="2024-01-31"),
 *     @OA\Property(property="order[id]", type="string", enum={"asc", "desc"}, example="asc"),
 *     @OA\Property(property="order[name]", type="string", enum={"asc", "desc"}, example="desc")
 * ),
 * @OA\Schema(
 *     schema="ReservationRequestPOST",
 *     @OA\Property(property="start", type="string", format="date-time", example="2024-01-01T19:00:00"),
 *     @OA\Property(property="end", type="string", format="date-time", example="2024-01-01T21:00:00"),
 *     @OA\Property(property="table_id", type="integer", example=1),
 *     @OA\Property(property="people_number", type="integer", example=4),
 *     @OA\Property(property="kid", type="boolean", example=false),
 *     @OA\Property(property="closed", type="boolean", example=false),
 *     @OA\Property(property="description", type="string", example="Birthday celebration"),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="phone", type="string", example="+1234567890")
 * ),
 * @OA\Schema(
 *     schema="ReservationRequestPUT",
 *     @OA\Property(property="start", type="string", format="date-time", example="2024-01-01T19:00:00"),
 *     @OA\Property(property="end", type="string", format="date-time", example="2024-01-01T21:00:00"),
 *     @OA\Property(property="table_id", type="integer", example=1),
 *     @OA\Property(property="people_number", type="integer", example=4),
 *     @OA\Property(property="kid", type="boolean", example=false),
 *     @OA\Property(property="closed", type="boolean", example=false),
 *     @OA\Property(property="description", type="string", example="Updated reservation description"),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="phone", type="string", example="+1234567890")
 * )
 */
class ReservationRequest extends FormRequest
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
            'fromDate' => 'date_format:Y-m-d|nullable',
            'toDate' => 'date_format:Y-m-d|nullable',
            'order.id' => 'string|in:asc,desc',
            'order.name' => 'string|in:asc,desc',
        ],
        self::METHOD_POST => [
            'start' => 'required|date',
            //'end' => 'nullable|date|after:start',
            'table_id' => 'nullable|integer|exists:tenant.tables,id',
            //'user_id' => 'required|integer|exists:users,id',
            'people_number' => 'required|integer',
            'kid' => 'boolean',
            'closed' => 'boolean',
            'description' => 'nullable|string',
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:15',
        ],
        self::METHOD_PUT => [
            'start' => 'nullable|date',
            //'end' => 'nullable|date|after:start',
            'table_id' => 'nullable|integer|exists:tenant.tables,id',
            //'user_id' => 'nullable|integer|exists:users,id',
            'people_number' => 'nullable|integer',
            'kid' => 'boolean',
            'closed' => 'boolean',
            'description' => 'nullable|string',
            'name' => 'nullable|string|max:100',
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
        return self::$rules[$this->getMethod()] ?? [];
    }
}
