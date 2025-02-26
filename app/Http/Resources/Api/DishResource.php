<?php

namespace App\Http\Resources\Api;

use App\Helpers\DishHelper;
use App\Helpers\PromotionHelper;
use App\Http\Resources\ResourceTrait;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Translatable\HasTranslations;
/**
 * @OA\Schema(
 *     schema="DishResource",
 *     type="object",
 *     title="Dish Resource",
 *     description="Dish Resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the dish"
 *     ),
 *     @OA\Property(
 *         property="food_category_id",
 *         type="integer",
 *         nullable=true,
 *         description="ID of the food category the dish belongs to"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Translated name of the dish"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Translated description of the dish"
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="number",
 *         format="float",
 *         description="Price of the dish"
 *     ),
 *     @OA\Property(
 *         property="delivery",
 *         type="boolean",
 *         description="Indicates if the dish is available for delivery"
 *     ),
 *     @OA\Property(
 *         property="position",
 *         type="integer",
 *         description="Position of the dish in the menu"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Date and time when the dish was created"
 *     ),
 *     @OA\Property(
 *         property="labels",
 *         type="array",
 *         @OA\Items(
 *             type="string"
 *         ),
 *         description="Labels associated with the dish"
 *     ),
 *     @OA\Property(
 *         property="photo_url",
 *         type="string",
 *         format="uri",
 *         description="URL to the photo of the dish"
 *     ),
 *     @OA\Property(
 *         property="category_name",
 *         type="string",
 *         nullable=true,
 *         description="Name of the category the dish belongs to (if requested)"
 *     ),
 *     @OA\Property(
 *         property="tags",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(
 *                 property="id",
 *                 type="integer",
 *                 description="Unique identifier for the tag"
 *             ),
 *             @OA\Property(
 *                 property="tag",
 *                 type="string",
 *                 description="Tag identifier"
 *             ),
 *             @OA\Property(
 *                 property="name_translation",
 *                 type="string",
 *                 description="Translated name of the tag"
 *             ),
 *             @OA\Property(
 *                 property="description_translation",
 *                 type="string",
 *                 description="Translated description of the tag"
 *             )
 *         ),
 *         description="Tags associated with the dish (if requested)"
 *     ),
 *     @OA\Property(
 *         property="category",
 *         type="object",
 *         description="Food category resource (if requested)",
 *         @OA\Property(
 *             property="id",
 *             type="integer",
 *             description="Unique identifier for the food category"
 *         ),
 *         @OA\Property(
 *             property="name",
 *             type="string",
 *             description="Name of the food category"
 *         )
 *     ),
 *     @OA\Property(
 *         property="attributes",
 *         type="array",
 *         @OA\Items(
 *             ref="#/components/schemas/AttributeResource"
 *         ),
 *         description="List of attributes associated with the dish (if requested)"
 *     ),
 *     @OA\Property(
 *         property="additions",
 *         type="array",
 *         @OA\Items(
 *             ref="#/components/schemas/AdditionResource"
 *         ),
 *         description="List of additions associated with the dish (if requested)"
 *     ),
 *     @OA\Property(
 *         property="additions_groups",
 *         type="array",
 *         @OA\Items(
 *             type="string"
 *         ),
 *         description="Groups of additions associated with the dish"
 *     ),
 *     @OA\Property(
 *         property="promotion",
 *         type="object",
 *         description="Promotion details for the dish (if available)",
 *         @OA\Property(
 *             property="type",
 *             type="string",
 *             description="Type of promotion"
 *         ),
 *         @OA\Property(
 *             property="value",
 *             type="number",
 *             format="float",
 *             description="Value of the promotion"
 *         )
 *     ),
 *     @OA\Property(
 *         property="withoutCategory",
 *         type="boolean",
 *         description="Indicates if the dish is without category"
 *     )
 * )
 */
class DishResource extends JsonResource
{
    use ResourceTrait, HasTranslations;

    public const LIMIT = 20;

