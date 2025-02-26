<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Marketplace\AddressService;
use App\Services\Marketplace\CheckoutService;
use App\Services\Marketplace\MarketplaceService;
use App\Services\Marketplace\OrderHistoryService;
use App\Services\Marketplace\OrdersService;
use App\Services\Marketplace\ProductsService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketplaceController extends Controller
{

    public function __construct(
        private ProductsService $productsService,
        private OrdersService $ordersService,
        private OrderHistoryService $orderHistoryService,
        private AddressService $addressService
    ) {}

    /**
     * @return View|Factory
     */
    public function index()
    {
        $data = $this->productsService->getCategories();
        $latestProducts = $this->productsService->getLatestProducts();
        $dataWithSubCategories = $this->productsService->getSubCategories($data);
        $latestProductsWithImages = $this->productsService->getImagesForLatestProducts($latestProducts);
        $latestProductsWithDefaultVariant = $this->productsService->getDefaultVariantForLatestProducts($latestProductsWithImages);
        $cart = $this->ordersService->getCartToken();

        return view('admin.marketplace.index')->with([
            'data' => $dataWithSubCategories,
            'latestProducts' => $latestProductsWithDefaultVariant,
            'cart' => $cart,
        ]);
    }

    /**
     * @param string $taxonCode
     *
     * @return View|Factory
     */
    public function products(string $taxonCode)
    {
        $data = $this->productsService->getProductsByTaxon($taxonCode);
        $productsWithImages = $this->productsService->getImagesForLatestProducts($data);

        return view('admin.marketplace.products')->with([
            'data' => $data,
            'products' => $productsWithImages,
        ]);
    }

    /**
     * @param string $code
     * @return View|Factory
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function product(string $code)
    {
        $data = $this->productsService->getProduct($code);

        $variants = $this->productsService->getProductVariants($data->variants);
        $images = $this->productsService->getProductImages($data->images);

        // test for multi images
        $testImages = $this->addImages($images);
//        test form variant not inStock
        $testVariants = $this->changeInStock($variants);

        return view('admin.marketplace.product')->with([
            'data' => $data,
            'variants' => $testVariants,
            'images' => $testImages,

        ]);
    }

//    test for variant not inStock
    public function changeInStock($variants)
    {
        $i = 0;

        foreach ($variants as $variant) {
            if ($i === 1) {
                $variant->inStock = false;
            }
            $i++;
        }
        return $variants;
    }

//    test for multi images
    public function addImages($images)
    {
        $newImages = [];

        for ($i = 0; $i < 3; $i++) {
            $newImages[] = $images[0];
        }

        return $newImages;
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'variant' => 'required',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $cartToken = $this->ordersService->getCartToken();
            $variantsInCart = $this->ordersService->showCart();
            $data = $this->ordersService->addToCart($request->variant, $request->quantity);
            if ($data && $validated) {

                return redirect()->route('admin.marketplace.cart')->with([
                    'variantsInCart' => $variantsInCart,
                    'cartToken' => $cartToken,
                    'success' => 'Item added successfully',
                ]);
            } else {
                return redirect()->route('admin.marketplace.cart')->with([
                    'variantsInCart' => $variantsInCart,
                    'cartToken' => $cartToken,
                    'error' => 'Item not found',
                ]);
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.marketplace.cart')->with('error', $e->getMessage());
        }
    }

    public function removeFromCart(Request $request)
    {
        $deleteStatus = $this->ordersService->removeFromCart($request->orderItemId);
        if ($deleteStatus) {

            return redirect()->route('admin.marketplace.cart');
        }
        throw new \Exception('Item not found');

    }

    public function removeCart()
    {
        $data = $this->ordersService->removeCart();
        if ($data) {
            $this->ordersService->showCart();
            return redirect()->route('admin.marketplace.cart');
        }
        throw new \Exception('Error removing cart');
    }

    public function updateCart(Request $request)
    {
        $variantsInCart = $this->ordersService->showCart();


        foreach ($variantsInCart as $key => $product) {
            if ($product->quantity !== intval($request->items[$key]['quantity'])) {
                $updateStatus = $this->ordersService->updateCartItem($product->id, $request->items[$key]['quantity']);
            }
        }

        if ($updateStatus) {
            return redirect()->route('admin.marketplace.cart');
        }
    }

    public function cart()
    {
        $cartToken = $this->ordersService->getCartToken();
        $variantsInCart = $this->ordersService->showCart();
        return view('admin.marketplace.cart')->with([
            'variantsInCart' => $variantsInCart,
            'cartToken' => $cartToken,
        ]);
    }

    public function completeOrder()
    {
        $this->ordersService->completeOrder();
    }

    public function address()
    {
        $data = $this->addressService->getaddress();

        return view('admin.marketplace.address')->with([
            'data' => $data,
        ]);
    }

    public function newaddress()
    {
        $countries = $this->addressService->getCountries();
        return view('admin.marketplace.new-address')->with([
            'countries' => $countries,
        ]);
    }

    public function addAddress(Request $request)
    {
        $billingAddress = $request->input('addAddress_address.billingAddress');
        $this->addressService->addaddress($billingAddress);
        return redirect()->route('admin.marketplace.address');
    }

    public function deleteAddress(Request $request)
    {
        $id = $request->input('id');
        $this->addressService->deleteAddress($id);
        return redirect()->route('admin.marketplace.address');
    }

    public function editAddress(Request $request, $id)
    {
        $address = $this->addressService->getaddressbyid($id);
        $countries = $this->addressService->getCountries();
        return view('admin.marketplace.edit-address')->with([
            'countries' => $countries,
            'address' => $address,
        ]);
    }

    public function updateAddress(Request $request, $id)
    {
        $address = $request->input('addAddress_address.billingAddress');;
        $countries = $this->addressService->getCountries();
        $this->addressService->updateAddress($address, $id);
        return redirect()->route('admin.marketplace.address');
    }

    public function checkout(Request $request)
    {
        $cartToken = $this->ordersService->getCartToken();
        $addressBook = $this->addressService->getaddress();
        return view('admin.marketplace.checkout')->with([
            'cartToken' => $cartToken,
            'addressBook' => $addressBook,
        ]);
    }

    public function checkoutSetAddress(Request $request)
    {
        $cartToken = $this->ordersService->getCartToken();
        $addressBook = $this->addressService->getaddress();
        return view('admin.marketplace.checkout')->with([
            'cartToken' => $cartToken,
            'addressBook' => $addressBook,
        ]);
    }

    public function orderHistory()
    {
        $ordersHistory = $this->orderHistoryService->getOrdersHistory();
        return view('admin.marketplace.order_history', ['ordersHistory' => $ordersHistory]);
    }

    public function getOrderHistoryDetails(Request $request)
    {
        $orderTokenValue = $request->order;
        $orderDetails = $this->orderHistoryService->getOrderHistoryDetails($request->order);
        $products = $this->productsService->getProductsImageForCartSummary($orderDetails['items']);
        $payMethod['name'] = ucfirst(basename($orderDetails['payments']['method']['name']));
        $payMethod['code'] = str_replace(' ', '_', strtolower($payMethod['name']));
        $shipMethod = ['name' => ucfirst(basename($orderDetails['shipments']['method'])), 'price' => $orderDetails['shippingTotal']];
        $countryName = $this->addressService->getCountryName($orderDetails['shippingAddress']['countryCode']);

        return view('admin.marketplace.order_history_order_details', [
            'orderDetails' => $orderDetails,
            'products' => $products,
            'payMethod' => $payMethod,
            'shipMethod' => $shipMethod,
            'countryName' => $countryName,
        ]);
    }

    public function notVerified()
    {
        request()->session()->flash('alert-warning', __('admin.marketplace not verified'));
        return view('admin.marketplace.notVerified');
    }

}
