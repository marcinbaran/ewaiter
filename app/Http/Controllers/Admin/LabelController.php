<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LabelRequest;
use App\Managers\LabelManager;
use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LabelController extends Controller
{
    public function __construct(private LabelManager $manager)
    {
    }

    public function index(): View
    {
        return view('admin.labels.index')->with([
            'controller' => 'label',
            'action' => 'index',
        ]);
    }

    public function store(LabelRequest $request)
    {
        $this->manager->create($request);

        $request->session()->flash('alert-success', __('admin.Label was created'));

        return $this->redirectToIndex($request, 'admin.labels.index');
    }

    public function create(Request $request)
    {
        return view('admin.labels.form')->with($this->hydrateData([
            'controller' => 'label',
            'action' => 'create',
            'data' => new Label(),
            'images' => $this->getLabelImages(),
            'defaultRedirectUrl' => route('admin.labels.index'),
        ], $request));
    }

    private function getLabelImages(): array
    {
        $imagesPath = 'images/labels';
        $directory = public_path($imagesPath);
        $files = scandir($directory);
        $filePaths = array_filter($files, function ($file) use ($directory) {
            return !in_array($file, ['.', '..']) && is_file($directory . '/' . $file);
        });
        $filePaths = array_map(function ($file) use ($imagesPath) {
            return [
                'name' => str_replace('.svg', '', $file),
                'path' => url('/') . '/' . $imagesPath . '/' . $file,
            ];
        }, $filePaths);

        return $filePaths;
    }

    public function edit(Request $request, Label $label, int $id)
    {
        $label = $label->findOrFail($id);

        return view('admin.labels.form')->with($this->hydrateData([
            'controller' => 'label',
            'action' => 'edit',
            'data' => $label,
            'images' => $this->getLabelImages(),
            'defaultRedirectUrl' => route('admin.labels.index'),
        ], $request));
    }

    public function update(LabelRequest $request, int $id)
    {
        $label = Label::findOrFail($id);
        $this->manager->update($request, $label);

        $request->session()->flash('alert-success', __('admin.Label was updated'));

        return $this->redirectToIndex($request, 'admin.labels.index');
    }

    public function delete(Request $request, Label $label)
    {
        $this->manager->delete($label);

        $request->session()->flash('alert-success', __('admin.Label was deleted'));

        return redirect()->route('admin.labels.index');
    }
}
