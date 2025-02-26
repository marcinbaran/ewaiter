<?php

namespace App\Http\Controllers\Api;

use App\Enum\NotificationTitle;
use App\Exceptions\ApiExceptions\Auth\InvalidCredentialsException;
use App\Exceptions\ApiExceptions\Auth\PasswordRecovery\InvalidSmsCodeException;
use App\Exceptions\ApiExceptions\Auth\UserNotFoundException;
use App\Http\Requests\Api\PhoneRequest;
use App\Http\Requests\Api\UserAuthRequest;
use App\Http\Requests\Api\UserRequest;
use App\Http\Requests\Api\VoucherRequest;
use App\Http\Resources\Api\UserResource;
use App\Managers\UserSystemManager;
use App\Models\FireBaseNotificationV2;
use App\Models\SentPointsHistory;
use App\Models\User;
use App\Models\UserSystem;
use App\Models\Voucher;
use App\Services\NotificationService;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="API Endpoints for managing Users"
 * )
 */
class UserController extends ApiController
{
    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var UserSystemManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        //parent::__construct();
        $this->transService = $service;
        $this->manager = new UserSystemManager($this->transService);
        $this->middleware('auth:api', ['except' => ['store', 'users_auth_code', 'users_auth_code_again']]);
    }




    /**
     * @OA\Get(
     *     path="/api/users",
     *     operationId="getUsers",
     *     tags={"Users"},
     *     summary="Get collection of users",
     *     description="Returns a collection of users.",
     *     security={
     *         {"passport": {}}
     *     },
     *     @OA\Parameter(
     *         name="itemsPerPage",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=20)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="order[id]",
     *         in="query",
     *         description="Order by ID",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="asc")
     *     ),
     *     @OA\Parameter(
     *         name="order[name]",
     *         in="query",
     *         description="Order by name",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/User")
     *             ),
     *             @OA\Property(property="locale", type="string", example="pl")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */



    /**
     * @param UserRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function index(UserRequest $request): AnonymousResourceCollection
    {
        $limit = $request->query->get('itemsPerPage', UserResource::LIMIT);
        $offset = ($request->query->get('page', 1) - 1) * $limit;
        $order = $request->input('order', ['id' => 'asc']);
        $criteria = ['id' => (array) $request->id];

        $user = Auth::user();
        $criteria['user'] = (array) ($user->isEndUserRole() ? [$user->id] : $request->user);

        return UserResource::collection(UserSystem::getRows($criteria, $order, $limit, $offset));
    }


    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     operationId="getUserById",
     *     tags={"Users"},
     *     summary="Get a user resource by id",
     *     description="Returns a specific user by their ID.",
     *     security={
     *         {"passport": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    /**
     * @param UserSystem $user
     *
     * @return UserResource
     */
    public function show(UserSystem $user): UserResource
    {
        $user_logged = Auth::user();
        throw_if($user_logged->isEndUserRole() && $user_logged->id != $user->id, new AccessDeniedHttpException(__('admin.Action prohibited')));

        return new UserResource($user);
    }



    /**
     * @OA\Get(
     *     path="/api/users/me",
     *     operationId="getUserDetails",
     *     tags={"Users"},
     *     summary="Get current user details",
     *     description="Returns the authenticated user's details.",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function me(): UserResource
    {
        return new UserResource(Auth::user());
    }


    /**
     * @OA\Post(
     *     path="/api/users",
     *     operationId="createUser",
     *     tags={"Users"},
     *     summary="Create a user resource",
     *     description="Creates a new user.",
     *     security={
     *         {"passport": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             ref="#/components/schemas/User"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function store(UserRequest $request): UserResource
    {
        return (new UserResource($this->manager->create($request)->fresh()))->withStatusCode(200);
    }


    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     operationId="updateUser",
     *     tags={"Users"},
     *     summary="Update a user resource by id",
     *     description="Updates a user's information.",
     *     security={
     *         {"passport": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             ref="#/components/schemas/User"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */

    public function update(UserRequest $request, UserSystem $user): UserResource
    {
        $user_logged = Auth::user();
        throw_if($user_logged->isEndUserRole() && $user_logged->id != $user->id, new AccessDeniedHttpException(__('admin.Action prohibited')));

        return new UserResource($this->manager->update($request, $user)->fresh());
    }


    public function destroy(UserSystem $user): UserResource
    {
        $user_logged = Auth::user();
        throw_if($user_logged->isEndUserRole() && $user_logged->id != $user->id, new AccessDeniedHttpException(__('admin.Action prohibited')));

        return new UserResource($this->manager->delete($user));
    }
    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     operationId="deleteUser",
     *     tags={"Users"},
     *     summary="Delete a user resource by id",
     *     description="Deletes a user.",
     *     security={
     *         {"passport": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function deleteAccount()
    {
        $deleteUser = auth()->user();
        $resource = new UserResource($this->manager->delete($deleteUser));

        if ($resource) {
            return response()->json(['data' => 200], 200);
        } else {
            return response()->json(['data' => 400], 400);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/vouchers/redeem",
     *     operationId="voucherRedeem",
     *     tags={"Users"},
     *     summary="Redeem a voucher",
     *     description="Allows a user to redeem a voucher.",
     *     security={
     *         {"passport": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="voucher", type="string", description="Voucher code")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Voucher redeemed successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="value", type="number", example=100)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Validation failed"),
     *             @OA\Property(property="details", type="string", example="Field voucher is required.")
     *         )
     *     )
     * )
     */
    public function voucherRedeem(VoucherRequest $request)
    {
        $this->manager->voucherRedeem($request);
        $voucher = Voucher::where('code', $request->get(VoucherRequest::VOUCHER_PARAM_KEY))->first();

        return response()->json(['status' => 200, 'value' => $voucher->value], 200);
    }


    /**
     * @OA\Post(
     *     path="/api/users_auth_code/{id}",
     *     operationId="usersAuthCode",
     *     tags={"Users"},
     *     summary="Authenticate user with auth code",
     *     description="Authenticates a user with a provided auth code.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="auth_code", type="string", description="Authentication code")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful authentication",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="string", example="The user has been successfully authenticated"),
     *             @OA\Property(property="phone", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid authentication or user already authenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="string", example="The user has already been authenticated or is incorrect")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Validation failed"),
     *             @OA\Property(property="details", type="string", example="Field auth_code is required.")
     *         )
     *     )
     * )
     */
    public function users_auth_code(UserAuthRequest $request, User $user)
    {
        return $this->manager->register_auth($request, $user) ?
            ['status' => 200, 'data' => __('admin.The user has been successfully authenticated'), 'phone' => $user->phone] :
            ['status' => 400, 'data' => __('admin.The user has already been authenticated or is incorrect'), 'phone' => $user->phone];
    }



    /**
     * @OA\Post(
     *     path="/api/users_auth_code_again/{id}",
     *     operationId="sendAuthCodeAgain",
     *     tags={"Users"},
     *     summary="Send auth code again",
     *     description="Sends the authentication code again to the user.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Auth code sent successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="string", example="The code has been successfully sent")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error in sending auth code",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="string", example="The user has already been authenticated or is incorrect")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function users_auth_code_again(User $user)
    {
        return $this->manager->register_auth_again($user) ?
            ['status' => 200, 'data' => __('admin.The code has been successfully sent')] :
            ['status' => 400, 'data' => __('admin.The user has already been authenticated or is incorrect'), 'error' => __('admin.The user has already been authenticated or is incorrect')];
    }
    /**
     * @OA\Post(
     *     path="/api/users/send_points",
     *     operationId="sendPoints",
     *     tags={"[MOB] Users"},
     *     summary="[MOB] Send points to another user",
     *     description="Allows a user to send points to another user.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="amount", type="number", description="Amount of points to send"),
     *             @OA\Property(property="receiverId", type="integer", description="ID of the receiving user"),
     *             @OA\Property(property="receiverPhone", type="string", description="Phone number of the receiving user"),
     *             @OA\Property(property="receiverEmail", type="string", description="Email of the receiving user")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Points sent successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="balance", type="number", example=100)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="balance", type="number", example=-1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function send_points(UserRequest $request)
    {
        $params['sender_id'] = auth()->user()->id;
        $params['amount'] = $request->query->get('amount', 0);
        $params['receiver_id'] = $request->query->get('receiverId', 0);
        $params['receiver_phone'] = $request->query->get('receiverPhone', '');
        $params['receiver_email'] = $request->query->get('receiverEmail', '');
        $result = User::send_points($params);

        if ($result['success'] == true) {
            SentPointsHistory::create([
                'sender_id' => $params['sender_id'],
                'receiver_id' => $params['receiver_id'],
                'amount' => $params['amount'],
            ]);

            $body = __(
                'firebase.received_points',
                [
                    'amount' => round($params['amount']),
                    'senderName' => auth()->user()->first_name
                ]
            );

//            NotificationService::sendPushToUser($params['receiver_id'], $body, 'user/points', $params['sender_id'], NotificationTitle::ALERT);
            FireBaseNotificationV2::create([
                'user_id' => $params['receiver_id'],
                'title' => __('firebase.E-waiter'),
                'body' => $body,
                'data' => json_encode([
                    'title' => __('firebase.E-waiter'),
                    'body' => $body,
                    'url' => '/account/points_screen',
                    'object_id' => $params['sender_id'],
                ]),
            ]);
        }

        return $result;
    }
    /**
     * @OA\Post(
     *     path="/api/users/change_password",
     *     operationId="changePassword",
     *     tags={"Users"},
     *     summary="[MOB] Change user's password",
     *     description="Allows the authenticated user to change their password.",
     *     security={
     *         {"passport": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="oldPassword", type="string", description="Current password"),
     *             @OA\Property(property="newPassword", type="string", description="New password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password changed successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Validation failed"),
     *             @OA\Property(property="details", type="string", example="Field newPassword is required.")
     *         )
     *     )
     * )
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'oldPassword' => 'required',
            'newPassword' => 'required',
        ]);

        $newPassword = $request->json('newPassword');
        $oldPassword = $request->json('oldPassword');
        $currentUser = auth()->user();

        if (! $currentUser) {
            throw new UserNotFoundException();
        }

        if (! Hash::check($oldPassword, $currentUser->password)) {
            throw new InvalidCredentialsException();
        }

        $currentUser->password = bcrypt($newPassword);

        return \Response::json(['success' => $currentUser->update()]);
    }
    /**
     * @OA\Post(
     *     path="/api/users/set_phone",
     *     operationId="setPhone",
     *     tags={"Users"},
     *     summary="Set user's phone number",
     *     description="Allows the authenticated user to set their phone number.",
     *     security={
     *         {"passport": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="phone", type="string", description="Phone number")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Phone number set successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Validation failed"),
     *             @OA\Property(property="details", type="string", example="Field phone is required.")
     *         )
     *     )
     * )
     */
    public function setPhone(PhoneRequest $request)
    {
        $request->validate([
            'phone' => 'required|string|unique:users,phone|max:20',
        ]);

        $this->manager->setPhone($request, auth()->user());

        return response()->noContent();
    }
    /**
     * @OA\Post(
     *     path="/api/users/verify_auth_code",
     *     operationId="verifyAuthCode",
     *     tags={"Users"},
     *     summary="Verify user's authentication code",
     *     description="Verifies the authentication code provided by the user.",
     *     security={
     *         {"passport": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="auth_code", type="integer", description="Authentication code")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Auth code verified successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Validation failed"),
     *             @OA\Property(property="details", type="string", example="Field auth_code is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid authentication code",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Invalid authentication code")
     *         )
     *     )
     * )
     */
    public function verifyAuthCode(Request $request)
    {
        $request->validate([
            'auth_code' => 'required|integer',
        ]);

        return $this->manager->verifyAuthCode($request) ?
            response()->noContent() :
            throw new InvalidSmsCodeException(['auth_code' => $request->get('auth_code')]);
    }
}
