<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Select2Trait;
use App\Http\Requests\Admin\PromotionRequest;
use App\Http\Resources\Admin\DishResource;
use App\Http\Resources\Admin\PromotionResource;
use App\Managers\PromotionManager;
use App\Models\Dish;
use App\Models\FoodCategory;
use App\Models\Promotion;
use App\Models\PromotionDish;
use App\Services\TranslationService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PromotionController extends Controller
{
    use Select2Trait;

    public $user_roles;
    protected ?string $redirectUrlSessionKey = 'promotion_custom_default_url';
    /**
     * @var TranslationService
     */
    private $transService;
    /**
     * @var PromotionManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        $this->user_roles = Auth::user()->roles;
        $this->transService = $service;
        $this->manager = new PromotionManager($this->transService);
        PromotionResource::wrap('results');

    }

    /**
     * @param Request $request
     *
     * @return View|Factory
     */
    public function index(Request $request)
    {
        session()->put($this->redirectUrlSessionKey, $request->fullUrl());

        return view('admin.promotions.index')->with([
            'controller' => 'promotion',
            'action' => 'index',
        ]);
    }

    /**
     * @param Request $request
     * @param Promotion $promotion
     *
     * @return View|Factory
     */
    public function show(Request $request, Promotion $promotion)
    {
        $boxes = array_diff(Promotion::boxes(), [$promotion->box]);
        0 == $promotion->box ?: $boxes[] = 0;
        $rows = Promotion::getRows(['active' => Promotion::ACTIVE_YES, 'box' => $boxes], [], 2, 0);
        $rows->add($promotion);

        return view('admin.promotions.show')->with([
            'controller' => 'promotion',
            'action' => 'show',
            'data' => new PromotionResource($promotion),
            'rows' => PromotionResource::collection($rows),
        ]);
    }

    /**
     * @return View|Factory
     */
    public function createDish(Request $request)
    {
        $promotion = new Promotion();

        return view('admin.promotions.dish_form')->with($this->hydrateData([
            'controller' => 'promotion',
            'action' => 'create',
            'data' => new PromotionResource($promotion),
            'defaultRedirectUrl' => route('admin.promotions.index'),
            'oldDish' => $this->getOldArrayForSelect2('orderDish', 'id'),
            'user_roles' => $this->user_roles,
        ], $request));
    }

    public function createCategory(Request $request)
    {
        $promotion = new Promotion();

        return view('admin.promotions.category_form')->with($this->hydrateData([
            'controller' => 'promotion',
            'action' => 'create',
            'data' => new PromotionResource($promotion),
            'defaultRedirectUrl' => route('admin.promotions.index'),
            'oldCategory' => $this->getOldArrayForSelect2('orderCategory', 'id'),
            'user_roles' => $this->user_roles,
        ], $request));
    }

    public function createBundle(Request $request)
    {
        $promotion = new Promotion();

        return view('admin.promotions.bundle_form')->with($this->hydrateData([
            'controller' => 'promotion',
            'action' => 'create',
            'data' => new PromotionResource($promotion),
            'defaultRedirectUrl' => route('admin.promotions.index'),
            'oldDishes' => $this->getOldArrayForSelect2('orderDishes', 'id'),
            'user_roles' => $this->user_roles,
        ], $request));
    }

    /**
     * @param PromotionRequest $request
     *
     * @return RedirectResponse
     */
    public function store(PromotionRequest $request)
    {
        $this->manager->create($request);

        $request->session()->flash('alert-success', __('admin.Promotion was created'));

        return $this->redirectToIndex($request, 'admin.promotions.index');
    }

    /**
     * @param Promotion $promotion
     *
     * @return View|Factory
     */
    public function edit(Request $request, Promotion $promotion)
    {
        $viewData = [
            'controller' => 'promotion',
            'action' => 'edit',
            'data' => new PromotionResource($promotion),
            'defaultRedirectUrl' => route('admin.promotions.index'),
            'user_roles' => $this->user_roles,
        ];

        if ($promotion->type == Promotion::TYPE_ON_CATEGORY) {
            $promotionFormView = 'admin.promotions.category_form';
            $viewData['oldCategory'] = $this->getOldArrayForSelect2('orderCategory', 'id');
        } elseif ($promotion->type == Promotion::TYPE_ON_BUNDLE) {
            $promotionFormView = 'admin.promotions.bundle_form';
            $viewData['oldDishes'] = $this->getOldArrayForSelect2('orderDishes', 'id');
        } else {
            $promotionFormView = 'admin.promotions.dish_form';
            $viewData['oldDish'] = $this->getOldArrayForSelect2('orderDish', 'id');
        }

        return view($promotionFormView)->with($this->hydrateData($viewData, $request));
    }

    /**
     * @param PromotionRequest $request
     * @param Promotion $promotion
     *
     * @return RedirectResponse
     */
    public function update(PromotionRequest $request, Promotion $promotion)
    {
        $this->manager->update($request, $promotion);

        $request->session()->flash('alert-success', __('admin.Promotion was updated'));

        return $this->redirectToIndex($request, 'admin.promotions.index');
    }

    /**
     * @param PromotionRequest $request
     * @param Promotion $promotion
     *
     * @return RedirectResponse
     */
    public function delete(PromotionRequest $request, Promotion $promotion)
    {
        $this->manager->delete($promotion);

        $request->session()->flash('alert-success', __('admin.Promotion was deleted'));

        return redirect()->route('admin.promotions.index');
    }

    public function dish(?int $id = null)
    {
        $promotionResource = new PromotionResource($id ? Promotion::findOrFail($id) : new Promotion());
        $promotionDishes = DishResource::collection(Dish::all());
        $response = $this->getJsonStringForSelect2($promotionDishes, isset($promotionResource->order_dish_id) ? collect([Dish::find($promotionResource->order_dish_id)]) : collect(), 'id', function ($item) {
            $foodCategory = FoodCategory::findOrFail($item->food_category_id);

            return $item->name . ' (' . $foodCategory->name . ')';
        });

        return $response;
    }

    public function categories(?int $id = null)
    {
        $promotionResource = new PromotionResource($id ? Promotion::findOrFail($id) : new Promotion());
        FoodCategory::fixTree();
        $promotionCategories = FoodCategory::all()->toTree();
        $excludedCategories = array_merge(FoodCategory::where('is_inheritable', false)->pluck('id')->toArray());

        $treeCategories = collect(json_decode(json_encode($this->createTreeFromCategories($promotionCategories, $excludedCategories))));

        $response = $this->getJsonStringForSelect2($treeCategories, isset($promotionResource->order_category_id) ? collect([FoodCategory::find($promotionResource->order_category_id)]) : collect(), 'id');

        return $response;
    }

    private function createTreeFromCategories(ResourceCollection|Collection $categoriesTree, array $excludedCategories = [], int $spaces = 0): array
    {
        $tree = [];
        foreach ($categoriesTree as $category) {
            $item = [
                'id' => $category->id,
                'name' => $category->name,
            ];

            $newItemName = '';
            for ($i = 0; $i < $spaces; $i++) {
                $newItemName .= $i === 0 ? "\u{2514}" : "\u{2500}";
            }
            $item['name'] = $newItemName . ' ' . $item['name'];

            if (!in_array($category->id, $excludedCategories)) {
                $tree[] = $item;
                if ($category->children->count() > 0) {
                    $children = $this->createTreeFromCategories($category->children, $excludedCategories, $spaces + 1);
                    $tree = array_merge($tree, $children);
                }
            }
        }

        return $tree;
    }

    public function dishes(?int $id = null)
    {
        $promotionDishes = DishResource::collection(Dish::all());
        $dishes = PromotionDish::where('promotion_id', $id)->get();
        $response = $this->getJsonStringForSelect2($promotionDishes, $dishes, 'dish_id', function ($item) {
            $foodCategory = FoodCategory::find($item->food_category_id);

            return $item->name . ' (' . $foodCategory->name . ')';
        });

        return $response;
    }
}
