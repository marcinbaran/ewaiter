<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Select2Trait;
use App\Http\Requests\Admin\DishRequest;
use App\Http\Resources\Admin\AttributeResource;
use App\Http\Resources\Admin\DishResource;
use App\Http\Resources\Admin\TagResource;
use App\Http\Resources\Api\AdditionGroupResource;
use App\Http\Resources\Api\LabelResource;
use App\Managers\DishManager;
use App\Models\AdditionGroup;
use App\Models\Attribute;
use App\Models\Dish;
use App\Models\FoodCategory;
use App\Models\Label;
use App\Models\Tag;
use App\Services\TranslationService;
use Hyn\Tenancy\Facades\TenancyFacade;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DishController extends Controller
{
    use Select2Trait;


    public $user_roles;

    /**
     * @var TranslationService
     */

    private $transService;

    /**
     * @var DishManager
     */
    private $manager;

    public function __construct(
        TranslationService $service,
    )
    {
        $this->transService = $service;
        $this->manager = new DishManager($this->transService);
        DishResource::wrap('results');
        $this->user_roles = Auth::user()->roles;
        $this->isWebsite = TenancyFacade::website() ? 1 : 0;

    }

    /**
     * @param Request $request
     *
     * @return View|Factory|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return DishResource::collection(Dish::getPaginatedForPanel($request->get('query_dish'), DishResource::LIMIT, ['name' => 'asc'], null));
        }

        return view('admin.dishes.index')->with([
            'controller' => 'dish',
            'action' => 'index',
        ]);
    }

    /**
     * @param Request $request
     * @param Dish $dish
     *
     * @return DishResource|View|Factory
     */
    public function show(Request $request, Dish $dish)
    {
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return new DishResource($dish);
        }

        return view('admin.dishes.show')->with([
            'controller' => 'dish',
            'action' => 'show',
            'data' => new DishResource($dish),
        ]);
    }

    /**
     * @return View|Factory
     */
    public function create(Request $request)
    {
        $dish = new Dish;

        return view('admin.dishes.form')->with($this->hydrateData([
            'controller' => 'dish',
            'action' => 'create',
            'data' => new DishResource($dish),
            'oldTags' => $this->getOldArrayForSelect2('tags', 'id'),
            'oldAdditionGroups' => $this->getOldArrayForSelect2('additions_groups', 'id'),
            'oldCategories' => $this->getOldArrayForSelect2('category', 'id'),
            'oldLabels' => $this->getOldArrayForSelect2('labels', 'id'),
            'oldAttributes' => $this->getOldArrayForSelect2('attributes', 'id'),
            'defaultRedirectUrl' => route('admin.dishes.index'),
            'user_roles' => $this->user_roles,
        ], $request));
    }

    /**
     * store function.
     *
     * @param DishRequest $request
     *
     * @return RedirectResponse
     */
    public function store(DishRequest $request)
    {
        $this->manager->createFromRequest($request);

        $request->session()->flash('alert-success', __('admin.Dish was created'));

        return $this->redirectToIndex($request, 'admin.dishes.index');
    }

    /**
     * @param Dish $dish
     *
     * @return View|Factory
     */
    public function edit(Request $request, Dish $dish)
    {
        return view('admin.dishes.form')->with($this->hydrateData([
            'controller' => 'dish',
            'action' => 'edit',
            'data' => new DishResource($dish),
            'oldTags' => $this->getOldArrayForSelect2('tags', 'id'),
            'oldAdditionGroups' => $this->getOldArrayForSelect2('additions_groups', 'id'),
            'oldCategories' => $this->getOldArrayForSelect2('category', 'id'),
            'oldLabels' => $this->getOldArrayForSelect2('labels', 'id'),
            'oldAttributes' => $this->getOldArrayForSelect2('attributes', 'id'),
            'defaultRedirectUrl' => route('admin.dishes.index'),
            'user_roles' => $this->user_roles,
        ], $request));
    }

    /**
     * @param DishRequest $request
     * @param Dish $dish
     *
     * @return RedirectResponse
     */
    public function update(DishRequest $request, Dish $dish)
    {
        $this->manager->updateFromRequest($request, $dish, true);

        $request->session()->flash('alert-success', __('admin.Dish was updated'));

        return $this->redirectToIndex($request, 'admin.dishes.index');
    }

    /**
     * @param Request $request
     * @param Dish $dish
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Dish $dish)
    {
        $this->manager->delete($dish);

        $request->session()->flash('alert-success', __('admin.Dish was deleted'));

        return redirect()->route('admin.dishes.index');
    }

    public function attributes(?int $id = null)
    {
        $dishResource = new DishResource($id ? Dish::findOrFail($id) : new Dish);
        $dishAttributes = AttributeResource::collection(Attribute::all());
        $response = $this->getJsonStringForSelect2($dishAttributes, $dishResource->attributes, 'id');

        return $response;
    }

    public function tags(?int $id = null)
    {
        $dishResource = new DishResource($id ? Dish::findOrFail($id) : new Dish);
        $dishTags = TagResource::collection(Tag::all());
        $response = $this->getJsonStringForSelect2($dishTags, $dishResource->tags, 'tag_id');

        return $response;
    }

    public function additionGroups(?int $id = null)
    {
        $dishResource = new DishResource($id ? Dish::findOrFail($id) : new Dish);
        $dishAdditionGroups = AdditionGroupResource::collection(AdditionGroup::all());
        $response = $this->getJsonStringForSelect2($dishAdditionGroups, $dishResource->additions_groups_dishes, 'addition_group_id');

        return $response;
    }

    public function labels(?int $id = null)
    {
        $dishResource = new DishResource($id ? Dish::findOrFail($id) : new Dish);
        $dishLabels = LabelResource::collection(Label::all());
        $response = $this->getJsonStringForSelect2($dishLabels, $dishResource->labels, 'id');

        return $response;
    }

    public function categories(?int $id = null)
    {
        $dishResource = new DishResource($id ? Dish::findOrFail($id) : new Dish);
        FoodCategory::fixTree();
        $dishCategories = FoodCategory::all()->toTree();

        $treeCategories = collect(json_decode(json_encode($this->createTreeFromCategories($dishCategories))));

        $response = $this->getJsonStringForSelect2($treeCategories, isset($dishResource->category->id) ? collect([$dishResource->category]) : collect(), 'id');

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
}
