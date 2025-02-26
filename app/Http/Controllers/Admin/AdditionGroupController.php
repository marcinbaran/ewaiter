<?php

namespace App\Http\Controllers\Admin;

use App\Enum\AdditionGroup\AdditionGroupType;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Select2Trait;
use App\Http\Requests\Admin\AdditionGroupRequest;
use App\Http\Resources\Admin\AdditionGroupResource;
use App\Http\Resources\Admin\DishResource;
use App\Http\Resources\Admin\FoodCategoryResource;
use App\Managers\AdditionGroupManager;
use App\Models\AdditionGroup;
use App\Models\Dish;
use App\Models\FoodCategory;
use App\Services\TranslationService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdditionGroupController extends Controller
{
    use Select2Trait;

    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var AdditionGroupManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        $this->transService = $service;
        $this->manager = new AdditionGroupManager();
        AdditionGroupResource::wrap('results');
    }

    /**
     * @param Request $request
     *
     * @return View|Factory|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        if ($request->has('fix_translations')) {
            foreach (DB::connection('tenant')->select('SELECT * FROM additions_groups WHERE 1') as $ag) {
                try {
                    if (! Str::isJson($ag->name)) {
                        $additionGroup = AdditionGroup::find($ag->id);
                        $additionGroup->setTranslation('name', 'pl', (string) $ag->name);
                        $additionGroup->save();
                        $additionGroup->fresh();
                    }
                } catch (\Throwable $e) {
                    dd($e);
                }
            }
        }

        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return AdditionGroupResource::collection(AdditionGroup::getPaginatedForPanel($request->get('query_addition'), AdditionGroupResource::LIMIT, ['id' => 'asc'], $request->get('query_category')));
        }

        return view('admin.additions_groups.index')->with([
            'controller' => 'addition_group',
            'action' => 'index',
        ]);
    }

    /**
     * @param Request $request
     * @param AdditionGroup $additionGroup
     *
     * @return AdditionGroupResource|View|Factory
     */
    public function show(Request $request, AdditionGroup $additionGroup)
    {
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return new AdditionGroupResource($additionGroup);
        }

        return view('admin.additions_groups.show')->with([
            'controller' => 'addition_group',
            'action' => 'show',
            'data' => new AdditionGroupResource($additionGroup),
        ]);
    }

    /**
     * @return View|Factory
     */
    public function create(Request $request)
    {
        $additionGroup = new AdditionGroup;

        return view('admin.additions_groups.form')->with($this->hydrateData([
            'controller' => 'addition_group',
            'action' => 'create',
            'data' => new AdditionGroupResource($additionGroup),
            'oldFoodCategories' => $this->getOldArrayForSelect2('addition_group_category', 'id'),
            'oldDishes' => $this->getOldArrayForSelect2('addition_group_dish', 'id'),
            'defaultRedirectUrl' => route('admin.additions_groups.index'),
        ], $request));
    }

    /**
     * store function.
     *
     * @param AdditionGroupRequest $request
     *
     * @return RedirectResponse
     */
    public function store(AdditionGroupRequest $request)
    {
        $this->manager->createFromRequest($request);

        $request->session()->flash('alert-success', __('admin.Addition group was created'));

        return $this->redirectToIndex($request, 'admin.additions_groups.index');
    }

    /**
     * @param AdditionGroup $additionGroup
     *
     * @return View|Factory
     */
    public function edit(Request $request, AdditionGroup $additionGroup)
    {
        if (! is_array($additionGroup->name)) {
            $additionGroup->setTranslation('name', 'pl', $additionGroup->name);
            $additionGroup->save();
            $additionGroup->fresh();
        }

        return view('admin.additions_groups.form')->with($this->hydrateData([
            'controller' => 'addition_group',
            'action' => 'edit',
            'data' => new AdditionGroupResource($additionGroup),
            'oldFoodCategories' => $this->getOldArrayForSelect2('addition_group_category', 'id'),
            'oldDishes' => $this->getOldArrayForSelect2('addition_group_dish', 'id'),
            'defaultRedirectUrl' => route('admin.additions_groups.index'),
        ], $request));
    }

    /**
     * @param AdditionGroupRequest $request
     * @param AdditionGroup $additionGroup
     *
     * @return RedirectResponse
     */
    public function update(AdditionGroupRequest $request, AdditionGroup $addition_group)
    {
        $this->manager->updateFromRequest($request, $addition_group);

        $request->session()->flash('alert-success', __('admin.Addition group was updated'));

        return $this->redirectToIndex($request, 'admin.additions_groups.index');
    }

    /**
     * @param AdditionGroupRequest $request
     * @param AdditionGroup $additionGroup
     *
     * @return RedirectResponse
     */
    public function duplicate(AdditionGroupRequest $request, AdditionGroup $additionGroup)
    {
        $newGroup = $this->manager->duplicateGroup($request, $additionGroup);

        $request->session()->flash('alert-success', __('admin.Addition group was duplicated'));

        return redirect()->route('admin.additions_groups.edit', ['addition_group' => $newGroup->id]);
    }

    /**
     * @param Request $request
     * @param AdditionGroup $additionGroup
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, AdditionGroup $additionGroup)
    {
        $this->manager->delete($additionGroup);

        $request->session()->flash('alert-success', __('admin.Addition group was deleted'));

        return redirect()->route('admin.additions_groups.index');
    }

    public function categories(?int $id = null)
    {
        $additionGroupResource = new AdditionGroupResource($id ? AdditionGroup::findOrFail($id) : new AdditionGroup());
        $additionGroupCategories = FoodCategoryResource::collection(FoodCategory::all());

        return $this->getJsonStringForSelect2($additionGroupCategories, $additionGroupResource->additions_groups_categories, 'category_id');
    }

    public function dishes(?int $id = null)
    {
        $additionGroupResource = new AdditionGroupResource($id ? AdditionGroup::findOrFail($id) : new AdditionGroup());
        $additionGroupDishes = DishResource::collection(Dish::all());

        return $this->getJsonStringForSelect2($additionGroupDishes, $additionGroupResource->additions_groups_dishes, 'dish_id');
    }

    public function additionGroupTypes(?int $id = null)
    {
        $additionGroupResource = new AdditionGroupResource($id ? AdditionGroup::findOrFail($id) : new AdditionGroup());
        $additionGroupTypes = collect(json_decode(json_encode($this->transformEnumKeyValuePairsForSelect2(AdditionGroupType::getKeyValuePairs(), 'addition_group.types'))));

        return $this->getJsonStringForSelect2($additionGroupTypes, isset($additionGroupResource->type) ? collect(['id' => $additionGroupResource->type]) : collect(), 'id');
    }
}
