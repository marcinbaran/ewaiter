<?php

namespace App\Console\Commands;

use App\Models\Attribute;
use App\Models\AttributeGroup;
use App\Models\Dish;
use App\Models\DishAttribute;
use App\Models\FoodCategory;
use App\Models\Restaurant;
use App\Repositories\MultiTentantRepositoryTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportExcel extends Command
{
    use MultiTentantRepositoryTrait;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'i:l';

    protected $mainCategory;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->setTenant();
        $mainGroup = $this->getOrCreateMainAttributeGroup('Rodzaj');
        $this->mainCategory = $this->getOrCreateMainCategory('Alkohole');
        $csvFiles = glob(storage_path('import').'/*.csv');
        $allData = []; // Tablica, która będzie przechowywać dane ze wszystkich plików CSV

        foreach ($csvFiles as $file) {
            if (($handle = fopen($file, 'r')) !== false) { // Otwarcie pliku do czytania
                $fileData = []; // Tablica do przechowywania danych z bieżącego pliku CSV
                while (($data = fgetcsv($handle, 1000, ',')) !== false) { // Iteracja przez wiersze pliku
                    $fileData[] = $data; // Dodawanie wiersza danych do tablicy pliku
                }
                fclose($handle); // Zamknięcie pliku po przetworzeniu
                $allData[str_replace(['/var/www/html/storage/import/lordjack - ', '.csv'], '', $file)] = $fileData; // Dodawanie danych z pliku do głównej tablicy danych
            }
        }

        $options = [
            'Szkocja' => [
                'name' => 0,
                'price' => 1,
                'description' => 4,
                'attributes' => [
                    2, 3,
                ],
            ],
            'Irlandia' => [
                'name' => 0,
                'price' => 1,
                'description' => 3,
                'attributes' => [
                    2,
                ],
            ],
            'Whisky' => [
                'name' => 0,
                'price' => 1,
                'description' => 6,
                'attributes' => [
                    2, 3, 4, 5,
                ],
            ],
            'USA' => [
                'name' => 0,
                'price' => 1,
                'description' => 2,
                'attributes' => [],
            ],
            'Świat' => [
                'name' => 0,
                'price' => 1,
                'description' => 2,
                'attributes' => [],
            ],
            'Tequila' => [
                'name' => 0,
                'price' => 1,
                'description' => false,
                'attributes' => [
                    2,
                ],
            ],
            'Wódka smakowa' => [
                'name' => 0,
                'price' => 1,
                'description' => false,
                'attributes' => [],
            ],
            'Wódka czysta' => [
                'name' => 0,
                'price' => 1,
                'description' => false,
                'attributes' => [
                    2,
                ],
            ],
            'Winiaki' => [
                'name' => 0,
                'price' => 1,
                'description' => false,
                'attributes' => [
                    2, 3,
                ],
            ],
            'Gin' => [
                'name' => 0,
                'price' => 1,
                'description' => 3,
                'attributes' => [
                    2,
                ],
            ],
            'Rum' => [
                'name' => 0,
                'price' => 1,
                'description' => 3,
                'attributes' => [
                    2,
                ],
            ],
            'Likiery' => [
                'name' => 0,
                'price' => 1,
                'description' => false,
                'attributes' => [],
            ],
            'Wino' => [
                'name' => 0,
                'price' => 1,
                'description' => 4,
                'attributes' => [
                    2, 3,
                ],
            ],
        ];

        foreach ($allData as $categoryName => $rows) {
            $this->info('Processing - '.$categoryName);
            $category = $this->getOrCreateMainAttribute($categoryName, $mainGroup);

            $attributeNames = [];
            foreach ($rows as $key => $row) {
                if ($key == 0) {
                    foreach ($options[$categoryName]['attributes'] as $attrKey) {
                        $attributeNames[$attrKey] = $this->getOrCreateAttributeGroup($row[$attrKey], $categoryName);
                    }
                    continue;
                }
                $name = trim(ucfirst($row[$options[$categoryName]['name']]));
                $price = (float) $row[$options[$categoryName]['price']];
                $description = $row[$options[$categoryName]['description']];

                $dish = $this->getOrCreateDish($name, $price, $description);
                DishAttribute::query()->firstOrCreate(['dish_id' => $dish->id, 'attribute_id' => $category->id], ['dish_id' => $dish->id, 'attribute_id' => $category->id]);

                foreach ($options[$categoryName]['attributes'] as $attrKey => $attrname) {
                    $attribute = $this->getOrCreateMainAttribute($row[$attrname], $attributeNames[$attrname]);
                    DishAttribute::query()->firstOrCreate(['dish_id' => $dish->id, 'attribute_id' => $attribute->id], ['dish_id' => $dish->id, 'attribute_id' => $attribute->id]);
                }
            }
        }
    }

    private function setTenant()
    {
        $rest = Restaurant::where('hostname', 'like', 'lordjack')->first();
        $this->reconnect($rest);
    }

    private function getOrCreateDish(string $name, $price, $description)
    {
        $dish = Dish::where('name', 'like', '%"pl":"'.$name.'"%')->first();

        if (! $dish instanceof Dish) {
            $dish = new Dish();
            $dish->name = ['pl' => $name];
            $dish->price = $price ?? 0;
            $dish->description = ['pl' => $description];
            $dish->visibility = 1;
            $dish->time_wait = 0;
            $dish->food_category_id = $this->mainCategory->id;
            $dish->delivery = 0;
            $dish->position = 0;
            $dish->save();

            $this->info('New dish created - '.$name);
        }

        return $dish;
    }

    private function getOrCreateMainAttributeGroup(string $categoryName)
    {
        $slug = Str::slug($categoryName);
        $category = AttributeGroup::query()
            ->where('key', 'like', 'rodzaj')
            ->where('is_primary', 1)
            ->first();

        if (! $category instanceof AttributeGroup) {
            $category = new AttributeGroup();
            $category->input_type = 'radio';
            $category->description = null;
            $category->name = ['pl' => $categoryName];
            $category->is_active = 1;
            $category->is_primary = 1;
            $category->key = $slug;
            $category->save();
            $this->info('New dish created - '.$categoryName.' - '.$slug.' - '.$category->id);
        }

        return $category;
    }

    private function getOrCreateMainAttribute(string $categoryName, AttributeGroup $mainGroup)
    {
        $slug = Str::slug($categoryName).'_'.$mainGroup->key;
        $model = Attribute::query()
            ->where('key', $slug)
            ->where('attribute_group_id', $mainGroup->id)
            ->first();

        if (! $model instanceof Attribute) {
            $model = new Attribute();
            $model->attribute_group_id = $mainGroup->id;
            $model->description = null;
            $model->name = ['pl' => $categoryName];
            $model->is_active = 1;
            $model->key = $slug;
            $model->save();
            $this->info('New attribute created - '.$categoryName);
        }

        return $model;
    }

    private function getOrCreateAttributeGroup(string $categoryName, string $main)
    {
        $slug = Str::slug($categoryName).'_'.Str::slug($main);

        $category = AttributeGroup::query()
            ->where('key', 'like', $slug)
            ->first();

        if (! $category instanceof AttributeGroup) {
            $category = new AttributeGroup();
            $category->input_type = 'radio';
            $category->description = null;
            $category->name = ['pl' => $categoryName];
            $category->is_active = 1;
            $category->is_primary = 0;
            $category->key = $slug;
            $category->save();
            $this->info('New attribute group created - '.$categoryName);
        }

        return $category;
    }

    private function getOrCreateMainCategory(string $name)
    {
        $category = FoodCategory::query()->where('name', 'like', '%"pl":"'.$name.'"%')->first();

        if (! $category instanceof FoodCategory) {
            $category = new FoodCategory();
            $category->name = ['pl' => $name];
            $category->description = null;
            $category->visibility = 1;
            $category->position = 0;
            $category->save();
            $this->info('New category created - '.$name);
        }

        return $category;
    }
}
