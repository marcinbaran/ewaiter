<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="PaymentRequest_GET",
 *     type="object",
 *     @OA\Property(property="itemsPerPage", type="integer", example=20, description="Number of items per page"),
 *     @OA\Property(property="page", type="integer", example=1, description="Page number"),
 *     @OA\Property(property="id", type="array", @OA\Items(type="integer"), description="Array of payment IDs"),
 *     @OA\Property(property="bill", type="array", @OA\Items(type="integer"), description="Array of bill IDs"),
 *     @OA\Property(property="order.id", type="string", enum={"asc", "desc"}, description="Order by ID"),
 *     @OA\Property(property="order.createdAt", type="string", enum={"asc", "desc"}, description="Order by creation date"),
 * )
 *
 * @OA\Schema(
 *     schema="PaymentRequest_POST",
 *     type="object",
 *     required={"bill.id", "email"},
 *     @OA\Property(property="bill.id", type="integer", example=1, description="Bill ID"),
 *     @OA\Property(property="email", type="string", example="example@example.com", description="Email address"),
 * )
 *
 * @OA\Schema(
 *     schema="PaymentRequest_PAYMENTS_STATUS",
 *     type="object",
 *     @OA\Property(property="p24_session_id", type="string", description="Przelewy24 session ID"),
 *     @OA\Property(property="p24_order_id", type="string", description="Przelewy24 order ID"),
 * )
 */
class PaymentRequest extends FormRequest
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
            'bill' => 'array|min:1',
            'bill.*' => 'integer|min:1|exists:tenant.bills,id',
            'order.id' => 'string|in:asc,desc',
            'order.createdAt' => 'string|in:asc,desc',
        ],
        self::METHOD_POST => [
            'bill.id' => 'required|integer|exists:tenant.bills,id',
            //'sdk_version' => 'required|string',
            'email' => 'required|email',
            'bank_id' => 'required|integer|min:1'
        ],
        'payments.status' => [
//            'p24_session_id' => 'required|string|exists:tenant.payments,p24_session_id',
//            'p24_order_id' => 'nullable',
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
        $rule = self::$rules[$this->route()->getName()] ?? (self::$rules[$this->getMethod()] ?? []);

        return $rule;
    }
}
