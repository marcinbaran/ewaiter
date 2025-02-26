<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\PaymentRequest;
use App\Http\Resources\Api\PaymentResource;
use App\Mail\PaymentErrorMail;
use App\Managers\PaymentManager;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Restaurant;
use App\Models\Settings;
use App\Services\Przelewy24Service;
use App\Services\TpayNotificationService;
use App\Services\TpayService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @OA\Tag(
 *     name="[TENANT] Payment",
 *     description="[TENANT] API Endpoints for managing Payment"
 * )
 */
class PaymentController extends ApiController
{
    /**
     * @var PaymentManager
     */
    private $manager;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['status_p24', 'callback_tpay', 'status_tpay', 'callback_p24']]);
        $this->manager = new PaymentManager();
    }

    /**
     * @OA\Get(
     *     path="/api/payments",
     *     summary="[TENANT] Get a list of payments",
     *     tags={"[TENANT] Payment"},
     *     @OA\Parameter(
     *         name="itemsPerPage",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(ref="#/components/schemas/PaymentRequest_GET/properties/itemsPerPage")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(ref="#/components/schemas/PaymentRequest_GET/properties/page")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Array of payment IDs",
     *         required=false,
     *         @OA\Schema(ref="#/components/schemas/PaymentRequest_GET/properties/id")
     *     ),
     *     @OA\Parameter(
     *         name="bill",
     *         in="query",
     *         description="Array of bill IDs",
     *         required=false,
     *         @OA\Schema(ref="#/components/schemas/PaymentRequest_GET/properties/bill")
     *     ),
     *     @OA\Parameter(
     *         name="order.id",
     *         in="query",
     *         description="Order by ID",
     *         required=false,
     *         @OA\Schema(ref="#/components/schemas/PaymentRequest_GET/properties/order.id")
     *     ),
     *     @OA\Parameter(
     *         name="order.createdAt",
     *         in="query",
     *         description="Order by creation date",
     *         required=false,
     *         @OA\Schema(ref="#/components/schemas/PaymentRequest_GET/properties/order.createdAt")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of payments",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/PaymentResource"))
     *     )
     * )
     */
    public function index(PaymentRequest $request): AnonymousResourceCollection
    {
        $limit    = $request->query->get('itemsPerPage', PaymentResource::LIMIT);
        $offset   = ($request->query->get('page', 1) - 1) * $limit;
        $order    = $request->input('order', ['id' => 'asc']);
        $criteria = ['id' => (array)$request->id, 'bill' => (array)$request->bill];

        return PaymentResource::collection(Payment::getRows($criteria, $order, $limit, $offset));
    }

    /**
     * @OA\Post(
     *     path="/api/payments",
     *     summary="[TENANT] Create a new payment",
     *     tags={"[TENANT] Payment"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/PaymentRequest_POST")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/PaymentResource")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Payment type not available"
     *     )
     * )
     */
    public function store(PaymentRequest $request, Przelewy24Service $payment24Service, TpayService $tpayService)
    {
        $references = $this->getParams($request, ['bill']);
        $bill       = Bill::find($references['bill']['id']);

        $response['tpay_skip'] = false;
        if ($bill->price - $bill->points_value == '0.00') {
            $response['tpay_skip'] = true;

            return $response;
        }

        throw_if(!$this->manager->checkIfAvailable($bill->paid_type),
            new AccessDeniedHttpException(__('admin.Payment type not available')));
        switch ($bill->paid_type) {
            case 'card_tpay':
                return self::store_tpay($request, $tpayService);
            default:
                throw(new AccessDeniedHttpException(__('admin.Payment type not available')));
        }
    }


    /**
     * @OA\Post(
     *     path="/api/payments/tpay",
     *     summary="[TENANT] Handle Tpay payment",
     *     tags={"[TENANT] Payment"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/PaymentRequest_POST")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tpay payment processed",
     *         @OA\JsonContent(ref="#/components/schemas/PaymentResource")
     *     )
     * )
     */
    public function store_tpay(PaymentRequest $request, TpayService $tpayService)
    {
        $params            = $this->getParams($request, ['email', 'bank_id']);
        $references        = $this->getParams($request, ['bill']);
        $bill              = Bill::find($references['bill']['id']);
        $params['bill_id'] = $bill->id;
        $params['hash']    = substr(md5($bill->id . microtime() . 'WK'), 0, 16);
        $name              = (isset($bill->user) && $bill->user->first_name) ? $bill->user->first_name . ' ' . $bill->user->last_name : 'Uzytkownik id: ' . $bill->user_id;

        $hostname = Restaurant::getCurrentRestaurant()->hostname;

        $paramsTpay = [
            'amount'      => $bill->amount(),
            'description' => 'Bill #' . $bill->id,
            'crc'         => $request->getSession()->getId() . '_' . $bill->id . '_' . date('YmdHis'),
            // unikalny ciąg znaków
            'result_url'  => URL::Route('payments.status_tpay'),
            'return_url'  => 'https://e-waiter.pl/callback_tpay?h=' . $params['hash'] . '&billId=' . $references['bill']['id'] . '&hostname=' . $hostname,
//            'return_url' =>  URL::Route('payments.callback_tpay', ['h'=>$params['hash']]),
            'email'       => $params['email'],
            'name'        => $name,
            'group'       => (int)$params['bank_id'],
            'accept_tos'  => 1,
        ];

        $params['p24_session_id'] = $paramsTpay['crc'];
        $params['p24_amount']     = $this->manager->getTpayTransactionAmount($paramsTpay['amount']);
        $params['p24_currency']   = 'PLN';
        $params['type']           = $request->get('type');
        $payment                  = DB::connection('tenant')->transaction(function () use ($params) {
            return Payment::create($params);
        });

        $tpayService->addValues($paramsTpay);
        $responseService = $tpayService->createTransaction();

        DB::connection('tenant')->transaction(function () use ($payment, $responseService) {
            $payment->update([
                'url'               => empty($responseService['url']) ? $payment->url : $responseService['url'],
                'p24_last_response' => $responseService,
                'p24_token'         => empty($responseService['token']) ? $payment->p24_token : $responseService['token'],
            ]);
            $payment->fresh();
        });

        if (!isset($responseService['error']) || 0 != (int)$responseService['error']) {
            $restaurant_email = Settings::getSetting('kontakt', 'email', true, false);
            if ($restaurant_email) {
                \Mail::to($restaurant_email)->send(new PaymentErrorMail($payment, json_encode($responseService)));
            }
            \Mail::to(app('config')['app']['dev_mail'])->send(new PaymentErrorMail($payment,
                json_encode($responseService)));

            return response()->json($responseService);
        }

        return new PaymentResource($payment);
    }

    /**
     * @OA\Get(
     *     path="/api/payments/callback_tpay/{h}",
     *     summary="[TENANT] Tpay callback",
     *          tags={"[TENANT] Payment"},
     *     @OA\Parameter(
     *         name="h",
     *         in="path",
     *         description="Hash value",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Callback response",
     *         @OA\JsonContent(type="boolean")
     *     )
     * )
     */
    public function callback_tpay($h, TpayService $tpayService)
    {
        if ($h) {
            $payment = Payment::where('hash', $h)->first();
            if ($payment) {
                $transactionId   = $payment->p24_token;
                $responseService = $tpayService->status($transactionId);
                if (isset($responseService['transaction']['status']) && 'correct' == $responseService['transaction']['status']) {
                    return response()->json(true);
                }

                return response()->json(false);
            } else {
                return response()->json(false);
            }
        }

        return response()->json(true);
    }

    /**
     * @OA\Post(
     *     path="/api/payments/status_tpay",
     *     summary="[TENANT] Tpay payment status",
     *          tags={"[TENANT] Payment"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/PaymentRequest_PAYMENTS_STATUS")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status updated",
     *         @OA\JsonContent(type="string", example="TRUE")
     *     )
     * )
     */
    public function status_tpay(Request $request, TpayNotificationService $tpayNotificationService)
    {
        $params = $this->getParams($request, ['tr_id' => '', 'tr_crc' => '', 'title']);
        Log::info('status_tpay', $params);

        $payment = Payment::where('p24_session_id', $params['tr_crc'])->first();

        $responseService = $tpayNotificationService->verify();

        DB::connection('tenant')->transaction(function () use ($payment, $responseService) {
            $isError = (!isset($responseService['error']) || 0 != (int)$responseService['error'] || $responseService['response']['tr_status'] != true);
            if ($isError) {
                $payment->p24_last_response = $responseService;
                Log::info($responseService);
                $restaurant_email = Settings::getSetting('kontakt', 'email', true, false);
                if ($restaurant_email) {
                    \Mail::to($restaurant_email)->send(new PaymentErrorMail($payment, json_encode($responseService)));
                }
                \Mail::to(app('config')['app']['dev_mail'])->send(new PaymentErrorMail($payment,
                    json_encode($responseService)));
            }
            $payment->paid        = $isError ? $payment->paid : true;
            $payment->transferred = $isError ? 1 : 0;
            $payment->save();
        });

        echo 'TRUE';
        exit;
    }

    public function bank_list()
    {
        $restaurant = Restaurant::getCurrentRestaurant();
        if (!$restaurant) {
            return response()->json(['error' => 'No restaurant found'], 404);
        }

        $merchantDetails = $restaurant->online_payment_provider_account()->first();
        if (!$merchantDetails) {
            return response()->json(['error' => 'No merchant found'], 404);
        }
        $merchantId = Crypt::decryptString($merchantDetails->login);

        return response()->json(TpayService::getBankList($merchantId));
    }
}
