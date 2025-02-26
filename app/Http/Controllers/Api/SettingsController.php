<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\SettingsRequest;
use App\Http\Resources\Api\SettingsResource;
use App\Managers\SettingsManager;
use App\Models\Settings;
use App\Models\User;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
/**
 * @OA\Tag(
 *     name="[TENANT] Settings",
 *     description="[TENANT] [MOB] API Endpoints for managing application settings."
 * )
 */
class SettingsController extends ApiController
{
    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var SettingsManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        parent::__construct();
        $this->transService = $service;
        $this->manager = new SettingsManager($this->transService);
    }

    /**
     * @OA\Get(
     *     path="/api/settings",
     *     operationId="getAllSettings",
     *     tags={"[TENANT] Settings"},
     *     summary="[TENANT] Get a collection of settings",
     *     description="Retrieve a list of application settings.",
     *     @OA\Parameter(
     *         name="itemsPerPage",
     *         in="query",
     *         description="The number of items per page (max 50)",
     *         required=false,
     *         @OA\Schema(type="integer", default=20)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="The page number of the collection",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Setting ID(s)",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Parameter(
     *         name="key",
     *         in="query",
     *         description="Setting key(s)",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="string"))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of settings",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Settings"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index(SettingsRequest $request)
    {
        $locale = app()->getLocale();

        return SettingsResource::collection(Settings::getRowsLang($locale));
    }

    /**
     * @OA\Post(
     *     path="/api/manage_settings/close-restaurant",
     *     operationId="closeRestaurant",
     *     tags={"[TENANT] Settings"},
     *     summary="[TENANT] Toggle restaurant closure",
     *     description="Set the restaurant status to open or closed.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="close", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Restaurant status updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="object", @OA\Property(property="close", type="boolean"))
     *         )
     *     ),
     *     @OA\Response(response=400, description="Action prohibited"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function close_restaurant(SettingsRequest $request)
    {
        $user = Auth::user();
        if (! $user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN])) {
            return json_encode([
                'status' => 400,
                'error' => __('admin.Action prohibited'),
            ]);
        }
        $active = (string) (int) ! $request->get('close');
        $value_active_array = [];
        $value_active = Settings::where('key', 'czas_pracy')->select(['value_active'])->first();
        foreach ($value_active->value_active as $lang_key => $days) {
            foreach ($days as $day_key => $active_old) {
                $value_active_array[$lang_key][$day_key] = $active;
            }
        }
        Settings::where('key', 'czas_pracy')->update(['value_active'=>json_encode($value_active_array)]);

        return [
            'status' => 200,
            'data' => ['close'=>$request->get('close')],
        ];
    }
    /**
     * @OA\Post(
     *     path="/api/address_delivery/address-delivery",
     *     operationId="addressDelivery",
     *     tags={"[TENANT] Settings"},
     *     summary="[TENANT] Toggle address delivery option",
     *     description="Enable or disable address delivery options.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="disable", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Address delivery option updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="object", @OA\Property(property="disable", type="boolean"))
     *         )
     *     ),
     *     @OA\Response(response=400, description="Action prohibited"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function address_delivery(SettingsRequest $request)
    {
        $user = Auth::user();
        if (! $user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN])) {
            return json_encode([
                'status' => 400,
                'error' => __('admin.Action prohibited'),
            ]);
        }
        $active = (string) (int) ! $request->get('disable');
        $value_active_array = [];
        $value_active = Settings::where('key', 'rodzaje_dostawy')->select(['value_active'])->first();
        foreach ($value_active->value_active as $lang_key => $days) {
            foreach ($days as $day_key => $active_old) {
                $value_active_array[$lang_key][$day_key] = $day_key == 'delivery_address' ? $active : $active_old;
            }
        }
        Settings::where('key', 'rodzaje_dostawy')->update(['value_active'=>json_encode($value_active_array)]);

        return [
            'status' => 200,
            'data' => ['disable'=>$request->get('disable')],
        ];
    }
}
