<?php

namespace App\Http\Controllers\Admin;

use App\Enum\AttributeGroupInputType;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Select2Trait;
use App\Http\Requests\Admin\AttributeGroupRequest;
use App\Http\Resources\Admin\AdditionGroupResource;
use App\Http\Resources\Admin\AttributeGroupResource;
use App\Managers\AttributeGroupManager;
use App\Models\AttributeGroup;
use Illuminate\Http\Request;

class AttributeGroupController extends Controller
{
    use Select2Trait;

    private $manager;

    public function __construct()
    {
        $this->manager = new AttributeGroupManager();
        AttributeGroupResource::wrap('results');
    }

    public function index(Request $request)
    {
        return view('admin.attribute_groups.index');
    }

    public function create(Request $request)
    {
        return view('admin.attribute_groups.form')->with($this->hydrateData([
            'data' => new AttributeGroupResource(new AttributeGroup()),
            'defaultRedirectUrl' => route('admin.attribute_groups.index'),
            'oldInputType' => old('input_type'),
            'attributes' => $request->get('attributes', null),
        ], $request));
    }

    public function store(AttributeGroupRequest $request)
    {
        $this->manager->createFromRequest($request);

        $request->session()->flash('alert-success', __('attribute_groups.attribute_group_was_created'));

        return $this->redirectToIndex($request, 'admin.attribute_groups.index');
    }

    public function edit(Request $request, AttributeGroup $attributeGroup)
    {
        return view('admin.attribute_groups.form')->with($this->hydrateData([
            'data' => new AdditionGroupResource($attributeGroup),
            'defaultRedirectUrl' => route('admin.attribute_groups.index'),
            'oldInputType' => old('input_type'),
            'attributes' => $request->get('attributes', null),
        ], $request));
    }

    public function update(AttributeGroupRequest $request, AttributeGroup $attributeGroup)
    {
        $this->manager->updateFromRequest($request, $attributeGroup);

        $request->session()->flash('alert-success', __('attribute_groups.attribute_group_was_updated'));

        return $this->redirectToIndex($request, 'admin.attribute_groups.index');
    }

    public function delete(Request $request, AttributeGroup $attributeGroup)
    {
        $this->manager->delete($attributeGroup);

        $request->session()->flash('alert-success', __('attribute_groups.attribute_group_was_deleted'));

        return redirect()->route('admin.attribute_groups.index');
    }

    public function input_types(Request $request, $id = null)
    {
        $inputTypes = collect(
            json_decode(json_encode(
                array_map(fn ($value) => collect(json_decode(json_encode(['id' => $value, 'name' => __('attribute_groups.'.$value)]))), AttributeGroupInputType::getValues())
            ))
        );
        $selectedInputType = $id ? collect(json_decode(json_encode([
            ['id' => AttributeGroup::find($id)?->input_type],
        ]))) : collect();
        $response = $this->getJsonStringForSelect2($inputTypes, $selectedInputType, 'id', );

        return $response;
    }
}
