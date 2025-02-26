<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ResourceTrait;
use App\Models\Promotion;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResource extends JsonResource
{
    use ResourceTrait;

    /**
     * @var int Default limit items per page
     */
    public const LIMIT = 20;

    public function __get($key)
    {
        $val = parent::__get($key);

        switch ($key) {
            case 'orderDish':
            case 'giftDish':
                return $val ? new DishResource($val) : null;
                break;
            case 'photo':
                return $val ? new ResourceResource($val) : null;
                break;
            case 'orderCategory':
                return $val ? new FoodCategoryResource($val) : null;
                break;
        }

        return $val;
    }

    public function toArray($request)
    {
        $array = [
            'id' => $this->id,
            'type' => $this->type,
            'typeValue' => $this->type_value,
            'value' => $this->value,
            'orderDish' => $this->orderDish,
            'giftDish' => $this->giftDish,
            'minQuantityOrderDish' => $this->min_quantity_order_dish,
            'minPriceBill' => $this->min_price_bill,
            'maxQuantityGiftDish' => $this->max_quantity_gift_dish,
            'description' => gtrans('promotions.'.$this->description),
            'merge' => $this->merge,
            'active' => $this->active,
            'startAt' => $this->start_at,
            'endAt' => $this->end_at,
            'photo' => $this->photo,
            'box' => $this->box,
        ];

        return $array;
    }

    public function getTypeName()
    {
        return Promotion::getTypeName($this->type);
    }

    public function getTypeValueName()
    {
        return $this->type ? 'amount' : 'percent';
    }

    /**
     * @return string
     */
    public function isMerge(): string
    {
        return $this->merge ? 'Yes' : 'No';
    }

    /**
     * @return string
     */
    public function isActive(): string
    {
        return $this->active ? 'Yes' : 'No';
    }

    /**
     * @return string
     */
    public function getOrderCategoryName(): string
    {
        return $this->orderCategory ? (string) $this->orderCategory->name : '';
    }

    /**
     * @return string
     */
    public function getOrderDishName(): string
    {
        return $this->orderDish ? (string) $this->orderDish->name : '';
    }

    /**
     * @return string
     */
    public function getGiftDishName(): string
    {
        return $this->giftDish ? (string) $this->giftDish->name : '';
    }

    /**
     * @return string
     */
    public function getOrderDishPhoto(): string
    {
        if (isset($this->orderDish->photos) && $this->orderDish->photos->first()) {
            return $this->orderDish ? (string) $this->orderDish->photos->first()->getPhoto() : '';
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getGiftDishPhoto(): string
    {
        if (isset($this->giftDish->photos) && $this->giftDish->photos->first()) {
            return $this->giftDish ? (string) $this->giftDish->photos->first()->getPhoto() : '';
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getOrderCategoryPhoto(): string
    {
        return ($this->orderCategory && $this->orderCategory->photo) ? (string) $this->orderCategory->photo->getPhoto() : '';
    }

    public function getPhotoByType()
    {
        if ($this->photo || Promotion::TYPE_ON_BILL == $this->type) {
            return $this->photo ? $this->photo->getPhoto() : '';
        }

        return (Promotion::TYPE_ON_CATEGORY == $this->type) ? $this->getOrderCategoryPhoto() : $this->getOrderDishPhoto();
    }

    public function getNameByType()
    {
        if (Promotion::TYPE_ON_BILL == $this->type) {
            return '';
        }

        return (Promotion::TYPE_ON_CATEGORY == $this->type) ? $this->getOrderCategoryName() : $this->getOrderDishName();
    }
}
