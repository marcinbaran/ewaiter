<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

trait ResourceTrait
{
    private static $statusCode = 200;

    /**
     * @param mixed $resource
     * @param array $params
     */
    public function __construct($resource, ...$params)
    {
        $this->resource = $resource;
        $this->additional([
            'locale' => app()->getLocale(),
            'date' => [
                'current_date' => Carbon::now()->toDateString(),
                'current_day_of_week' => Carbon::now()->dayOfWeek,
                'current_time' => Carbon::now()->toTimeString(),
            ],
        ]);
        if (method_exists($this, 'init')) {
            $this->init($params);
        }
    }

    /**
     * @param mixed  $date
     * @param string $format
     *
     * @return null|string
     */
    public function dateFormat($date, string $format = 'Y-m-d\TH:i:s')
    {
        if (null === $date) {
            return $date;
        }
        if (! ($date instanceof \DateTime)) {
            $date = new \DateTime($date);
        }

        return $date->format($format);
    }

    /**
     * Customize the response for a request. Override the method from the JsonResource.
     *
     * @param Request      $request
     * @param JsonResponse $response
     */
    public function withResponse($request, $response)
    {
        $response->setStatusCode(self::$statusCode);
    }

    /**
     * Set status code to response.
     *
     * @param int $statusCode
     *
     * @return JsonResource
     */
    public function withStatusCode(int $statusCode): JsonResource
    {
        self::$statusCode = $statusCode;

        return $this;
    }

    /**
     * Create new anonymous resource collection.
     *
     * @param mixed $resource
     * @param array $params
     *
     * @return AnonymousResourceCollection
     */
    public static function collection($resource, ...$params)
    {
        $anonymousResourceCollection = parent::collection($resource);

        if (
            $anonymousResourceCollection->count() > 0 &&
            method_exists($anonymousResourceCollection->collection->first(), 'init')
        ) {
            $anonymousResourceCollection->collection->map->init($params);
        }
        $anonymousResourceCollection->additional([
            'locale' => app()->getLocale(),
            'date' => [
                'current_date' => Carbon::now()->toDateString(),
                'current_day_of_week' => Carbon::now()->dayOfWeek,
            ],
        ]);

        return $anonymousResourceCollection;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function isFoodCategoriesRoute(Request $request): bool
    {
        return false !== strpos($request->route()->getName(), 'food-categories.');
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isDishesRoute(Request $request): bool
    {
        return false !== strpos($request->route()->getName(), 'dishes.');
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isPromotionsRoute(Request $request): bool
    {
        return false !== strpos($request->route()->getName(), 'promotions.');
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isTablesRoute(Request $request): bool
    {
        return false !== strpos($request->route()->getName(), 'tables.');
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isBillsRoute(Request $request): bool
    {
        return false !== strpos($request->route()->getName(), 'bills.');
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isAdditionsRoute(Request $request): bool
    {
        return false !== strpos($request->route()->getName(), 'additions.');
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isPlayerIdsRoute(Request $request): bool
    {
        return false !== strpos($request->route()->getName(), 'player-ids.');
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isRestaurantsRoute(Request $request): bool
    {
        return false !== strpos($request->route()->getName(), 'restaurants.');
    }
}
