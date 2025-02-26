<?php

namespace App\Managers;

use App\Http\Controllers\ParametersTrait;
use App\Models\Tag;
use App\Services\UtilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TagManager
{
    use ParametersTrait;

    private $locales;

    public function __construct()
    {
        $this->locales = UtilService::getLocales();
    }

    public function create(Request $request): Tag
    {
        $params = $this->getParams($request, ['name', 'visibility' => false, 'description', 'icon']);

        $params['tag'] = Str::slug($params['name']['pl']);

        $tag = DB::connection('tenant')->transaction(function () use ($params) {
            $tag = Tag::create(Tag::decamelizeArray($params))->fresh();

            return $tag;
        });

        return $tag;
    }

    public function update(Request $request, Tag $tag): Tag
    {
        $params = $this->getParams($request, ['name', 'visibility' => false, 'description', 'icon']);

        $params['tag'] = Str::slug($params['name']['pl']);

        DB::connection('tenant')->transaction(function () use ($params, $tag) {
            if (! empty($params)) {
                $tag->update($params);
            }
        });

        return $tag;
    }

    public function delete(Tag $tag): Tag
    {
        DB::connection('tenant')->transaction(function () use ($tag) {
            $tag->delete();
        });

        return $tag;
    }
}