    public function toArray(Request $request): array
    {
        $locale = in_array($request->get('locale'), config('app.available_locales')) ? $request->get('locale') : config('app.fallback_locale');

        $array = [
            'id' => $this->id,
            'food_category_id' => $this->category?->id,
            'name' => $this->getTranslation('name', $locale),
            'description' => $this->getTranslation('description', $locale),
            'price' => $this->price,
//            'timeWait' => $this->time_wait,
            'delivery' => $this->hasDelivery(),
            'position' => $this->position,
            'created_at' => (string) $this->created_at,
            'labels' => DishHelper::getLabelsForDish($this->resource),
            'photo_url' => $this->photos->isEmpty() ? DishHelper::getDefaultDishPhotoUrlFromTenant() : $this->photos->first()->getFileUrl(),
        ];

        if ($this->isWithoutCategory($request)) {
            $array['category_name'] = $this->category?->name;
        }

        if ($this->isWithTags($request)) {
            $array['tags'] = $this->getTagsNameTranslate($request['locale']);
        }

        if ($this->isWithFoodCategory($request) && !$this->isWithoutCategory($request)) {
            $array['category'] = new FoodCategoryResource($this->category);
        }

        if ($this->isWithAttributes($request)) {
            $array['attributes'] = AttributeResource::collection($this->attributes);
        }

        if ($this->isWithAdditions($request)) {
            $array['additions'] = [];
            if (count($this->additions)) {
                foreach ($this->additions as $addition_dish) {
                    $array['additions'][] = new AdditionResource($addition_dish->addition);
                }
            }

            $array['additions_groups'] = $this->getAdditionGroups();
        }

        if ($this->isWithPromotions($request)) {
            $array['promotion'] = PromotionHelper::getPromotionForDish($this->resource, $locale);
        }

        return $array;
    }

    protected function isWithFoodCategory(Request $request): bool
    {
        return ! $this->isPromotionsRoute($request) && ! $this->isFoodCategoriesRoute($request);
    }

    protected function isWithAttributes(Request $request): bool
    {
        return (int) $request->with_attribute;
    }

    protected function isWithTags(Request $request): bool
    {
        return $this->isPromotionsRoute($request) || $this->isDishesRoute($request) || $request->withTags;
    }

    protected function isWithAdditions(Request $request): bool
    {
        return $this->isPromotionsRoute($request) || ($this->isDishesRoute($request) && (! $this->isRootRoute($request) || (int) $request->withAdditions)) || (int) $request->withAdditions;
    }

    protected function isWithPromotions(Request $request): bool
    {
        return (int) $request->withPromotions;
    }

    protected function isRootRoute(Request $request): bool
    {
        return 'dishes.index' == $request->route()->getName();
    }

    protected function getTagsNameTranslate($locale)
    {
        $fallbackLocale = config('app.fallback_locale');
        if (count($this->tags)) {
            $tag_names = [];
            foreach ($this->tags as $tag) {
                $tag_names[] = [
                    'id' => $tag->tag->id,
                    'tag' => gtrans('tags.'.$tag->tag->tag),
                    'name_translation' => $tag->tag->name_translation[$locale] ??
                            $tag->tag->name_translation[$fallbackLocale] ??
                            $tag->tag->name,
                    'description_translation' => $tag->tag->description_translation[$locale] ??
                            $tag->tag->description_translation[$fallbackLocale] ??
                            $tag->tag->description,
                ];
            }

            return $tag_names;
        }

        return [];
    }

    protected function getPromotionPrice($promotionsArray)
    {
        $price = $this->price;
        if (count($promotionsArray)) {
            foreach ($promotionsArray as $promotion) {
                switch ($promotion->type) {
                    case 0: //dish
                    case 2: //category
                        $tmp_price = Promotion::TYPE_VALUE_PRICE == $promotion->type_value ? $price - $promotion->value : $this->price - ($this->price * ($promotion->value / 100));
                        break;
                    case 1: //bill
                    case 3: //bundle
                    default:
                        $tmp_price = $this->price;
                        break;
                }
                if ($price > $tmp_price) {
                    $price = $tmp_price;
                }
            }
        }

        return number_format((float) $price, 2, '.', '');
    }

    private function isWithoutCategory(Request $request): bool
    {
        return (bool) $request->withoutCategory;
    }
}
