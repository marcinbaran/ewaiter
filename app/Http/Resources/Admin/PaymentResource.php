<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'p24_session_id' => $this->p24_session_id,
            'p24_amount' => $this->p24_amount,
            'p24_currency' => $this->p24_currency,
            'p24_token' => $this->p24_token,
            'p24_last_error' => $this->p24_last_response['error'] ? (int) $this->p24_last_response['error'] : null,
            'paid' => $this->paid,
            'transferred' => $this->transferred,
            'createdAt' => $this->dateFormat($this->created_at),
            'updatedAt' => $this->dateFormat($this->updated_at),
        ];

        if (! $this->isAjaxRoute($request)) {
            $array['bill'] = new BillResource($this->bill);
        }

        return $array;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isAjaxRoute(Request $request): bool
    {
        return 'select2' == $request->get('query_type');
    }
}
