<?php

namespace App\View\Components\Admin\Form;

use App\Models\Resource;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Gallery extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public bool $multiple = false,
        public string $namespace = '',
        public array|string $files = [],
        public bool $disabled = false,
        public string $name = '',
        public string $accept = '*',
        public ?string $id = '',
        public array $additionalData = [],
        public bool $required = false,
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        if (! $this->id || $this->id == 0) {
            if (old('temp_gallery_id')) {
                $this->id = old('temp_gallery_id');
                $this->files = [];
                foreach (Resource::query()
                             ->where('resourcetable_id', $this->id)
                             ->where('resourcetable_type', $this->namespace)
                             ->get() as $res) {
                    $this->files[] = [
                        'source' => $res->id,
                        'options' => [
                            'type' => 'local',
                        ],
                    ];
                }
                $this->files = json_encode($this->files);
            }
        }
        if (! $this->files) {
            $fileType = $this->additionalData['file_type'] ?? '';
            $images = Resource::query()
                ->where('resourcetable_type', $this->namespace)
                ->where('resourcetable_id', $this->id)
                ->get();

            foreach ($images as $image) {
                $imageFileType = $image->additional['file_type'] ?? '';
                if (($fileType && $fileType == $imageFileType) || $fileType === '') {
                    $this->files[] = [
                        'source' => $image->id,
                        'options' => [
                            'type' => 'local',
                        ],
                    ];
                }
            }

            $this->files = json_encode($this->files);
        }

        return view('components.admin.form.gallery');
    }
}
