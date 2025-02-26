<?php

namespace App\Managers;

use App\Http\Controllers\ParametersTrait;
use App\Models\PlayerId;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlayerIdManager
{
    use ParametersTrait;

    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @param TranslationService $service
     */
    public function __construct(TranslationService $service)
    {
        $this->transService = $service;
    }

    /**
     * @param Request $request
     *
     * @return PlayerId
     */
    public function create(Request $request): PlayerId
    {
        $params = $request->all(['playerId', 'deviceInfo']);
        $references = $this->getParams($request, ['user']);
        $user = Auth::user();

        $params['user_id'] = $references['user']['id'] ?? $user->id;
        $playerId = DB::connection('tenant')->transaction(function () use ($params) {
            $playerId = PlayerId::create(PlayerId::decamelizeArray($params))->fresh();

            return $playerId;
        });

        return $playerId;
    }

    /**
     * @param Request  $request
     * @param PlayerId $playerId
     *
     * @return PlayerId
     */
    public function update(Request $request, PlayerId $playerId): PlayerId
    {
        $params = $this->getParams($request, ['playerId', 'deviceInfo']);
        $references = $this->getParams($request, ['user']);

        ! isset($references['user']['id']) ?: $params['user_id'] = $references['user']['id'];

        if (! empty($params)) {
            DB::connection('tenant')->transaction(function () use ($params, $playerId) {
                $playerId->update($params);
                $playerId->fresh();
            });
        }

        return $playerId;
    }

    /**
     * @param PlayerId $playerId
     *
     * @return PlayerId
     */
    public function delete(PlayerId $playerId): PlayerId
    {
        DB::connection('tenant')->transaction(function () use ($playerId) {
            $playerId->delete();
        });

        return $playerId;
    }
}
