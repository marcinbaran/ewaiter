<?php

namespace App\Managers;

use App\Http\Controllers\ParametersTrait;
use App\Models\Addition;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdditionManager
{
    use ParametersTrait;

    private $transService;

    public function __construct(TranslationService $service)
    {
        $this->transService = $service;
    }

    public function createFromRequest(Request $request): Addition
    {
        $params = $this->getParams($request, ['name', 'price']);
        $params['references'] = $this->getParams($request, ['addition_addition_group']);

        return self::create($params, $this->transService);
    }

    public function updateFromRequest(Request $request, Addition $addition): Addition
    {
        $params = $this->getParams($request, ['name', 'price']);
        $params['references'] = $this->getParams($request, ['addition_addition_group']);

        return self::update($addition, $params, $this->transService);
    }

    public static function delete(Addition $addition): Addition
    {
        DB::connection('tenant')->transaction(function () use ($addition) {
            $addition->delete();
        });

        return $addition;
    }

    public static function create(array $params, TranslationService $service): Addition
    {
        $addition = DB::connection('tenant')->transaction(function () use ($params) {
            $addition = Addition::create(Addition::decamelizeArray(array_diff_key($params, ['languages' => 1, 'references' => 1])))->fresh();

            if (isset($params['references']['addition_addition_group'])) {
                foreach ($params['references']['addition_addition_group'] as $addition_addition_group) {
                    $addition->additions_additions_groups()->create(['addition_group_id' => (int) $addition_addition_group['id'], 'addition_id' => $addition->id]);
                }
            }

            return $addition;
        });

        $service->publish('additions');

        return $addition;
    }

    public static function update(Addition $addition, array $params, TranslationService $service)//: Addition
    {
        if (! empty($params)) {
            DB::connection('tenant')->transaction(function () use ($params, $addition) {
                $addition->update(Addition::decamelizeArray(array_diff_key($params, ['languages' => 1, 'references' => 1])));
                $addition->fresh();

                $addition->additions_additions_groups()->delete();
                if (isset($params['references']['addition_addition_group'])) {
                    foreach ($params['references']['addition_addition_group'] as $addition_addition_group) {
                        $addition->additions_additions_groups()->create(['addition_group_id' => (int) $addition_addition_group['id'], 'addition_id' => $addition->id]);
                    }
                }
            });
        }

        $service->publish('additions');

        return $addition;
    }
}
