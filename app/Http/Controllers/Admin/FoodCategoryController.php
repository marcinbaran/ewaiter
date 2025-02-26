<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Select2Trait;
use App\Http\Requests\Admin\FoodCategoryRequest;
use App\Http\Resources\Admin\FoodCategoryResource;
use App\Managers\FoodCategoryManager;
use App\Models\FoodCategory;
use App\Services\TranslationService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FoodCategoryController extends Controller
{
    use Select2Trait;

    public $user_roles;

    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var FoodCategoryManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        $this->user_roles = Auth::user()->roles;
        $this->transService = $service;
        $this->manager = new FoodCategoryManager($this->transService);
        FoodCategoryResource::wrap('results');

    }

    /**
     * @param Request $request
     *
     * @return View|Factory|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $data = FoodCategory::get()->toTree();

        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return FoodCategoryResource::collection(FoodCategory::getPaginatedForPanel($request->get('query_category'), FoodCategoryResource::LIMIT, ['name' => 'asc']));
        }

        return view('admin.categories.index')->with([
            'controller' => 'category',
            'action' => 'index',
            'data' => $data,
        ]);
    }

    /**
     * @param Request $request
     * @param FoodCategory $category
     *
     * @return FoodCategoryResource|View|Factory
     */
    public function show(Request $request, FoodCategory $foodCategory)
    {
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return new FoodCategoryResource($foodCategory);
        }

        return view('admin.categories.show')->with([
            'controller' => 'food-category',
            'action' => 'show',
            'data' => new FoodCategoryResource($foodCategory),
        ]);
    }

    /**
     * store function.
     *
     * @param FoodCategoryRequest $request
     *
     * @return RedirectResponse
     */
    public function store(FoodCategoryRequest $request)
    {
        $this->manager->create($request);

        $request->session()->flash('alert-success', __('admin.Food category was created'));

        return $this->redirectToIndex($request, 'admin.categories.index');
    }

    /**
     * @return View|Factory
     */
    public function create(Request $request)
    {
        $category = new FoodCategory();

        return view('admin.categories.form')->with($this->hydrateData([
            'controller' => 'food-catergory',
            'action' => 'create',
            'data' => new FoodCategoryResource($category),
            'parent' => $request->parentFoodCategoryId ?? null,
            'oldCategories' => $this->getOldArrayForSelect2('parent', 'id'),
            'defaultRedirectUrl' => route('admin.categories.index'),
            'user_roles' => $this->user_roles
        ], $request));
    }

    /**
     * @param FoodCategory $foodCategory
     *
     * @return View|Factory
     */
    public function edit(Request $request, FoodCategory $foodCategory)
    {
        return view('admin.categories.form')->with($this->hydrateData([
            'controller' => 'food-category',
            'action' => 'edit',
            'data' => new FoodCategoryResource($foodCategory),
            'oldCategories' => $this->getOldArrayForSelect2('parent', 'id'),
            'defaultRedirectUrl' => route('admin.categories.index'),
            'user_roles' => $this->user_roles
        ], $request));
    }

    /**
     * @param FoodCategoryRequest $request
     * @param FoodCategory $foodCategory
     *
     * @return RedirectResponse
     */
    public function update(FoodCategoryRequest $request, FoodCategory $foodCategory)
    {
        $this->manager->update($request, $foodCategory);

        $request->session()->flash('alert-success', __('admin.Food category was updated'));

        return $this->redirectToIndex($request, 'admin.categories.index');
    }

    /**
     * @param Request $request
     * @param FoodCategory $foodCategory
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, FoodCategory $foodCategory)
    {
        $this->manager->delete($foodCategory);

        $request->session()->flash('alert-success', __('admin.Food category was deleted'));

        return redirect()->route('admin.categories.index');
    }

    public function categories(?int $id = null)
    {
        $categoryResource = new FoodCategoryResource($id ? FoodCategory::findOrFail($id) : new FoodCategory());
        $currentLocale = config('app.locale');
        $categoryChildren = $this->getChildrenIds($categoryResource);
        $excludedCategories = array_merge($id ? [$id] : [], $categoryChildren, FoodCategory::where('is_inheritable', false)->pluck('id')->toArray());

        FoodCategory::fixTree();
        $categories = FoodCategoryResource::collection(FoodCategory::all()->toTree());

        $treeCategories = collect(json_decode(json_encode($this->createTreeFromCategories($categories, $excludedCategories))));
        $treeCategories = collect(json_decode(json_encode([['id' => -1, 'name' => __('admin.do_not_assign_category')]])))->concat($treeCategories);

        $selectedCategory = collect([
            ['id' => $categoryResource->parent_id ?? -1],
        ]);

        $response = $this->getJsonStringForSelect2($treeCategories, $selectedCategory, 'id');

        return $response;
    }

    private function getChildrenIds($category)
    {
        $children = [];
        foreach ($category->children as $child) {
            $children[] = $child->id;
            $children = array_merge($children, $this->getChildrenIds($child));
        }

        return $children;
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
            $item['name'] = $newItemName.' '.$item['name'];

            if (! in_array($category->id, $excludedCategories)) {
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
