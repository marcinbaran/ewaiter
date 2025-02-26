<?php

namespace App\Http\Controllers\Actions\Api\Order;

use App\Commands\Address\CreateAddressCommand;
use App\Commands\Bill\CreateBillCommand;
use App\Commands\Order\CreateOrdersCommand;
use App\DTO\Orders\CreateAddressDTO;
use App\DTO\Orders\CreateBillDTO;
use App\DTO\Orders\CreateOrderDTO;
use App\Enum\DeliveryMethod;
use App\Http\Controllers\Actions\Api\ApiCommandActionBase;
use App\Http\Requests\Api\PlaceOrderRequest;
use App\Http\Resources\Api\BillResource;
use App\Http\Validators\Orders\PlaceOrderValidator;
use App\Http\Validators\ValidatorInterface;
use App\Models\Bill;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Post(
 *     path="/api/place_order",
 *     operationId="placeOrder",
 *     tags={"[TENANT] Order"},
 *     summary="Place a new order",
 *     description="[TENANT] This endpoint places a new order and returns the bill information.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="customer_name", type="string", description="Name of the customer"),
 *             @OA\Property(property="customer_phone", type="string", description="Phone number of the customer"),
 *             @OA\Property(property="items", type="array", @OA\Items(
 *                 @OA\Property(property="id", type="integer", description="ID of the item"),
 *                 @OA\Property(property="quantity", type="integer", description="Quantity of the item")
 *             )),
 *             @OA\Property(property="delivery_method", type="string", enum={"DELIVERY_TO_ADDRESS", "PICKUP"}, description="Method of delivery"),
 *             @OA\Property(property="address", type="object", @OA\Property(property="street", type="string", description="Street name"),
 *             @OA\Property(property="city", type="string", description="City name"),
 *             @OA\Property(property="postcode", type="string", description="Postal code"),
 *             @OA\Property(property="country", type="string", description="Country")),
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order placed successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="object", ref="#/components/schemas/BillResource")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Invalid input data")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="An error occurred while processing the request.")
 *         )
 *     )
 * )
 */

class PlaceOrderCommandAction extends ApiCommandActionBase
{
    public function __construct(PlaceOrderRequest $request)
    {
        parent::__construct($request);
    }

    public function handle()
    {

        $addressDto = CreateAddressDTO::createFromRequest($this->request);
        if ($addressDto !== null) {
            $this->commandBus->send(new CreateAddressCommand($addressDto));
        }

        $billDto = CreateBillDTO::createFromRequest($this->request, $addressDto);
        $this->commandBus->send(new CreateBillCommand($billDto));

        $orderDtoCollection = CreateOrderDTO::createCollectionFromRequest($this->request, $billDto->getId());
        $this->commandBus->send(new CreateOrdersCommand($orderDtoCollection));

        $response = response([
            'data' => new BillResource($this->prepareBill(Bill::find($billDto->getId()), $billDto->getDeliveryMethod())),
        ]);
        $response->header('Access-Control-Allow-Origin', '*');

        $response->send();
    }

    protected function getValidator(): ?ValidatorInterface
    {
        return new PlaceOrderValidator();
    }

    protected function prepareBill(Bill $bill, DeliveryMethod $deliveryMethod): Bill
    {
        $this->appendOrders($bill);
        $this->appendRestaurantName($bill);

        if ($deliveryMethod === DeliveryMethod::DELIVERY_TO_ADDRESS) {
            $this->appendAddress($bill);
        }
        return $bill;
    }

    protected function appendOrders(Bill $bill): void
    {
        $bill->with_orders = true;
    }

    protected function appendAddress(Bill $bill): void
    {
        $bill->with_address = true;
    }

    protected function appendRestaurantName(Bill $bill): void
    {
        $bill->restaurant_name = Restaurant::getCurrentRestaurant()->name;
    }
}
