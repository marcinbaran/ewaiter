<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="RefundResource",
 *     type="object",
 *     title="Refund Resource",
 *     description="Resource representing a refund"
 * )
 */
class RefundResource extends JsonResource
{
    use ResourceTrait;

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
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     *
     * @OA\Property(
     *     property="id",
     *     type="integer",
     *     description="The ID of the refund"
     * ),
     * @OA\Property(
     *     property="bill",
     *     ref="#/components/schemas/BillResource",
     *     description="The associated bill"
     * ),
     * @OA\Property(
     *     property="payment",
     *     ref="#/components/schemas/PaymentResource",
     *     description="The associated payment"
     * ),
     * @OA\Property(
     *     property="billId",
     *     type="integer",
     *     description="The ID of the associated bill"
     * ),
     * @OA\Property(
     *     property="paymentId",
     *     type="integer",
     *     description="The ID of the associated payment"
     * ),
     * @OA\Property(
     *     property="amount",
     *     type="number",
     *     format="float",
     *     description="The amount of the refund"
     * ),
     * @OA\Property(
     *     property="status",
     *     type="string",
     *     description="The status of the refund"
     * ),
     * @OA\Property(
     *     property="createdAt",
     *     type="string",
     *     format="date-time",
     *     description="The timestamp when the refund was created"
     * ),
     * @OA\Property(
     *     property="updatedAt",
     *     type="string",
     *     format="date-time",
     *     description="The timestamp when the refund was last updated"
     * )
     */
    public function toArray($request): array
    {
        $array = [
            'id' => $this->id,
            'bill' => new BillResource($this->bill),
            'payment' => new PaymentResource($this->payment),
            'billId' => $this->bill_id,
            'paymentId' => $this->payment_id,
            'amount' => $this->amount,
            'status' => $this->status,
            'createdAt' => $this->dateFormat($this->created_at),
            'updatedAt' => $this->dateFormat($this->updated_at),
        ];

        return $array;
    }
}
