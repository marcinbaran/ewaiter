<?php


/**
 * ApiController file.
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ParametersTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
use Request;


/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="1.0.3",
 *         title="E-Waiter API",
 *         description="API for E-Waiter application if endpoint descrpition name or tag starts with [MOB] it means that this endpoint is for mobile application and if it starts with [TENANT] it means that this endpoint is for tenant application",
 *         @OA\Contact(name="E-Waiter Development Team")
 *     ),
 *   @OA\Components(
 *          @OA\SecurityScheme(
 *              securityScheme="bearerAuth",
 *              type="http",
 *              scheme="bearer",
 *              bearerFormat="JWT",
 *              description="Use a Bearer token to access these API endpoints. ONLY PASS THE TOKEN, NO NEED TO ADD `Bearer`",
 *          )
 *      ),
 *     @OA\Server(
 *         url="http://e-waiter.lan",
 *         description="Local server of E-Waiter application"
 *     ),
 *     @OA\Server(
 *         url="https://dev.e-waiter.pl",
 *         description="Dev server of E-Waiter application"
 *     ),
 * )
 */

class ApiController extends BaseController
{
    use ParametersTrait, AuthorizesRequests;


    public function __construct()
    {
        Request::has('provider') && Request::get('provider') == 'tenancy-api' ? $this->middleware(['auth:tenancy-api']) : $this->middleware(['auth:api']);
    }
}
