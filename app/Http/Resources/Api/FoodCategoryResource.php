<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use App\Models\FoodCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Translatable\HasTranslations;
/**
 * @OA\Schema(
 *     schema="FoodCategoryResource",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID of the category"
 *     ),
 *     @OA\Property(
 *         property="position",
 *         type="integer",
 *         description="Position of the category"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description of the category"
 *     ),
 *     @OA\Property(
 *         property="numberOfDishes",
 *         type="integer",
 *         description="Number of visible dishes in the category"
 *     ),
 *     @OA\Property(
 *         property="numberOfChildren",
 *         type="integer",
 *         description="Number of visible child categories"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the category"
 *     ),
 *     @OA\Property(
 *         property="dishes",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/DishResource"),
 *         description="List of dishes in the category"
 *     ),
 *     @OA\Property(
 *         property="children",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/FoodCategoryResource"),
 *         description="List of child categories"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="FoodCategoryResourcePOST",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID of the category"
 *     ),
 *     @OA\Property(
 *         property="position",
 *         type="integer",
 *         description="Position of the category"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description of the category"
 *     ),
 *     @OA\Property(
 *         property="numberOfDishes",
 *         type="integer",
 *         description="Number of visible dishes in the category"
 *     ),
 *     @OA\Property(
 *         property="numberOfChildren",
 *         type="integer",
 *         description="Number of visible child categories"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the category"
 *     ),
 *     @OA\Property(
 *         property="dishes",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/DishResource"),
 *         description="List of dishes in the category"
 *     ),
 *     @OA\Property(
 *         property="children",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/FoodCategoryResourcePOST"),
 *         description="List of child categories"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="FoodCategoryResourcePUT",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID of the category"
 *     ),
 *     @OA\Property(
 *         property="position",
 *         type="integer",
 *         description="Position of the category"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description of the category"
 *     ),
 *     @OA\Property(
 *         property="numberOfDishes",
 *         type="integer",
 *         description="Number of visible dishes in the category"
 *     ),
 *     @OA\Property(
 *         property="numberOfChildren",
 *         type="integer",
 *         description="Number of visible child categories"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the category"
 *     ),
 *     @OA\Property(
 *         property="dishes",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/DishResource"),
 *         description="List of dishes in the category"
 *     ),
 *     @OA\Property(
 *         property="children",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/FoodCategoryResourcePUT"),
 *         description="List of child categories"
 *     )
 * )
 */
class FoodCategoryResource extends JsonResource
{
    use ResourceTrait, HasTranslations;

    /**
     * @var int Default limit items per page
     */
    public const LIMIT = 20;

    /**
     * @var int Default depth tree
     */
    public const DEPTH = 0;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        $lang = $request->input('locale', config('app.fallback_locale'));

        // Recursive function to filter children categories
        $filteredChildren = $this->filterCategories($this->children);

        $array = [
            'id' => $this->id,
            'position' => $this->position,
            'description' => $this->getTranslation('description', $lang),
            'numberOfDishes' => $this->visibleDishes->count(),
            'numberOfChildren' => count($filteredChildren),
            'parent_id' => $this->parent_id,
            'name' => $this->getTranslation('name', $lang),
        ];

        if ($this->isWithDishes($request)) {
            $dishesWithPosition = $this->dishesWithPosition;
            $dishesWithoutPosition = $this->dishesWithoutPosition;

            $mergedDishes = $dishesWithPosition->merge($dishesWithoutPosition);
            $array['numberOfDishes'] = $mergedDishes->count();
            $array['dishes'] = DishResource::collection($mergedDishes);
        }

        if ($this->isWithChildren($request)) {
            $array['children'] = self::collection($filteredChildren);
        }

        return $array;
    }

    private function filterCategories($categories)
    {
        $filtered = [];

        foreach ($categories as $category) {
            if ($this->checkVisibility($category)) {
                $filteredChildCategories = $this->filterCategories($category->children);

                if ($category->visibleDishes->count() > 0 || count($filteredChildCategories) > 0) {
                    $category->children = $filteredChildCategories;
                    $filtered[] = $category;
                }
            }
        }

        return $filtered;
    }

    private function checkVisibility($category)
    {
        if (is_null($category->parent_id)) {
            return $category->visibility;
        }

        $parentCategory = FoodCategory::find($category->parent_id);

        if (is_null($parentCategory)) {
            return false;
        }

        if (! $parentCategory->visibility || ! $category->visibility) {
            return false;
        }

        return $this->checkVisibility($parentCategory);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function isWithChildren(Request $request): bool
    {
        return $this->isFoodCategoriesRoute($request) || $this->isPromotionsRoute($request);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function isWithDishes(Request $request): bool
    {
        return $this->isPromotionsRoute($request) || $this->isFoodCategoriesRoute($request);
    }
}
