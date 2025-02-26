<?php

namespace App\Rules\Promotion;

use App\Enum\Promotion\PromotionValidationFailType;
use App\Enum\PromotionType;
use App\Models\Promotion;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Collection;

class ExistPromotionRule implements ValidationRule, DataAwareRule
{
    protected $data = [];

    private bool $isPromotionValid = true;

    private ?PromotionValidationFailType $failType = null;

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $promotion = request()->route()->parameter('promotion');
        $promotionType = PromotionType::from($this->data['type']);

        $this->setPromotionValid($promotion);

        if (! $this->isPromotionValid) {
            $fail($this->getFailMessage($promotionType, $this->failType));
        }
    }

    private function getActivePromotionsWithType(?PromotionType $type, ?int $currentPromotionId): Collection
    {
        $promotionsQuery = Promotion::query()
            ->where('active', true);

        if ($type == PromotionType::DISH) {
            $promotionsQuery = $promotionsQuery->where('type', $type)
                ->where('order_dish_id', $this->data['orderDish']['id']);
        } elseif ($type == PromotionType::CATEGORY) {
            $promotionsQuery = $promotionsQuery->where('type', $type)
                ->where('order_category_id', $this->data['orderCategory']['id']);
        }

        if ($currentPromotionId) {
            $promotionsQuery = $promotionsQuery->where('id', '!=', $currentPromotionId);
        }

        return $promotionsQuery->get();
    }

    private function isPromotionDateValid(Carbon $newPromotionStart, Carbon $newPromotionEnd, Carbon $currPromotionStart, Carbon $currPromotionEnd): bool
    {
        return ! (
            $newPromotionStart->isBetween($currPromotionStart, $currPromotionEnd) ||
            $newPromotionEnd->isBetween($currPromotionStart, $currPromotionEnd)
        );
    }

    private function isPromotionTypeValid(PromotionType $newPromotionType, PromotionType $currPromotionType): bool
    {
        return $newPromotionType != $currPromotionType;
    }

    private function isPromotionNameValid(array $newPromotionName, array $currPromotionName): bool
    {
        return $newPromotionName['pl'] != $currPromotionName['pl'];
    }

    private function isPromotionAlreadySet(Promotion $currPromotionName): bool
    {
        if (isset($this->data['orderDish'])) {
            return $this->data['orderDish']['id'] != $currPromotionName['order_dish_id'];
        }

        if (isset($this->data['orderCategory'])) {
            return $this->data['orderCategory']['id'] != $currPromotionName['order_category_id'];
        }

        return true;
    }

    private function setPromotionValid(?Promotion $newPromotion)
    {
        $newPromotionName = $this->data['name'];
        $newPromotionType = PromotionType::from($this->data['type']);
        $newPromotionActive = (bool) $this->data['active'];
        $newPromotionStartAt = Carbon::parse($this->data['startAt'])->startOfDay() ?? Carbon::now()->startOfDay()->subYears(10);
        $newPromotionEndAt = Carbon::parse($this->data['endAt'])->endOfDay() ?? Carbon::now()->endOfDay()->addYears(10);

        $currPromotions = $this->getActivePromotionsWithType(null, $newPromotion?->id);

        foreach ($currPromotions as $currPromotion) {
            $currPromotionName = $currPromotion->getTranslations('name');
            $currPromotionType = PromotionType::from($currPromotion->type);
            $currPromotionStartAt = Carbon::parse($currPromotion->start_at)->startOfDay();
            $currPromotionEndAt = Carbon::parse($currPromotion->end_at)->endOfDay();

//            $isPromotionTypeValid = $this->isPromotionTypeValid($newPromotionType, $currPromotionType);
            $isPromotionAlreadySet = $this->isPromotionAlreadySet($currPromotion);
            $isPromotionDateValid = $this->isPromotionDateValid($newPromotionStartAt, $newPromotionEndAt, $currPromotionStartAt, $currPromotionEndAt);
            $isPromotionNameValid = $this->isPromotionNameValid($newPromotionName, $currPromotionName);

            if (! $isPromotionNameValid) {
                $this->isPromotionValid = false;
                $this->failType = PromotionValidationFailType::PROMOTION_NAME_EXISTS;

                return;
            }

            if ($newPromotionActive) {
                if (! $isPromotionAlreadySet && ! $isPromotionDateValid && $newPromotionType !== PromotionType::BUNDLE) {
                    $this->isPromotionValid = false;
                    $this->failType = PromotionValidationFailType::PROMOTION_DATE_INVALID_AND_EXISTS;

                    return;
                }
            }
        }
    }

    private function getFailMessage(PromotionType $type, PromotionValidationFailType $failType): string
    {
        if ($failType == PromotionValidationFailType::PROMOTION_NAME_EXISTS) {
            return __('promotion.validation.promotion_with_the_same_name_already_exists');
        } elseif ($type == PromotionType::DISH && $failType == PromotionValidationFailType::PROMOTION_DATE_INVALID_AND_EXISTS) {
            return __('promotion.validation.dish_promotion_in_that_time_already_exists');
        } elseif ($type == PromotionType::CATEGORY && $failType == PromotionValidationFailType::PROMOTION_DATE_INVALID_AND_EXISTS) {
            return __('promotion.validation.category_promotion_in_that_time_already_exists');
        }

        return '';
    }
}
