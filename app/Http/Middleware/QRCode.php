<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class QRCode
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->is('api/*')) {
            return $next($request);
        }

        if (
            $request->is('/') &&
            (
                ($request->has('hostname') && $request->has('restaurant_id')) ||
                $request->has('downloadApp')
            )
        ) {
            if($request->has('hostname') && $request->get('hostname') == 'lordjack') {
                logger()->info('https://e-waiter.pl/restaurants/details/lordjack');
                header("Location: https://e-waiter.pl/restaurants/details/lordjack");
                exit;
            }
            $android = 'https://play.google.com/store/apps/details?id=com.primebitstudio.virtualwaiter&hl=pl';
            $ios = 'https://apps.apple.com/pl/app/id1518982193';

            logger()->info($_SERVER['HTTP_USER_AGENT']);
            if ((strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false) || (strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false) || (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') !== false)) {
                $url = $ios;
            } else {
                $url = $android;
            }

            header("Location: $url");
            exit;
        }

        return $next($request);
    }
}
