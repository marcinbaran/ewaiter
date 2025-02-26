<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
