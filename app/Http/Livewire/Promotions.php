<?php

namespace App\Http\Livewire;

use App\Http\Resources\Admin\PromotionResource;
use App\Services\UtilService;
use Livewire\Component;

class Promotions extends Component
{
    public $promotionType = null;

    public $values = null;

    public $valueType = null;

    public $locales = [];

    public $dishes = [];

    public $orderCategoryId = null;

    public $orderDishId = null;

    public $photos = [];

    public $times = [
        'start' => null,
        'end' => null,
    ];

    public $promotionId = null;

    public $isPromotionOverlap = false;

    public $isPromotionActive = false;

    public $oldDish = null;

    public $oldDishes = null;

    public $oldCategory = null;

    public array $descriptions = [];

    private PromotionResource $data;

    public function mount($data)
    {
        $this->data = $data;
        $this->promotionType = old('type', $data['type']) ?? 0;
        $this->value = old('value', $data['value'] ?? null);
        $this->valueType = old('type_value', $data['type_value']) ?? 0;
        $this->locales = $data->getLocales() ?? [];
        $this->orderCategoryId = old('orderCategory.id', $data['order_category_id'] ?? null);
        $this->dishes = UtilService::exchangeOldToSelect2(old('orderDishes'), 'dish_id', $this->data['promotion_dishes'] ?? null);
        $this->orderDishId = old('orderDish.id', $data['order_dish_id'] ?? null);
        $this->photos = $data['photos_json'] ?? [];
        $this->times = [
            'start' => old('startAt', $data['start_at'] ?? null),
            'end' => old('endAt', $data['end_at'] ?? null),
        ];
        $this->promotionId = $data['id'] ?? null;
        $this->isPromotionOverlap = old('merge', $data['merge'] ?? false);
        $this->isPromotionActive = old('active', $data['active'] ?? false);
        $this->oldDish = $oldDish ?? null;
        $this->oldDishes = $oldDishes ?? null;
        $this->oldCategory = $oldCategory ?? null;
        $this->getDescriptions();
        $this->initializeJSDependecies();
    }

    public function updated()
    {
        $this->initializeJSDependecies();
    }

    public function render()
    {
        return view('livewire.promotions');
    }

    private function getDescriptions()
    {
        foreach ($this->locales as $locale) {
            $this->descriptions[$locale] = old('description.'.$locale, $this->data->getTranslation('description', $locale));
        }
    }

    private function initializeJSDependecies()
    {
        $this->dispatchBrowserEvent('activateButton');
        $this->dispatchBrowserEvent('initIMask');
        $this->dispatchBrowserEvent('initSelect2');
        $this->dispatchBrowserEvent('initFilepond');
        $this->dispatchBrowserEvent('initFlowbite');
        $this->dispatchBrowserEvent('add-rounded-corners-to-new-inputs');
    }
}
