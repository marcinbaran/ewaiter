<?php

namespace App\Managers;

use App\Enum\PromotionType;
use App\Exceptions\SimpleValidationException;
use App\Http\Controllers\ParametersTrait;
use App\Models\Dish;
use App\Models\Promotion;
use App\Models\PromotionDish;
use App\Services\TranslationService;
use App\Services\UploadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionManager
{
    use ParametersTrait;

    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @param TranslationService $service
     */
    public function __construct(TranslationService $service)
    {
        $this->transService = $service;
    }

    /**
     * @param Request $request
     *
     * @return Promotion
     */
    public function create(Request $request): Promotion
    {
        $params = $this->getParams($request, [
            'type',
            'typeValue',
            'value',
            'name',
            'description',
            'startAt',
            'endAt',
            'merge',
            'active',
        ]);

        if ($params['startAt'] === null) {
            $params['startAt'] = Carbon::today()->startOfDay();
        }

        if ($params['endAt'] === null) {
            $params['endAt'] = Carbon::today()->endOfDay();
        }

        $references = $this->getParams($request, ['orderDish', 'giftDish', 'orderCategory', 'orderDishes']);
        $params['order_dish_id'] = isset($references['orderDish']) ? $references['orderDish']['id'] : null;
        $params['order_category_id'] = isset($references['orderCategory']) ? $references['orderCategory']['id'] : null;
        $paramsEn = $this->getParams($request, ['description_en']);

        if($params['type']==PromotionType::BUNDLE->value){
            if($this->validateBundle($params,$references)){
                throw new SimpleValidationException([__('promotion.validation.bundle_promotion_in_that_time_already_exist')]);
            }
        }
        $promotion = DB::connection('tenant')->transaction(function () use ($params, $references, $request) {
            $promotion = Promotion::create(Promotion::decamelizeArray($params))->fresh();

            $uploadService = new UploadService();
            $uploadService->moveTempFiles($request, $promotion);

            if (isset($references['orderDishes'])) {
                foreach ($references['orderDishes'] as $order_dish) {
                    $promotion->promotion_dishes()->create(['dish_id' => $order_dish['id'], 'promotion_id' => $promotion->id]);
                }
            }

            return $promotion;
        });

        return $promotion;
    }

    /**
     * @param Request $request
     * @param Promotion $promotion
     *
     * @return Promotion
     */
    public function update(Request $request, Promotion $promotion): Promotion
    {
        $params = $this->getParams($request, [
            'typeValue',
            'value',
            'name',
            'description',
            'startAt',
            'endAt',
            'merge',
            'active',
        ]);

        $references = $this->getParams($request, ['orderDish', 'giftDish', 'orderCategory', 'orderDishes']);
        !isset($references['orderDish']) ?: $params['order_dish_id'] = $references['orderDish']['id'];
        !isset($references['orderCategory']) ?: $params['order_category_id'] = $references['orderCategory']['id'];

        DB::connection('tenant')->transaction(function () use ($params, $references, $promotion) {
            if (!empty($params)) {
                $promotion->update($params);
                $promotion->fresh();
            }

            $existingDishes = $promotion->promotion_dishes()->withTrashed()->pluck('dish_id')->toArray();

            $newDishes = isset($references['orderDishes']) ? array_column($references['orderDishes'], 'id') : [];

            $dishesToDelete = array_diff($existingDishes, $newDishes);
            if (!empty($dishesToDelete)) {
                $promotion->promotion_dishes()->whereIn('dish_id', $dishesToDelete)->delete(); // soft delete
            }

            foreach ($newDishes as $dishId) {
                $existingDish = $promotion->promotion_dishes()->withTrashed()->where('dish_id', $dishId)->first();

                if ($existingDish) {
                    if ($existingDish->trashed()) {
                        $existingDish->restore();
                    }
                } else {
                    $promotion->promotion_dishes()->create(['dish_id' => $dishId, 'promotion_id' => $promotion->id]);
                }
            }
        });


        return $promotion;
    }

    /**
     * @param Promotion $promotion
     *
     * @return Promotion
     */
    public function delete(Promotion $promotion): Promotion
    {
        DB::connection('tenant')->transaction(function () use ($promotion) {
            $promotion->delete();
        });

        return $promotion;
    }

    private function validateBundle(array $promotion, array $references): bool
    {

        $dishIds = collect($references['orderDishes'])->pluck('id')->map(function($id) {
            return (int) $id;
        })->toArray();
        $dishCount = count($dishIds);
        $existingPromotion = Promotion::where('type', $promotion['type'])
            ->whereNull('deleted_at')
            ->whereHas('promotion_dishes', function($query) use ($dishIds) {
                $query->whereIn('dish_id', $dishIds);
            }, '=', $dishCount)
            ->whereDoesntHave('promotion_dishes', function($query) use ($dishIds) {
                $query->whereNotIn('dish_id', $dishIds);
                $query->whereNull('deleted_at');
            })
            ->exists();
        return $existingPromotion;
    }
}
