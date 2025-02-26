<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Select2Trait;
use App\Http\Requests\Admin\AttributeRequest;
use App\Http\Resources\Admin\AdditionResource;
use App\Http\Resources\Admin\AttributeGroupResource;
use App\Http\Resources\Admin\AttributeResource;
use App\Managers\AttributeManager;
use App\Models\Attribute;
use App\Models\AttributeGroup;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    use Select2Trait;

    private $manager;

    public function __construct()
    {
        $this->manager = new AttributeManager();
        AdditionResource::wrap('results');
    }

    public function index()
    {
        return view('admin.attributes.index');
    }

    public function create(Request $request)
    {
        return view('admin.attributes.form')->with($this->hydrateData([
            'data' => new AttributeResource(new Attribute()),
            'defaultRedirectUrl' => route('admin.attributes.index'),
            'images' => $this->getAttributeImages(),
            'oldAttributeGroup' => old('attribute_group_id'),
        ], $request));
    }

    public function store(AttributeRequest $request)
    {
        $this->manager->createFromRequest($request);

        $request->session()->flash('alert-success', __('attributes.attribute_was_created'));

        return $this->redirectToIndex($request, 'admin.attributes.index');
    }

    public function edit(Request $request, Attribute $attribute)
    {
        return view('admin.attributes.form')->with($this->hydrateData([
            'data' => new AttributeResource($attribute),
            'defaultRedirectUrl' => route('admin.attributes.index'),
            'images' => $this->getAttributeImages(),
            'oldAttributeGroup' => old('attribute_group_id'),
        ], $request));
    }

    public function update(AttributeRequest $request, Attribute $attribute)
    {
        $this->manager->updateFromRequest($request, $attribute);

        $request->session()->flash('alert-success', __('attributes.attribute_was_updated'));

        return $this->redirectToIndex($request, 'admin.attributes.index');
    }

    public function delete(Request $request, Attribute $attribute)
    {
        $this->manager->delete($attribute);

        $request->session()->flash('alert-success', __('attributes.attribute_was_deleted'));

        return redirect()->route('admin.attributes.index');
    }

    public function attribute_group(?int $id = null)
    {
        $attributeResource = new AttributeResource($id ? Attribute::findOrFail($id) : new Attribute());
        $attributeGroups = AttributeGroupResource::collection(AttributeGroup::all());
        $selectedAttributeGroup = $id ? collect(json_decode(json_encode([
            ['id' => $attributeResource->attribute_group_id],
        ]))) : collect();

        $response = $this->getJsonStringForSelect2($attributeGroups, $selectedAttributeGroup, 'id');

        return $response;
    }

    private function getAttributeImages(): array
    {
        $imagesPath = 'images/attributes';
        $directory = public_path($imagesPath);
        $files = scandir($directory);
        $filePaths = array_filter($files, function ($file) use ($directory) {
            return ! in_array($file, ['.', '..']) && is_file($directory.'/'.$file);
        });
        $filePaths = array_map(function ($file) use ($imagesPath) {
            return [
                'name' => str_replace('.svg', '', $file),
                'path' => url('/').'/'.$imagesPath.'/'.$file,
            ];
        }, $filePaths);

        return $filePaths;
    }
}
