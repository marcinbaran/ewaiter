<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use App\Models\Promotion;
use App\Models\Restaurant;
use App\Repositories\MultiTentantRepositoryTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Schema(
 *     schema="OrderResource",
 *     type="object",
 *     title="Order Resource",
 *     description="Order resource representation",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="dish", ref="#/components/schemas/DishResource"),
 *     @OA\Property(property="bundle", ref="#/components/schemas/BundleResource"),
 *     @OA\Property(
 *         property="additions",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/AdditionResource")
 *     ),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="price", type="number", format="float", example=29.99),
 *     @OA\Property(property="additionsPrice", type="number", format="float", example=5.00),
 *     @OA\Property(property="packagePrice", type="number", format="float", example=2.50),
 *     @OA\Property(property="discount", type="number", format="float", example=10.00),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="paid", type="boolean", example=true),
 *     @OA\Property(property="restaurant_id", type="integer", example=1),
 *     @OA\Property(property="createdAt", type="string", format="date-time", example="2021-08-15T15:52:01+00:00"),
 *     @OA\Property(property="updatedAt", type="string", format="date-time", example="2021-08-16T15:52:01+00:00"),
 *     @OA\Property(property="table", ref="#/components/schemas/TableResource"),
 *     @OA\Property(property="bill", ref="#/components/schemas/BillResource"),
 *     @OA\Property(property="products_in_order", type="array", @OA\Items(type="object"), description="Products in order")
 * )
 */
class OrderResource extends JsonResource
{
    use ResourceTrait,MultiTentantRepositoryTrait;

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
//        $bundle = new BundleResource($this->bundle);
//        if (!isset($this->bundle->id)) {
//            $bundle = BundleResource::collection($this->bundle->get());
//        }
        $array = [
            'id' => $this->id,
            $this->type => $this->type === 'dish' ? new DishResource($this->dish) : "bundle",

//            'additions' =>  AdditionResource::collection($this->getAdditions($this->restaurant_id)),
//            'roast' => $this->getRoast(),
            'quantity' => $this->quantity,
            'price' => $this->price,
            'additionsPrice' => $this->additions_price,
            'packagePrice' => number_format($this->package_price, 2, '.', ''),
            'discount' => $this->discount,
            'status' => $this->status,
            'paid' => $this->paid,
            'restaurant_id' => $this->restaurant_id,
            'createdAt' => $this->dateFormat($this->created_at),
            'updatedAt' => $this->dateFormat($this->updated_at),

        ];

        if ($this->type === 'dish') {
            $array['additions'] = $this->customize['additions'];
            $array['dish'] = new DishResource($this->dish);
            $array['dish']['name'] = $this->item_name;
        }else{
            $array['additions'] = $this->customize['additions'];
            $array['bundle'] = new BundleResource($this->bundle);
            if(!is_null($this->products_in_order)){
                $array['bundle']  = $this->disassembleProductsInOrder($array['bundle'],$this->products_in_order);
            }

        }

        if ($this->isWithTable($request)) {
            $array['table'] = new TableResource($this->table);
        }

        if ($this->isWithBill($request)) {
            $array['bill'] = new BillResource($this->bill);
        }

        return $array;
    }

    /**
     * @param array $params
     */
    public function init($params = null)
    {
        if (null === $params || is_array($params)) {
            $this->restaurant_id = empty($params) ? null : $params[0];
        }
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
     * @param BundleResource $bundleArray
     * @param array $products_in_order
     * @return mixed
     * @throws \Exception
     */
    private function disassembleProductsInOrder(mixed $bundleArray,array|string $products_in_order)
    {
        if(is_string($products_in_order)){
            $products_in_order = json_decode($products_in_order,true);
        }
        $productIdsInOrder = array_column($products_in_order, 'id');

        $array= array_filter($bundleArray->getDishes(), function ($dish) use ($productIdsInOrder) {
            return in_array($dish['id'], $productIdsInOrder);
        });

        $result =$bundleArray->setDishes($array);
        return $result;
    }



}
