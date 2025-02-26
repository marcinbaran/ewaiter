<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="PaymentResource",
 *     type="object",
 *     title="Payment Resource",
 *     description="Resource representing payment details",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=123,
 *         description="Unique identifier for the payment."
 *     ),
 *     @OA\Property(
 *         property="bill",
 *         ref="#/components/schemas/BillResource",
 *         description="The bill associated with the payment."
 *     ),
 *     @OA\Property(
 *         property="p24_session_id",
 *         type="string",
 *         example="1234567890",
 *         description="The session ID for the payment (specific to p24 payment gateway)."
 *     ),
 *     @OA\Property(
 *         property="p24_amount",
 *         type="number",
 *         format="float",
 *         example=150.00,
 *         description="The amount paid in the specified currency."
 *     ),
 *     @OA\Property(
 *         property="p24_currency",
 *         type="string",
 *         example="PLN",
 *         description="The currency used for the payment."
 *     ),
 *     @OA\Property(
 *         property="p24_token",
 *         type="string",
 *         example="abcdef123456",
 *         description="The token generated for the payment session."
 *     ),
 *     @OA\Property(
 *         property="p24_last_error",
 *         type="string",
 *         nullable=true,
 *         example="Error message if any occurred",
 *         description="The last error message from the payment gateway, if any."
 *     ),
 *     @OA\Property(
 *         property="paid",
 *         type="boolean",
 *         example=true,
 *         description="Indicates whether the payment has been completed."
 *     ),
 *     @OA\Property(
 *         property="transferred",
 *         type="boolean",
 *         example=false,
 *         description="Indicates whether the funds have been transferred."
 *     ),
 *     @OA\Property(
 *         property="url",
 *         type="string",
 *         format="uri",
 *         example="https://payment.gateway/redirect",
 *         description="URL for payment gateway or receipt."
 *     ),
 *     @OA\Property(
 *         property="createdAt",
 *         type="string",
 *         format="date-time",
 *         example="2023-07-30T12:34:56Z",
 *         description="The timestamp when the payment was created."
 *     ),
 *     @OA\Property(
 *         property="updatedAt",
 *         type="string",
 *         format="date-time",
 *         example="2023-07-30T12:34:56Z",
 *         description="The timestamp when the payment was last updated."
 *     )
 * )
 */
class PaymentResource extends JsonResource
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
            'p24_session_id' => $this->p24_session_id,
            'p24_amount' => $this->p24_amount,
            'p24_currency' => $this->p24_currency,
            'p24_token' => $this->p24_token,
            'p24_last_error' => $this->p24_last_response['error'] ?? null,
            'paid' => $this->paid,
            'transferred' => $this->transferred,
            'url' => $this->url,
            'createdAt' => $this->dateFormat($this->created_at),
            'updatedAt' => $this->dateFormat($this->updated_at),
        ];

        return $array;
    }
}
