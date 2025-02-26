<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use App\Models\Promotion;
use App\Models\Resource;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="PromotionResource",
 *     type="object",
 *     title="Promotion Resource",
 *     description="Resource representing a promotion"
 * )
 */
class PromotionResource extends JsonResource
{
    use ResourceTrait;

    /**
     * @var int Default limit items per page
     */
    public const LIMIT = 20;
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     *
     * @OA\Property(
     *     property="id",
     *     type="integer",
     *     description="The ID of the promotion"
     * ),
     * @OA\Property(
     *     property="type",
     *     type="string",
     *     description="Type of the promotion"
     * ),
     * @OA\Property(
     *     property="typeValue",
     *     type="integer",
     *     description="Value type of the promotion"
     * ),
     * @OA\Property(
     *     property="value",
     *     type="number",
     *     format="float",
     *     description="Value of the promotion"
     * ),
     * @OA\Property(
     *     property="orderCategory",
     *     ref="#/components/schemas/FoodCategoryResource",
     *     description="Category related to the promotion order"
     * ),
     * @OA\Property(
     *     property="orderDish",
     *     ref="#/components/schemas/DishResource",
     *     description="Dish related to the promotion order"
     * ),
     * @OA\Property(
     *     property="giftDish",
     *     ref="#/components/schemas/DishResource",
     *     description="Dish offered as a gift in the promotion"
     * ),
     * @OA\Property(
     *     property="minQuantityOrderDish",
     *     type="integer",
     *     description="Minimum quantity of the ordered dish"
     * ),
     * @OA\Property(
     *     property="minPriceBill",
     *     type="number",
     *     format="float",
     *     description="Minimum price of the bill for the promotion"
     * ),
     * @OA\Property(
     *     property="maxQuantityGiftDish",
     *     type="integer",
     *     description="Maximum quantity of the gift dish"
     * ),
     * @OA\Property(
     *     property="description",
     *     type="string",
     *     description="Description of the promotion"
     * ),
     * @OA\Property(
     *     property="merge",
     *     type="boolean",
     *     description="Whether the promotion can be merged with others"
     * ),
     * @OA\Property(
     *     property="active",
     *     type="boolean",
     *     description="Whether the promotion is currently active"
     * ),
     * @OA\Property(
     *     property="startAt",
     *     type="string",
     *     format="date-time",
     *     description="Start date and time of the promotion"
     * ),
     * @OA\Property(
     *     property="endAt",
     *     type="string",
     *     format="date-time",
     *     description="End date and time of the promotion"
     * ),
     * @OA\Property(
     *     property="photo",
     *     ref="#/components/schemas/ResourceResource",
     *     description="Photo associated with the promotion"
     * ),
     * @OA\Property(
     *     property="box",
     *     type="string",
     *     description="Box associated with the promotion"
     * ),
     * @OA\Property(
     *     property="bundle",
     *     ref="#/components/schemas/PromotionBundleResource",
     *     description="Bundle of dishes included in the promotion"
     * ),
     * @OA\Property(
     *     property="bundle_price",
     *     type="number",
     *     format="float",
     *     description="Total price of the bundle after applying promotion"
     * )
     */
    public function toArray($request)
    {
        $array = [
            'id' => $this->id,
            'type' => $this->type,
            'typeValue' => $this->type_value,
            'value' => $this->value,
            'orderCategory' => new FoodCategoryResource($this->orderCategory),
            'orderDish' => new DishResource($this->orderDish),
            'giftDish' => new DishResource($this->giftDish),
            'minQuantityOrderDish' => $this->min_quantity_order_dish,
            'minPriceBill' => $this->min_price_bill,
            'maxQuantityGiftDish' => $this->max_quantity_gift_dish,
            'description' => gtrans('promotions.'.$this->description),
            'merge' => $this->merge,
            'active' => $this->active,
            'startAt' => $this->start_at,
            'endAt' => $this->end_at,
            'photo' => new ResourceResource($this->getPhotoByType(), ResourceResource::returnDefaultCroppaOptions('promotions')),
            'box' => $this->box,
            'bundle' => new PromotionBundleResource($this->promotion_dishes),
            'bundle_price' => self::getBundlePrice($this->id),
        ];

        return $array;
    }

    /**
     * @return resource|null
     */
    public function getOrderDishPhoto()
    {
        return $this->orderDish ? $this->orderDish->photos->first() : null;
    }

    /**
     * @return resource|null
     */
    public function getGiftDishPhoto()
    {
        return $this->giftDish ? $this->giftDish->photos->first() : null;
    }

    /**
     * @return resource|null
     */
    public function getOrderCategoryPhoto()
    {
        return ($this->orderCategory && $this->orderCategory->photo) ? $this->orderCategory->photo : null;
    }

    /**
     * @return resource|null
     */
    public function getPhotoByType()
    {
        if ($this->photo || Promotion::TYPE_ON_BILL == $this->type) {
            return $this->photo ? $this->photo : null;
        }

        if (Promotion::TYPE_ON_CATEGORY == $this->type) {
            return $this->getOrderCategoryPhoto();
        }
        $photo = $this->getOrderDishPhoto();

        return $photo ? $photo : $this->getGiftDishPhoto();
    }

    public static function getBundlePrice($id)
    {
        $promotion = Promotion::where('id', $id)->first();
        if (count($promotion->promotion_dishes)) {
            $price = 0;
            foreach ($promotion->promotion_dishes as $promotion_dish) {
                $price += $promotion_dish->dish->price;
            }
            $discount = (Promotion::TYPE_VALUE_PRICE == $promotion->type_value ? $promotion->value : ($price * $promotion->value) / 100);

            return number_format((float) $price - $discount, 2, '.', '');
        } else {
            return number_format((float) 0, 2, '.', '');
        }
    }
}
