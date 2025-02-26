<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Spatie\Translatable\HasTranslations;

class Tag extends Model
{
    use ModelTrait;
    use UsesTenantConnection;
    use HasTranslations;

    protected $fillable = [
        'tag',
        'name',
        'description',
        'icon',
        'visibility',
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    protected $attributes = [
        'visibility' => 0,
    ];

    protected $casts = [
        'name_translation' => 'json',
        'description_translation' => 'json',
    ];

    public $translatable = [
        'name',
        'description',
    ];

    public function dishes(): HasMany
    {
        return $this->hasMany(DishTag::class, 'tag_id', 'id');
    }

    public function isVisibility(): string
    {
        return $this->visibility ? 'Yes' : 'No';
    }

    public static function getRows(array $criteria, array $order, int $limit, int $offset): Collection
    {
        $query = self::limit($limit)->offset($offset);

        if (! empty($criteria['id'])) {
            $query->whereIn('id', $criteria['id']);
        }
//        if (!empty($criteria['bill'])) {
//            $query->whereIn('bill_id', $criteria['bill']);
//        }
        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                $query->orderBy(self::decamelize($column), $direction);
            }
        }

        return $query->get();
    }

    public static function getPaginatedForPanel(string $filter = null, int $paginateSize, array $order = null, $filter_columns = null): LengthAwarePaginator
    {
        $query = self::distinct()->where('visibility', 1);

        if (! empty($filter)) {
            $query->where('tag', 'like', '%'.$filter.'%')
                ->orWhere('name', 'like', '%'.$filter.'%')
                ->orWhere('description', 'like', '%'.$filter.'%');
        }

        return $query->paginate($paginateSize, ['tags.*']);
    }
}
