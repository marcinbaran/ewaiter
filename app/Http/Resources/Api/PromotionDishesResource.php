<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use App\Models\Promotion;
use App\Models\Resource;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="PromotionDishesResource",
 *     type="object",
 *     title="Promotion Dishes Resource",
 *     description="Resource representing a promotion related to dishes",
 * )
 */
class PromotionDishesResource extends JsonResource
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
     *     property="typeName",
     *     type="string",
     *     description="Name of the promotion type"
     * ),
     * @OA\Property(
     *     property="typeValue",
     *     type="integer",
     *     description="Value type of the promotion"
     * ),
     * @OA\Property(
     *     property="typeValueName",
     *     type="string",
     *     description="Name of the type value"
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
     * )
     */
    public function toArray($request)
    {
        $array = [
            'id' => $this->id,
            'type' => $this->type,
            'typeName' => $this->getTypeName($this->type),
            'typeValue' => $this->type_value,
            'typeValueName' => $this->getTypeValueName($this->type_value),
            'value' => $this->value,
            'orderCategory' => new FoodCategoryResource($this->orderCategory),
//            'orderDish' => new DishResource($this->orderDish),
//            'giftDish' => new DishResource($this->giftDish),
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
            //'bundle_dishes' => new PromotionBundleResource($this->giftDish)
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

    public function getTypeName()
    {
        return Promotion::getTypeName($this->type);
    }

    public function getTypeValueName()
    {
        return $this->type ? 'amount' : 'percent';
    }
}
