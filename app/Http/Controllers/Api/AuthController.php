<?php

namespace App\Http\Controllers\Api;

use App\Enum\ExternalLoginType;
use App\Exceptions\ApiExceptions\Auth\ActiveUserNotFoundException;
use App\Exceptions\ApiExceptions\Auth\InvalidCredentialsException;
use App\Exceptions\ApiExceptions\Auth\UserIsBlockedException;
use App\Exceptions\ApiExceptions\General\InvalidRequestDataException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserResource;
use App\Mail\ResetMail;
use App\Models\UserSystem;
use App\Services\AuthService;
use App\Services\ExternalLogin\ExternalLoginService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Laravel\Passport\Client as PassportClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Validator;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="API Endpoints for Auth"
 * )
 */
class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {
        $this->middleware('auth:api', ['except' => [
            'authenticate', 'authenticate_guest', 'refreshToken', 'remind_password', 'loginExternal',
        ]]);
    }


    /**
     * @OA\Post(
     *     path="/api/auth",
     *     operationId="authenticateUser",
     *     tags={"Auth"},
     *     summary="Authenticate user",
     *     description="Authenticates a user with login and password.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="login", type="string", description="User login", example="admin@primebitstudio.com"),
     *             @OA\Property(property="password", type="string", description="User password", example="secret"),
     *
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=31536000),
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImY0YTVlNThmOWU1YmJmZGEzYjBmNDM5YWE4N2IxNzBlNGYzZjRmYjhhMmQ5ZWIyZWEzZTEwMzkxZWIyNjUwZDFhMjU0YzU2NTk2MGQxOWEyIn0.eyJhdWQiOiIyIiwianRpIjoiZjRhNWU1OGY5ZTViYmZkYTNiMGY0MzlhYTg3YjE3MGU0ZjNmNGZiOGEyZDllYjJlYTNlMTAzOTFlYjI2NTBkMWEyNTRjNTY1OTYwZDE5YTIiLCJpYXQiOjE1MzA1MjQ3OTgsIm5iZiI6MTUzMDUyNDc5OCwiZXhwIjoxNTYyMDYwNzk4LCJzdWIiOiIzIiwic2NvcGVzIjpbXX0.cdBtKZbQ1-geBMPKDsfx1tS2KbH7QpkAObgmTjvLk1tIpK1zEXov-5y-l60kxQ6YmPVB5_en8vA1bWZJGMy5hsQw66FoFNixf-P3b-_8k4wWbEYHqhhKP6snFQRkh3aTmG3iMJEzKdIXNh9xIOw0sd1B1KnpenTyHo1RGQ04MmGC5btfDB75ciKWj5LbnRzzU_cKSWppPVXLTklkQcZHHU9mfefIa8AOAXaHut_7oMbssAdiKoArwsUezjDgn5xxpnk71xX9rQcOD6EtkESdP4UafaIWk66NhujCM-n_1hbVRFXDpTnOli50TFIL99mIgpUX2r8CIfbabOC4CM3tgtFFACZkTDV1nCHbU6sfbR7V8H7xjJcumD2NoR4GAQULT9JmCbY2MgYpwYuPLiwv7YP-3UcCGxF0zojECCVexv1tlw0zYicLMiheE2cteRSuUtIrQ1Pdcz6Evp6j1SAAjQUQuVnRS34aVIYvwyQK5rvv0MowDTra_0hbx9AxKZI0jeNxfgOKdBH4Z5fzK1jhw8_5VYPze7FkAOCuxfjFzLzp-AsbzsVncyQ3-UxzYvcs00ZLaZKPl7P7adHWF0ZOrscvuZBQw5kSNr2QNDEH8-DuUeWBs4XoxkwO4wY2nQlgCmaYs86y3RGyxMGjESPyvP7SZNpY2qYp_AYdc01uEjI"),
     *             @OA\Property(property="refresh_token", type="string", example="def50200b2cea03bf9a8d331016eb3e1284affb3366708ac7908b7233951ff00092852e6f088c0b7f0a216ec41cc6c147441a2771ca0f14249ceb23c09bcaf1841930669307417762bbf97529f839c84359792462b17c4c685249f930189ddcee94d592c0d0976774978d26c1acecfbcb95fb860b2cae7cdf7f5970940ea0963bb69857c7a7f9e26e42795bbb4b373d2cddfbb4afadec123371295e514d542f23a902403e319a39070a26f839c13952225e420b7ac9c686508a8fc551fbb2d5a72f777010168dd1a6dd10d93bc8c9e4b4212b967413ae02a681daf3ae4b438827b757cab689878bcfd10449a43ebea033c8e3ccf3f0e06b5aa538a0d7f589db93114982a93b38a20230a82e66f688af85e9aab1b36ee5a0be670d71aeb43b8029b5a05d39658c9eb368bdd9464a9be50b51e095e429231663006d7db7bd08a63525821bee268f4ad05f650b5379b6395d0efbd9bebd54730037bee1118ded5400a"),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid credentials")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input data",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="object",
     *                 @OA\Property(property="message", type="string", example="Invalid input data"),
     *                 @OA\Property(property="status_code", type="integer", example=422)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Inefficient data",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="object",
     *                 @OA\Property(property="message", type="string", example="Inefficient data"),
     *                 @OA\Property(property="status_code", type="integer", example=409)
     *             )
     *         )
     *     )
     * )
     */
    public function authenticate(Request $request)
    {
        $validation = Validator::make(request()->json()->all(), [
            'login' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        if ($validation->fails()) {
            throw new InvalidRequestDataException();
        }

        return new JsonResponse($this->authService->login($request));
    }

    /**
     * @OA\Post(
     *     path="/api/login-external",
     *     operationId="loginExternal",
     *     tags={"Auth"},
     *     summary="External login for users",
     *     description="Authenticate user via external login provider using an ID token and provider type.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id_token", type="string", description="ID token from external provider"),
     *             @OA\Property(property="type", type="string", enum={"google", "facebook", "apple"}, description="Type of external provider")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=31536000),
     *             @OA\Property(property="access_token", type="string", description="Access token"),
     *             @OA\Property(property="refresh_token", type="string", description="Refresh token"),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Invalid input data"),
     *             @OA\Property(property="status_code", type="integer", example=422)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function loginExternal(ExternalLoginService $externalLoginService, Request $request)
    {
        $validation = Validator::make(request()->json()->all(), [
            'id_token' => 'required|string',
            'type' => [
                'required',
                new Enum(ExternalLoginType::class),
            ],
        ]);

        if ($validation->fails()) {
            throw new InvalidRequestDataException();
        }

        return new JsonResponse($this->authService->loginExternal($request));
    }



    /**
     * @OA\Post(
     *     path="/api/auth_guest",
     *     operationId="authenticateGuest",
     *     tags={"Auth"},
     *     summary="Authenticate guest user",
     *     description="Authenticates a guest user with an optional login.",
     *     @OA\RequestBody(
     *         required=false,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImY0YTVlNThmOWU1YmJmZGEzYjBmNDM5YWE4N2IxNzBlNGYzZjRmYjhhMmQ5ZWIyZWEzZTEwMzkxZWIyNjUwZDFhMjU0YzU2NTk2MGQxOWEyIn0.eyJhdWQiOiIyIiwianRpIjoiZjRhNWU1OGY5ZTViYmZkYTNiMGY0MzlhYTg3YjE3MGU0ZjNmNGZiOGEyZDllYjJlYTNlMTAzOTFlYjI2NTBkMWEyNTRjNTY1OTYwZDE5YTIiLCJpYXQiOjE1MzA1MjQ3OTgsIm5iZiI6MTUzMDUyNDc5OCwiZXhwIjoxNTYyMDYwNzk4LCJzdWIiOiIzIiwic2NvcGVzIjpbXX0.cdBtKZbQ1-geBMPKDsfx1tS2KbH7QpkAObgmTjvLk1tIpK1zEXov-5y-l60kxQ6YmPVB5_en8vA1bWZJGMy5hsQw66FoFNixf-P3b-_8k4wWbEYHqhhKP6snFQRkh3aTmG3iMJEzKdIXNh9xIOw0sd1B1KnpenTyHo1RGQ04MmGC5btfDB75ciKWj5LbnRzzU_cKSWppPVXLTklkQcZHHU9mfefIa8AOAXaHut_7oMbssAdiKoArwsUezjDgn5xxpnk71xX9rQcOD6EtkESdP4UafaIWk66NhujCM-n_1hbVRFXDpTnOli50TFIL99mIgpUX2r8CIfbabOC4CM3tgtFFACZkTDV1nCHbU6sfbR7V8H7xjJcumD2NoR4GAQULT9JmCbY2MgYpwYuPLiwv7YP-3UcCGxF0zojECCVexv1tlw0zYicLMiheE2cteRSuUtIrQ1Pdcz6Evp6j1SAAjQUQuVnRS34aVIYvwyQK5rvv0MowDTra_0hbx9AxKZI0jeNxfgOKdBH4Z5fzK1jhw8_5VYPze7FkAOCuxfjFzLzp-AsbzsVncyQ3-UxzYvcs00ZLaZKPl7P7adHWF0ZOrscvuZBQw5kSNr2QNDEH8-DuUeWBs4XoxkwO4wY2nQlgCmaYs86y3RGyxMGjESPyvP7SZNpY2qYp_AYdc01uEjI"),
     *             @OA\Property(property="refresh_token", type="string", example="def50200b2cea03bf9a8d331016eb3e1284affb3366708ac7908b7233951ff00092852e6f088c0b7f0a216ec41cc6c147441a2771ca0f14249ceb23c09bcaf1841930669307417762bbf97529f839c84359792462b17c4c685249f930189ddcee94d592c0d0976774978d26c1acecfbcb95fb860b2cae7cdf7f5970940ea0963bb69857c7a7f9e26e42795bbb4b373d2cddfbb4afadec123371295e514d542f23a902403e319a39070a26f839c13952225e420b7ac9c686508a8fc551fbb2d5a72f777010168dd1a6dd10d93bc8c9e4b4212b967413ae02a681daf3ae4b438827b757cab689878bcfd10449a43ebea033c8e3ccf3f0e06b5aa538a0d7f589db93114982a93b38a20230a82e66f688af85e9aab1b36ee5a0be670d71aeb43b8029b5a05d39658c9eb368bdd9464a9be50b51e095e429231663006d7db7bd08a63525821bee268f4ad05f650b5379b6395d0efbd9bebd54730037bee1118ded5400a"),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid credentials")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input data",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="object",
     *                 @OA\Property(property="message", type="string", example="Invalid input data"),
     *                 @OA\Property(property="status_code", type="integer", example=422)
     *             )
     *         )
     *     )
     * )
     */
    public function authenticate_guest(Request $request)
    {
        $validation = Validator::make(request()->json()->all(), [
            'login' => 'nullable|string|max:255',
        ]);

        if ($validation->fails()) {
            throw new InvalidRequestDataException();
        }
        // grab credentials from the request
        $credentials = request()->json()->all();

        try {
            $login = null;
            $user = null;
            if (isset($credentials['login'])) {
                $login = $credentials['login'];
                $user = UserSystem::findGuest($login);
            }
            if (! $user) {
                do {
                    $login = substr(md5('Guest'.uniqid().'WK'.microtime()), 0, 16);
                    $user_login = UserSystem::where('login', $login)->first();
                } while (! empty($user_login));
                $params = [
                    'guest' => 1,
                    'activated' => 1,
                    'blocked' => 0,
                    'roles' => ['ROLE_GUEST'],
                    'login' => $login,
                    'email' => $login,
                    'password' => bcrypt($login),
                ];
                $user = UserSystem::create($params)->fresh();
            }
        } catch (\Throwable $e) {
            dd($e);
        }

        /*
         * Check that user is activated one.
         */
        if ($user->blocked) {
            throw new UserIsBlockedException();
        }

        throw_if(! ($client = PassportClient::where('password_client', 1)->first()), new AccessDeniedHttpException('Passport not configured properly.'));

        $request->request->add([
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => $login,
            'password' => $login,
            'scope' => null,
        ]);
        $request->headers->remove('content-type');

        // Fire off the internal request.
        $proxy = Request::create('oauth/token', 'POST');
        $response = \Route::dispatch($proxy);
        $json = (array) json_decode($response->getContent());

        if (! $response->isSuccessful()) {
            throw new InvalidCredentialsException();
        }

        $json['user'] = new UserResource($user);
        $json['locale'] = app()->getLocale();
        $response->setContent(json_encode($json));

        return $response;
    }


    /**
     * @OA\Post(
     *     path="/api/refresh-token",
     *     operationId="refreshToken",
     *     tags={"Auth"},
     *     summary="Refresh user token",
     *     description="Refreshes the user's access token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="refresh_token", type="string", description="Token to refresh session", example="def50200b2cea03bf9a8d331016eb3e1284affb3366708ac7908b7233951ff00092852e6f088c0b7f0a216ec41cc6c147441a2771ca0f14249ceb23c09bcaf1841930669307417762bbf97529f839c84359792462b17c4c685249f930189ddcee94d592c0d0976774978d26c1acecfbcb95fb860b2cae7cdf7f5970940ea0963bb69857c7a7f9e26e42795bbb4b373d2cddfbb4afadec123371295e514d542f23a902403e319a39070a26f839c13952225e420b7ac9c686508a8fc551fbb2d5a72f777010168dd1a6dd10d93bc8c9e4b4212b967413ae02a681daf3ae4b438827b757cab689878bcfd10449a43ebea033c8e3ccf3f0e06b5aa538a0d7f589db93114982a93b38a20230a82e66f688af85e9aab1b36ee5a0be670d71aeb43b8029b5a05d39658c9eb368bdd9464a9be50b51e095e429231663006d7db7bd08a63525821bee268f4ad05f650b5379b6395d0efbd9bebd54730037bee1118ded5400a")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=31536000),
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImEyOWMyOGI2M2E3ODk2NDhjM2U2ZjEwMGE4MzMzMTBkYzhlMDgxNjhlNzMwNzg1MjhhMGJmYTYyMTFkYzIzZjdkOTNiZjIxMjJiNWE3NjBiIn0.eyJhdWQiOiIyIiwianRpIjoiYTI5YzI4YjYzYTc4OTY0OGMzZTZmMTAwYTgzMzMxMGRjOGUwODE2OGU3MzA3ODUyOGEwYmZhNjIxMWRjMjNmN2Q5M2JmMjEyMmI1YTc2MGIiLCJpYXQiOjE1MDM1NTYwNjksIm5iZiI6MTUwMzU1NjA2OSwiZXhwIjoxNTM1MDkyMDY5LCJzdWIiOiIxMiIsInNjb3BlcyI6W119.RqpyyLNrvLMi4Vxpe85-XNCYFQX9wN3JEXJasMFP4YN82HP3KsdVjxako7oJIL7Kc8Fiy4y2Pl7F5daJBEmcmh_UlvP7699vL9X4WrHLmhc6ukkwdLs7h3rXzKhdJBbfBPejb-oqWHRF2I31vPj8x-RZAPjmyGw8GaWVbWxIqykezJbm3sRAuEzkg_a93jxaRWMmwHeU422Og1L9At6olWkIHF4Te3Y9chhL5nMeojIYFrDbwUKSuEXeA_jURd4oOI__A8FcNroSINvLVi_jU7DSINM3wjY1rujGAh_VxdyQb_Xb14txAOGc4CXEVvA2lilmyaLzEV4LelIt9Yyvypjn6OA9ouy1Dp3j2JUMneJmu4SyTcG29xcUZQeePe8DSKqlknN8T9kbzHY_nlI1LEUxSOVSapKASV1so2Kjly9SxQIbz3P4LHpxEBSXi3Yh-Clv0YfN-Vl2me5wwBw10rQjsN5oYuuYlpfESKhXYiIV3v0rbiv3heKBumC5d2xTQOBcVAspKWuKc7axWdRQZZRyeHFmNIMX-GljmQE1qQYzP2WBA7x-bgisPXAyu5wGs5E4kL23Xj_cQGZUlmIq50bxDv3BeCjek12oI1QX1KjXqIBd5NREvT63F0tEx35spQvIkERMDOznevkGshs8ZS_7H-esS1qZ_ZzGWr1VGXg"),
     *             @OA\Property(property="refresh_token", type="string", example="def50200b2cea03bf9a8d331016eb3e1284affb3366708ac7908b7233951ff00092852e6f088c0b7f0a216ec41cc6c147441a2771ca0f14249ceb23c09bcaf1841930669307417762bbf97529f839c84359792462b17c4c685249f930189ddcee94d592c0d0976774978d26c1acecfbcb95fb860b2cae7cdf7f5970940ea0963bb69857c7a7f9e26e42795bbb4b373d2cddfbb4afadec123371295e514d542f23a902403e319a39070a26f839c13952225e420b7ac9c686508a8fc551fbb2d5a72f777010168dd1a6dd10d93bc8c9e4b4212b967413ae02a681daf3ae4b438827b757cab689878bcfd10449a43ebea033c8e3ccf3f0e06b5aa538a0d7f589db93114982a93b38a20230a82e66f688af85e9aab1b36ee5a0be670d71aeb43b8029b5a05d39658c9eb368bdd9464a9be50b51e095e429231663006d7db7bd08a63525821bee268f4ad05f650b5379b6395d0efbd9bebd54730037bee1118ded5400a")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid credentials")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input data",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="object",
     *                 @OA\Property(property="message", type="string", example="Invalid input data"),
     *                 @OA\Property(property="status_code", type="integer", example=422)
     *             )
     *         )
     *     )
     * )
     */
    public function refreshToken()
    {
        $validation = Validator::make(request()->json()->all(), [
            'refresh_token' => 'required|string',
        ]);

        if ($validation->fails()) {
            throw new InvalidRequestDataException();
        }

        // grab credentials from the request
        $credentials = request()->json()->all();

        if (empty($credentials['refresh_token'])) {
            throw new InvalidCredentialsException();
        }

        $client = PassportClient::where('password_client', 1)->first();

        request()->request->add([
            'grant_type' => 'refresh_token',
            'refresh_token' => $credentials['refresh_token'],
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'scope' => null,
        ]);
        request()->headers->remove('content-type');

        // Fire off the internal request.
        $proxy = Request::create('oauth/token', 'POST');

        $response = \Route::dispatch($proxy);

        $json = (array) json_decode($response->getContent());

        if (! $response->isSuccessful()) {
            throw new InvalidCredentialsException();
        }

        return $response;
    }


    /**
     * @OA\Post(
     *     path="/api/remind-password",
     *     operationId="remindPassword",
     *     tags={"Auth"},
     *     summary="Remind user password",
     *     description="Sends an email to the user with a link to reset their password.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", description="E-mail assigned to user account", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="string", example="Email sended")
     *         )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthenticated."),
     *              @OA\Property(property="details", type="string",nullable=true, example=null),
     *              @OA\Property(property="data", type="string", nullable=true,example=null),
     *              @OA\Property(property="locale", type="string", nullable=true,example=null),
     *          )
     *      ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input data",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="object",
     *                 @OA\Property(property="message", type="string", example="Invalid input data"),
     *                 @OA\Property(property="status_code", type="integer", example=422)
     *             )
     *         )
     *     )
     * )
     */
    public function remindPassword()
    {
        $validation = Validator::make(request()->json()->all(), [
            'email' => 'required|email',
        ]);

        if ($validation->fails()) {
            throw new InvalidRequestDataException(['email' => 'Email is required']);
        }

        $email = request()->email;

        if (! ($user = UserSystem::findAccountToRemind($email))) {
            throw new ActiveUserNotFoundException();
        }

        $remind_token = md5($user->id.'tryRemindWK'.microtime());
        $user->remind_token = $remind_token;
        $user->save();

        $url = Route('admin.auth.change_password', ['token' => $remind_token]);

        \Mail::to($user->email)->send(new ResetMail($user, $url));

        $response = ['data' => __('admin.Email sended')];

        return $response;
    }
}
