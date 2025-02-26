<?php

namespace App\Http\Controllers\Actions\Api\Order;

use App\DTO\Orders\ValidateBillDTO;
use App\Http\Controllers\Actions\Api\ApiCommandActionBase;
use App\Http\Requests\Api\ValidateCartRequest;
use App\Http\Validators\Orders\ValidateCartValidator;
use App\Http\Validators\ValidatorInterface;
use App\Managers\BillManager;
use App\Managers\DeliveryRangeManager;
/**
 * @OA\Post(
 *     path="/api/validate_cart",
 *     summary="[TENANT] Validate cart details and calculate costs",
 *     tags={"[TENANT] Order"},
 *     description="Validates cart details and calculates delivery cost, package price, and service charge based on the cart items.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"items"},
 *                 @OA\Property(
 *                     property="items",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         required={"product_id", "quantity"},
 *                         @OA\Property(property="product_id", type="integer", example=1),
 *                         @OA\Property(property="quantity", type="integer", example=2)
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="total_price",
 *                     type="number",
 *                     format="float",
 *                     description="The total price of the cart items",
 *                     example=150.00
 *                 ),
 *                 @OA\Property(
 *                     property="discount",
 *                     type="number",
 *                     format="float",
 *                     description="Total discount applied to the cart",
 *                     example=10.00
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful validation and cost calculation",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="delivery_cost", type="number", format="float", example=5.00),
 *             @OA\Property(property="package_price", type="number", format="float", example=2.00),
 *             @OA\Property(property="service_charge", type="number", format="float", example=3.00)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid request data",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", example="Invalid cart data provided.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", example="An unexpected error occurred.")
 *         )
 *     ),
 *     security={{"bearerAuth":{}}}
 * )
 */
class ValidateCartCommandAction extends ApiCommandActionBase
{
    public function __construct(ValidateCartRequest $request)
    {
        parent::__construct($request);
    }

    public function handle()
    {
        $billDto = ValidateBillDTO::createFromRequest($this->request);
        $billDto->setTotalPrice(BillManager::getTotalFoodPrice($billDto));
        $billDto->setDiscount(BillManager::getTotalDiscount($billDto));

        $responseContent = [
            'delivery_cost' => DeliveryRangeManager::getDeliveryCost($billDto),
            'package_price' => BillManager::getPackageCost($billDto),
            'service_charge' => BillManager::getServiceCharge($billDto),
        ];

        $response = response($responseContent);

        $response->header('Access-Control-Allow-Origin', '*');

        $response->send();
    }

    protected function getValidator(): ?ValidatorInterface
    {
        return new ValidateCartValidator();
    }
}
