<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="NotificationGet",
 *     type="object",
 *     @OA\Property(property="itemsPerPage", type="integer", example=10),
 *     @OA\Property(property="page", type="integer", example=1),
 *     @OA\Property(property="type", type="array", @OA\Items(type="string", example="alert")),
 *     @OA\Property(property="id", type="array", @OA\Items(type="string", example="1")),
 *     @OA\Property(property="table", type="array", @OA\Items(type="integer", example=2)),
 *     @OA\Property(property="bill", type="array", @OA\Items(type="integer", example=3)),
 *     @OA\Property(property="user", type="array", @OA\Items(type="integer", example=4)),
 *     @OA\Property(property="isRead", type="boolean", example=true),
 *     @OA\Property(property="fromDate", type="string", format="date", example="2023-01-01"),
 *     @OA\Property(property="toDate", type="string", format="date", example="2023-12-31"),
 *     @OA\Property(property="order.id", type="string", example="asc"),
 *     @OA\Property(property="order.type", type="string", example="desc"),
 *     @OA\Property(property="order.readAt", type="string", example="asc"),
 *     @OA\Property(property="order.createdAt", type="string", example="desc"),
 * )
 * @OA\Schema(
 *     schema="NotificationPost",
 *     type="object",
 *     required={"type"},
 *     @OA\Property(property="type", type="string", enum={"waiter", "promotion", "alert", "reservation"}, example="alert"),
 *     @OA\Property(property="description", type="string", example="This is a notification description."),
 *     @OA\Property(property="table_id", type="integer", example=2),
 *     @OA\Property(property="bill_id", type="integer", example=3),
 *     @OA\Property(property="user_id", type="integer", example=4),
 * )
 * @OA\Schema(
 *     schema="NotificationPut",
 *     type="object",
 *     @OA\Property(property="description", type="string", example="Updated description"),
 *     @OA\Property(property="readAt", type="string", format="date-time", example="2023-01-01T10:00:00Z"),
 * )
 */
class NotificationRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        self::METHOD_GET => [
            'itemsPerPage' => 'integer|min:1|max:50',
            'page' => 'integer|min:1',
            'type' => 'array|min:1',
            'type.*' => 'string|min:1',
            //'type.*' => 'string|min:1|in:waiter,promotion,alert,status_bill,order_bill',
            'id' => 'array|min:1',
            'id.*' => 'string|min:1',
            'table' => 'array|min:1',
            'table.*' => 'integer|exists:tenant.tables,id',
            'bill' => 'array|min:1',
            'bill.*' => 'integer|exists:tenant.bills,id',
            'user' => 'array|min:1',
            'user.*' => 'integer|exists:tenant.users,id',
            'isRead' => 'boolean',
            'fromDate' => 'date_format:Y-m-d|nullable',
            'toDate' => 'date_format:Y-m-d|nullable',
            'order.id' => 'string|in:asc,desc',
            'order.type' => 'string|in:asc,desc',
            'order.readAt' => 'string|in:asc,desc',
            'order.createdAt' => 'string|in:asc,desc',
        ],
        self::METHOD_POST => [
            'type' => 'required|string|in:waiter,promotion,alert,reservation',
            'description' => 'string',
            'table.id' => 'nullable|integer:exists:tenant.tables,id',
            'bill.id' => 'nullable|integer:exists:tenant.bills,id',
            'user.id' => 'nullable|integer:exists:tenant.users,id',
        ],
        self::METHOD_PUT => [
            'description' => 'string',
            'readAt' => 'date',
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
