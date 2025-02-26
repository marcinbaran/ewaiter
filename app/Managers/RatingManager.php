<?php

namespace App\Managers;

use App\Http\Controllers\ParametersTrait;
use App\Models\Rating;
use App\Models\Restaurant;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RatingManager
{
    use ParametersTrait;

    /**
     * @param TranslationService $service
     */
    public function __construct()
    {
    }

    /**
     * @param Request $request
     *
     * @return Rating
     */
    public function create(Request $request): Rating
    {
        $params = $this->getParams($request, ['restaurant_id', 'bill_id', 'visibility' => true,
            'anonymous', 'comment', 'restaurant_comment', 'r_food', 'r_delivery']);

        $checkRating = self::checkRating($request);
        throw_if($checkRating !== true, new AccessDeniedHttpException(gtrans('ratings.'.$checkRating)));

        $rating = DB::transaction(function () use ($params) {
            $rating = Rating::create(Rating::decamelizeArray($params))->fresh();

            return $rating;
        });

        return $rating;
    }

    /**
     * @param Request $request
     * @param Rating    $rating
     *
     * @return Rating
     */
    public function update(Request $request, Rating $rating): Rating
    {
        $params = $this->getParams($request, ['visibility' => true,
            'anonymous', 'comment', 'restaurant_comment', 'r_food', 'r_delivery']);
        DB::transaction(function () use ($params, $rating) {
            if (! empty($params)) {
                $rating->update($params);
            }
        });

        return $rating;
    }

    /**
     * @param Rating $rating
     *
     * @return Rating
     */
    public function delete(Rating $rating): Rating
    {
        DB::transaction(function () use ($rating) {
            $rating->delete();
        });

        return $rating;
    }

    /**
     * @param Request $request
     */
    public function checkRating($request)
    {
        $rating = Rating::where('restaurant_id', $request->restaurant_id)->where('bill_id', $request->bill_id)->first();
        if ($rating) {
            return 'Rating already exists';
        }
        $restaurant = Restaurant::where('id', $request->restaurant_id)->first();
        if ($restaurant) {
            config(['database.connections.tenant.database' => $restaurant->hostname]);
            \DB::reconnect('tenant');
            $bill = \DB::connection('tenant')->table('bills')->where('id', $request->bill_id)->first();
            \DB::purge('tenant');
            if (! $bill) {
                return 'Order does not exists';
            }
            if ($bill->status != 3) {
                return 'Order is not ended';
            }

            return true;
        }

        return 'Restaurant does not exists';
    }
}
