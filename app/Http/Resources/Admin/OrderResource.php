<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'dish' => new DishResource($this->dish),
            'additions' => AdditionResource::collection($this->getAdditions()),
            //'roast' => $this->getRoast(),
            'quantity' => $this->quantity,
            'price' => $this->price,
            'discount' => $this->discount,
            'status' => $this->status,
            'paid' => $this->paid,
            'createdAt' => $this->dateFormat($this->created_at),
            'updatedAt' => $this->dateFormat($this->updated_at),
        ];
        if ($this->isWithTable($request)) {
            $array['table'] = new TableResource($this->table);
        }
        if ($this->isWithBill($request)) {
            $array['bill'] = new BillResource($this->bill);
        }

        return $array;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isWithTable(Request $request): bool
    {
        return ! $this->isTablesRoute($request);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isWithBill(Request $request): bool
    {
        return ! $this->isBillsRoute($request);
    }

    /**
     * @return string
     */
    public function isPaid(): string
    {
        return $this->paid ? 'Yes' : 'No';
    }
}
