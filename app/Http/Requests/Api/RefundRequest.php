<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="RefundRequestGET",
 *     type="object",
 *     @OA\Property(property="itemsPerPage", type="integer", example=10, description="Number of items per page."),
 *     @OA\Property(property="page", type="integer", example=1, description="Page number for pagination."),
 *     @OA\Property(property="id", type="array", @OA\Items(type="integer", example=1, description="Array of refund IDs to filter.")),
 *     @OA\Property(property="bill", type="array", @OA\Items(type="integer", example=1, description="Array of bill IDs to filter.")),
 *     @OA\Property(property="payment", type="array", @OA\Items(type="integer", example=1, description="Array of payment IDs to filter.")),
 *     @OA\Property(property="order.id", type="string", enum={"asc", "desc"}, description="Order by refund ID."),
 *     @OA\Property(property="order.createdAt", type="string", enum={"asc", "desc"}, description="Order by creation date."),
 *     description="Request schema for the GET method of RefundRequest."
 * )
 */
/**
 * @OA\Schema(
 *     schema="RefundRequestPOST",
 *     type="object",
 *     @OA\Property(property="bill_id", type="integer", example=1, description="ID of the bill associated with the refund."),
 *     @OA\Property(property="payment_id", type="integer", example=1, description="ID of the payment associated with the refund."),
 *     @OA\Property(property="amount", type="number", format="float", example=100.50, description="Amount to be refunded."),
 *     @OA\Property(property="status", type="integer", example=1, enum={0, 1, 2}, description="Status of the refund (0: pending, 1: completed, 2: failed)."),
 *     description="Request schema for the POST method of RefundRequest."
 * )
 */

class RefundRequest extends FormRequest
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
            'payment' => 'array|min:1',
            'payment.*' => 'integer|min:1|exists:tenant.payments,id',
            'order.id' => 'string|in:asc,desc',
            'order.createdAt' => 'string|in:asc,desc',
        ],
        self::METHOD_POST => [
            'bill_id' => 'required|integer|exists:tenant.bills,id',
            'payment_id' => 'required|integer|exists:tenant.payments,id',
            'amount' => 'required|numeric',
            'status' => 'required|integer|in:0,1,2',
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
