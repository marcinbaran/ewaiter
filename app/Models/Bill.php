<?php

namespace App\Models;

use App\Decorators\MoneyDecorator;
use App\Enum\DeliveryMethod;
use App\Events\BillWasCreatedEvent;
use App\Helpers\MoneyFormatter;
use App\Mail\OrderCancelClientMail;
use App\Mail\OrderCancelClientRestaurant;
use App\Models\Bill\BillStatusLifeCycleTrait;
use App\Services\GlobalSearch\Searchable;
use App\Services\ReferringUserService;
use App\Services\TicketService;
use Bkwld\Croppa\Facades\Croppa;
use Carbon\Carbon;
use Hyn\Tenancy\Facades\TenancyFacade;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

/**
 * @OA\Schema(
 *     schema="Bill",
 *     type="object",
 *     description="Bill model",
 *     @OA\Property(property="id", type="integer", description="Unique identifier for the bill", example=1),
 *     @OA\Property(property="price", type="number", format="float", description="Total price of the bill", example=100.50),
 *     @OA\Property(property="paid", type="boolean", description="Payment status", example=true),
 *     @OA\Property(property="discount", type="number", format="float", description="Discount applied to the bill", example=10.00),
 *     @OA\Property(property="comment", type="string", description="Additional comments", example="Please deliver quickly"),
 *     @OA\Property(property="time_wait", type="integer", description="Time wait in minutes", example=15),
 *     @OA\Property(property="status", type="integer", description="Status of the bill", enum={0, 1, 2, 3, 4, 5}),
 *     @OA\Property(property="payment_at", type="string", format="date-time", description="Payment timestamp", example="2023-07-30T15:20:30Z"),
 *     @OA\Property(property="games_payment", type="number", format="float", description="Payment for games", example=5.00),
 *     @OA\Property(property="tip", type="number", format="float", description="Tip amount", example=2.50),
 *     @OA\Property(property="room_delivery", type="integer", description="Room delivery identifier", example=101),
 *     @OA\Property(property="paid_type", type="string", description="Type of payment", enum={"cash", "card", "room"}),
 *     @OA\Property(property="user_id", type="integer", description="Identifier of the user", example=1),
 *     @OA\Property(property="address_id", type="integer", description="Identifier of the address", example=1),
 *     @OA\Property(property="table_number", type="integer", description="Table number", example=12),
 *     @OA\Property(property="personal_pickup", type="boolean", description="Indicates if the order is for personal pickup", example=true),
 *     @OA\Property(property="phone", type="string", description="Phone number for the order", example="+123456789"),
 *     @OA\Property(property="delivery_type", type="string", description="Type of delivery", example="delivery_address"),
 *     @OA\Property(property="delivery_time", type="string", format="date-time", description="Expected delivery time", example="2023-07-30T15:45:00Z"),
 *     @OA\Property(property="cart", type="boolean", description="Indicates if the bill is in the cart", example=false),
 *     @OA\Property(property="delivery_cost", type="number", format="float", description="Cost of delivery", example=5.00),
 *     @OA\Property(property="service_charge", type="number", format="float", description="Service charge applied", example=2.00),
 *     @OA\Property(property="points", type="integer", description="Points used in the transaction", example=100),
 *     @OA\Property(property="points_value", type="number", format="float", description="Value of points used", example=1.00),
 *     @OA\Property(property="points_refunded", type="boolean", description="Indicates if points were refunded", example=false),
 *     @OA\Property(property="ticket", type="integer", description="Ticket status", enum={0, 1, 2}),
 *     @OA\Property(property="user_res_id", type="integer", description="User reservation ID", example=1),
 *     @OA\Property(property="released_at", type="string", format="date-time", description="Release timestamp", example="2023-07-30T15:30:00Z")
 * )
 */
class Bill extends Model implements Searchable, Auditable
{
    use ModelTrait,
        Notifiable,
        UsesTenantConnection,
        AuditableTrait,
        BillStatusLifeCycleTrait;

    /**
     * @var int
     */
    public const STATUS_NEW = 0;

    /**
     * @var int
     */
    public const STATUS_ACCEPTED = 1;

    /**
     * @var int
     */
    public const STATUS_READY = 2;

    /**
     * @var int
     */
    public const STATUS_RELEASED = 3;

    /**
     * @var int
     */
    public const STATUS_CANCELED = 4;

    /**
     * @var int
     */
    public const STATUS_COMPLAINT = 5;

    /**
     * @var int
     */
    public const TIME_WAIT = 15; //15min

    /**
     * @var bool
     */
    public const PAID_NO = false;

    /**
     * @var bool
     */
    public const PAID_YES = true;

    /**
     * @var string
     */
    public const PAID_TYPE_CASH = 'cash';

    /**
     * @var string
     */
    public const PAID_TYPE_CARD = 'card';

    /**
     * @var string
     */
    public const PAID_TYPE_ROOM = 'room';

    /**
     * @var float Cost games
     */
    public const GAMES_PAYMENT = 5;

    /**
     * @var int
     */
    public const TICKET_NA = 0;

    /**
     * @var int
     */
    public const TICKET_TO_SEND = 1;

    /**
     * @var int
     */
    public const TICKET_SENT = 2;

    /**
     * @var array
     */
    protected $fillable = [
        'price',
        'paid', // FIXME: should this be fillable?
        'discount',
        'comment',
        'time_wait',
        'status',
        'payment_at',
        'games_payment',
        'tip',
        'room_delivery',
        'paid_type',
        'user_id',
        'address_id',
        'table_number',
        'personal_pickup',
        'phone',
        'delivery_type',
        'delivery_time',
        'cart',
        'delivery_cost',
        'service_charge',
        'points',
        'points_value',
        'points_refunded',
        'ticket',
        'user_res_id',
        'released_at',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at',
        'price',
    ];

    protected $dispatchesEvents = [
        'created' => BillWasCreatedEvent::class,
    ];

    protected static $statusName = [
        self::STATUS_NEW => 'new',
        self::STATUS_ACCEPTED => 'accepted',
        self::STATUS_READY => 'ready',
        self::STATUS_RELEASED => 'released',
        self::STATUS_CANCELED => 'canceled',
        self::STATUS_COMPLAINT => 'complaint',
    ];

    protected static $ticketStatusName = [
        self::TICKET_NA => 'Not applicable',
        self::TICKET_TO_SEND => 'To send',
        self::TICKET_SENT => 'Sent',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'price' => 0,
        'status' => self::STATUS_NEW,
        'paid' => self::PAID_NO,
        'discount' => 0,
        'paid_type' => self::PAID_TYPE_CASH,
        'games_payment' => 0,
        'tip' => 0,
        'personal_pickup' => false,
        'delivery_cost' => 0,
        'ticket' => self::TICKET_NA,
    ];

    /**
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'bill_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class, 'table_number', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_delivery', 'id');
    }

    /**
     * @return HasOne
     */
    public function address(): HasOne
    {
        return $this->hasOne(Address::class, 'id', 'address_id');
    }

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(UserSystem::class, 'id', 'user_id');
    }

    /**
     * @return HasOne
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * @return HasOne
     */
    public function refund(): HasOne
    {
        return $this->hasOne(Refund::class);
    }

    /**
     * @return HasOne
     */
    public function refunds(): HasMany
    {
        return $this->HasMany(Refund::class);
    }

    public function scopePlaced(Builder $builder)
    {
        $builder->where('cart', 0);
    }

    /**
     * @param array $criteria
     * @param array $order
     * @param int $limit
     * @param int $offset
     *
     * @return Collection
     */
    public static function getRows(array $criteria, array $order, int $limit, int $offset): Collection
    {
        $query = self::select();
        if (! $criteria['noLimit']) {
            $query->offset($offset)->limit($limit);
        }
        if (! empty($criteria['id'])) {
            $query->whereIn('id', $criteria['id']);
        }
        if (! empty($criteria['status'])) {
            $query->whereIn('status', $criteria['status']);
        }
        if (! empty($criteria['roomDelivery'])) {
            $query->whereIn('room_delivery', $criteria['roomDelivery']);
        }
        if (! empty($criteria['paidType'])) {
            $query->whereIn('paid_type', $criteria['paidType']);
        }
        if (! empty($criteria['user'])) {
            $query->whereIn('user_id', $criteria['user']);
        }
        if (null !== ($criteria['paid'] ?? null)) {
            $query->where('paid', '=', $criteria['paid']);
        }

        if (! $criteria['withCarts']) {
            $query->where('cart', 0);
        }

        if (! empty($criteria['date'])) {
            $query->whereDate('created_at', $criteria['date']);
        }

        if (! empty($criteria['fromDate'])) {
            $query->whereDate('created_at', '>=', $criteria['fromDate']);
        }

        if (! empty($criteria['toDate'])) {
            $query->whereDate('created_at', '<=', $criteria['toDate']);
        }

        if ($criteria['onlyWithGuestBills']) {
            $query->where('user_id', '=', $criteria['onlyWithGuestBills']);
        }

        if (! empty($criteria['withOrders'])) {
            $query->with(['orders']);
        }

        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                $query->orderBy(self::decamelize($column), $direction);
            }
        }

        return $query->get();
    }

    /**
     * @param array $criteria
     * @param array $order
     * @param int $limit
     * @param int $offset
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getRestaurantsRows(array $criteria, array $order, int $limit, int $offset): \Illuminate\Support\Collection
    {
        $restaurants = ! empty($criteria['restaurant_id']) ? Restaurant::whereIn('id', $criteria['restaurant_id'])->get() : Restaurant::get();
        $collection_bills = new Collection;
        if ($restaurants) {
            foreach ($restaurants as $key => $restaurant) {
                config(['database.connections.tenant.database' => $restaurant->hostname]);
                \DB::reconnect('tenant');

                $query = self::select(\DB::raw('*,"'.$restaurant->name.'" AS restaurant_name,"'.$restaurant->id.'" AS restaurant_id, "'.$restaurant->hostname.'" AS restaurant_hostname'));

                $query->with(['orders', 'orders.dish', 'address']);
                /*
                if (!empty($criteria['id'])) {
                    $query->whereIn('id', $criteria['id']);
                }*/
                if (! empty($criteria['status'])) {
                    $query->whereIn('status', $criteria['status']);
                }
                if (! empty($criteria['roomDelivery'])) {
                    $query->whereIn('room_delivery', $criteria['roomDelivery']);
                }
                if (! empty($criteria['paidType'])) {
                    $query->whereIn('paid_type', $criteria['paidType']);
                }

                if (null !== ($criteria['paid'] ?? null)) {
                    $query->where('paid', '=', $criteria['paid']);
                }

                if (! $criteria['withCarts']) {
                    $query->where('cart', 0);
                }

                if (! empty($criteria['date'])) {
                    $query->whereDate('created_at', $criteria['date']);
                }

                if (! empty($criteria['fromDate'])) {
                    $query->whereDate('created_at', '>=', $criteria['fromDate']);
                }

                if (! empty($criteria['toDate'])) {
                    $query->whereDate('created_at', '<=', $criteria['toDate']);
                }

                if ($criteria['onlyWithGuestBills']) {
                    $query->where('user_id', '=', $criteria['onlyWithGuestBills']);
                }

                if (! empty($criteria['user'])) {
                    $query->whereIn('user_id', $criteria['user']);
                }
                $collection_bills = $collection_bills->toBase()->merge($query->get());
            }
            /*if(\Request::ip() == '91.192.164.50'){
                dd($collection_bills->where('restaurant_id',35)->count());
            }*/
            $totalPages = $collection_bills->count();

            if (! empty($order)) {
                foreach ($order as $column => $direction) {
                    $collection_bills = $direction == 'asc' ? $collection_bills->sortBy(self::decamelize($column)) : $collection_bills->sortByDesc(self::decamelize($column));
                }
            }
            if (! $criteria['noLimit']) {
                $collection_bills = $collection_bills->slice($offset)->take($limit);
            }
        }

        return $collection_bills;
    }

    public function markPaidOrders()
    {
        $paid = $this->paid;
        $orders = $this->orders()->where('status', '!=', Order::STATUS_CANCELED)->get();
        $orders->map(function (Order $order, $key) use ($paid) {
            $order->update(['paid' => $paid]);
        });
    }

    public function checkPhoneNotifications()
    {
        $notify_after_minutes = config('serwersms.notify_after_minutes') ? config('serwersms.notify_after_minutes') : 8;
        $time_passed = Carbon::now()->subMinutes($notify_after_minutes)->format('Y-m-d H:i:s');
        $time_passed_24 = Carbon::now()->subDays(1)->format('Y-m-d H:i:s');

        $bills = self::where('status', 0)
            ->where('cart', 0)
            ->where('paid', 1)
            ->where('updated_at', '<', $time_passed)
            ->where('updated_at', '>', $time_passed_24)
            ->orderBy('id', 'desc')
            ->first();
        if (! $bills) {
            PhoneNotification::where('active', true)->update([
                'active' => false,
            ]);
        }
    }

    public function sendTickets()
    {
        $ticketService = new TicketService();
        /*if(!$ticketService->test())
            return false;*/

        $address = [];
        if ($this->address()->count()) {
            foreach ($this->address()->get() as $val) {
                $address[] = [
                    'imie' => $val['name'],
                    'nazwisko' => $val['surname'],
                    'ulica' => $val['street'],
                    'numer_budynku' => $val['building_number'].' '.$val['house_number'],
                    'pietro' => $val['floor'],
                    'kod' => $val['postcode'],
                    'miasto' => $val['city'],
                    'telefon' => $this->phone,
                    'nip' => $val['nip'],
                    'nazwa_firmy' => $val['company_name'],
                ];
            }
        } else {
            $address[] = [
                'telefon' => $this->phone,
            ];
        }
        $orders = [];
        foreach ($this->orders()->get() as $order) {
            $additions = [];
            if (count($order->getAdditions())) {
                foreach ($order->getAdditions() as $addition) {
                    $additions[] = [
                        'id' => $addition->id,
                        'nazwa' => gtrans('additions.'.$addition->name),
                    ];
                }
            }

            $orders[] = [
                'id' => $order->id,
                'nazwa' => isset($order->dish) ? gtrans('dishes.'.$order->dish->name) : 'Brak dania!',
                'ilosc' => $order->quantity,
                'uwagi' => '',
                'dodatek' => $additions,
            ];
        }

        $ticketService->method('/api/DaneRachunku/Print');

        $printer_name = Settings::getSetting('drukarka', 'nazwa_drukarki', true, false);
        if (! $printer_name) {
            $printer_name = 'Brak drukarki';
        }
        $data = [
            'daneRachunku' => [
                'id' => $this->id,
                'nrZamowienienia' => $this->id,
                'data' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
                'rodzaj' => $this->getTypeDelivery(),
                'rodzajPlatnosci' => $this->getTypePayment(),
                'czasDostarczenia' => $this->delivery_time,
                'logo' => '',
                'pozycjeZamowienia' => $orders,
                'adres' => $address,
                'uwagi' => $this->comment,
            ],
            'printSettings' => [
                'Copies' => 1,
                'FromPage' => 0,
                'ToPage' => 0,
                'PrinterName' => $printer_name,
            ],
        ];
        $ticketService->data($data);
        $result = $ticketService->send();
        if (isset($result->Success) && $result->Success) {
            $this->update(['ticket' => 2]);

            return true;
        }

        return false;
    }

    public function totalPrice()
    {
        return $this->price - $this->discount - $this->points_value + $this->games_payment + $this->delivery_cost;
    }

    public function amount()
    {
        return number_format($this->totalPrice(), 2, '.', '');
    }

    public function price_to_display()
    {
        return $this->amount();
    }

    public function getPackagePrice()
    {
        $package_price = 0;
        foreach ($this->orders as $order) {
            $package_price += ($order->package_price * $order->quantity);
        }

        return number_format($package_price, 2, '.', '');
    }

    public function getDishPrice()
    {
        $price = 0;
        foreach ($this->orders as $order) {
            $price = $price + ($order->price * $order->quantity);
            $price = $price + ($order->additions_price);
        }

        return number_format($price, 2, '.', '');
    }

    public function getFullPrice(): float
    {
        $price = 0;
        foreach ($this->orders as $order) {
            $price += ($order->price * $order->quantity);
            $price += ($order->package_price * $order->quantity);
            $price += ($order->additions_price);
        }
        $price += $this->delivery_cost;
        $price += $this->service_charge;

        return number_format($price, 2, '.', '');
    }

    public function getDiscountedPrice(): float
    {
        return MoneyFormatter::format($this->getFullPrice() - $this->discount);
    }

    public function getPriceToPay()
    {
        return $this->getFullPrice() - $this->discount - $this->points_value;
    }

    public static function getStatusName(int $status)
    {
        throw_if(! isset(self::$statusName[$status]), new \Exception('Not found this status!'));

        return self::$statusName[$status];
    }

    public function getStatuses()
    {
        return self::$statusName;
    }

    public function getDeliveryType()
    {
        $deliveryMethod = $this->delivery_type ? DeliveryMethod::from($this->delivery_type) : null;

        if (! $deliveryMethod) {
            if ($this->room_delivery) {
                $deliveryMethod = DeliveryMethod::ROOM_DELIVERY;
            } elseif ($this->table_number) {
                $deliveryMethod = DeliveryMethod::TABLE_DELIVERY;
            } elseif ($this->personal_pickup) {
                $deliveryMethod = DeliveryMethod::PERSONAL_PICKUP;
            } elseif ($this->address_id) {
                $deliveryMethod = DeliveryMethod::DELIVERY_TO_ADDRESS;
            } else {
                throw new \Exception('Unknown delivery type for Bill: '.$this->id);
            }
        }

        $deliveryTranslation = __('bills.delivery_method.'.$deliveryMethod->value);

        if ($deliveryMethod === DeliveryMethod::ROOM_DELIVERY) {
            $deliveryTranslation .= ': '.$this->room_delivery;
        } elseif ($deliveryMethod === DeliveryMethod::TABLE_DELIVERY) {
            $deliveryTranslation .= ': '.$this->table_number;
        }

        return $deliveryTranslation;
    }

    public function getTypeDelivery()
    {
        if ($this->room_delivery) {
            return __('admin.Room').': '.$this->room_delivery;
        } elseif ($this->table_number) {
            return __('admin.TABLE').': '.$this->table_number;
        } elseif ($this->personal_pickup) {
            return __('orders.Personal pickup');
        } elseif ($this->address_id) {
            return __('orders.Delivery');
        }

        return '';
    }

    public function getTypeDeliveryKey()
    {
        if ($this->room_delivery) {
            return 'delivery_room:'.$this->room_delivery;
        } elseif ($this->table_number) {
            return 'delivery_table: '.$this->table_number;
        } elseif ($this->personal_pickup) {
            return 'delivery_personal_pickup';
        } elseif ($this->address_id) {
            return 'delivery_address';
        }

        return '';
    }

    public function getTypeSettingsDelivery()
    {
        if ($this->room_delivery) {
            return 'delivery_room';
        } elseif ($this->table_number) {
            return 'delivery_table';
        } elseif ($this->personal_pickup) {
            return 'delivery_personal_pickup';
        } elseif ($this->address_id) {
            return 'delivery_address';
        }

        return '';
    }

    public function getTypePayment()
    {
        if ($this->paid_type) {
            return __('orders.'.$this->paid_type);
        }

        return '';
    }

    public function getTypePaymentKey()
    {
        return $this->paid_type;
    }

    /**
     * @param string|null $filter
     * @param int $paginateSize
     * @param array $order
     * @param array $filter_columns
     * @param bool $ajax
     *
     * @return LengthAwarePaginator
     */
    public static function getPaginatedForPanel(string $filter = null, int $paginateSize, array $order = null, array $filter_columns = null, bool $ajax = false): LengthAwarePaginator
    {
        $query = self::select('bills.*')->where('cart', 0)->with('orders');

        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                if ($column != 'paid_price') {
                    $query->orderBy(self::decamelize($column), $direction);
                } else {
                    $query->orderBy(\DB::raw('`price` - `discount`'), $direction);
                }
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        if (! empty($filter_columns)) {
            foreach ($filter_columns as $filter_column => $value) {
                if ($value !== null && $filter_column == 'created_at' && $value != 'all') {
                    $query->whereRaw('DATE(created_at) = \''.$value.'\'');
                } elseif ($value !== null && $filter_column != 'created_at') {
                    $query->where($filter_column, $value);
                }
            }
        }
        if (! $ajax && ((is_array($filter_columns) && ! array_key_exists('created_at', $filter_columns)) || is_null($filter_columns))) {
            $query->whereRaw('DATE(created_at) = \''.Carbon::now()->format('Y-m-d').'\'');
        }

        if (! empty($filter)) {
            $query->where(function ($q) use ($filter) {
                $q->where('bills.price', 'LIKE', '%'.$filter.'%')
                    ->orWhere('bills.discount', 'LIKE', '%'.$filter.'%')
                    ->orWhere('bills.id', 'LIKE', '%'.$filter.'%')
                    ->orWhere('bills.created_at', 'LIKE', '%'.$filter.'%')
                    ->orWhere('bills.time_wait', 'LIKE', '%'.$filter.'%')
                    ->orWhere('bills.delivery_time', 'LIKE', '%'.$filter.'%');
                //->orWhereRaw('(price - discount) AS paid_price LIKE = ?', 'LIKE', '%'.$filter.'%')
            });
        }

        return $query->paginate($paginateSize, ['bills.*']);
    }

    /**
     * @param bool $onlyGroup
     * @param array $order
     *
     * @return LengthAwarePaginator
     */
    public static function getPaginatedForStatistics(array $filter_columns = null): LengthAwarePaginator
    {
        $criteria = [];
        $query = self::select('bills.*')->where('cart', 0)->with('orders');

        $query->orderBy('created_at', 'desc');

        if (! empty($filter_columns)) {
            if (! empty($filter_columns['start'])) {
                $criteria[] = ['created_at', '>=', $filter_columns['start']];
            }
            if (! empty($filter_columns['end'])) {
                $criteria[] = ['created_at', '<=', $filter_columns['end']];
            }
        }
        if (! empty($criteria)) {
            $query->where($criteria);
        }

        $paginateSize = $query->count();

        return $query->paginate($paginateSize, ['bills.*']);
    }

    /**
     * @param string|null $filter
     * @param int $paginateSize
     * @param bool $onlyGroup
     * @param array $order
     *
     * @return LengthAwarePaginator
     */
    public static function getBillsDates(string $filter = null, int $paginateSize, array $order = null, array $filter_columns = null)
    {
        $query = self::selectRaw('DATE(created_at) AS created_at')->where('cart', 0)->distinct()->orderByRaw('DATE(created_at) DESC');

        return $query->pluck('created_at');
    }

    public static function getStatisticByRestaurant(array $criteria, int $restaurant_id): Collection
    {
        $environment = app(\Hyn\Tenancy\Environment::class);
        $restaurant = Restaurant::where('id', $restaurant_id)->first();
        $website = \Hyn\Tenancy\Models\Website::where('uuid', $restaurant->hostname)->first();
        $hostname = \Hyn\Tenancy\Models\Hostname::where('website_id', $website->id)->first();

        $environment->tenant($website);

        $select = self::orderBy('year', 'ASC')
            ->orderBy('month', 'ASC')
            ->orderBy('dates', 'ASC')
            ->where('status', '>', self::STATUS_NEW)
            ->where('paid', true);
        if (! empty($criteria)) {
            $select->where($criteria);
        }

        return $select->selectRaw('count(id) as sums, DATE_FORMAT(created_at,"%d.%m.%Y") as dates, DATE_FORMAT(created_at,"%Y") AS year, DATE_FORMAT(created_at,"%m") AS month')
            ->groupBy('dates')
            ->groupBy('year')
            ->groupBy('month')
            ->get();
    }

    public static function getStatisticRestaurants($request): \Illuminate\Support\Collection
    {
        $stats = [];

        $createdAt = $request->query->get('createdAt');
        if (! empty($createdAt['start'])) {
            $criteria[] = ['created_at', '>=', $createdAt['start']];
        }
        if (! empty($createdAt['end'])) {
            $criteria[] = ['created_at', '<=', $createdAt['end']];
        }
        $website = TenancyFacade::website();
        if ($website) {
            $restaurant = Restaurant::where('hostname', $website->uuid)->first();
            $select = self::where('cart', 0);
            if (! empty($criteria)) {
                $select->where($criteria);
            }

            $order_number = $select->count();

            $select->where('paid', true);
            $value = $select->sum(\DB::raw('price - discount'));
            $provision_value = number_format(($value * ($restaurant->provision / 100)), 2, '.', '');

            /*
             * Provision logged
             */
            $select_provision_logged = clone $select;
            $select_provision_logged->where('user_res_id', $restaurant->id);
            $value_to_provision_logged = $select_provision_logged->sum(\DB::raw('price - discount'));
            $provision_logged_value = number_format(($value_to_provision_logged * ($restaurant->provision_logged / 100)), 2, '.', '');

            /*
             * Provision unlogged
             */
            $select_provision_unlogged = clone $select;
            $select_provision_unlogged->where(function ($q) use ($restaurant) {
                $q->where('user_res_id', null)
                    ->orWhere('user_res_id', '!=', $restaurant->id);
            });
            $value_to_provision_unlogged = $select_provision_unlogged->sum(\DB::raw('price - discount'));
            $provision_unlogged_value = number_format(($value_to_provision_unlogged * ($restaurant->provision_unlogged / 100)), 2, '.', '');

            $order_paid = $select->count();

            $select->where('status', '>', self::STATUS_NEW);
            $order_confirmed_number = $select->count();

            $order_ratio_paid = $order_number ? number_format(($order_paid / $order_number), 2, '.', '') : 0;

            $used_points = $select->sum('points_value');

            /*$sum = number_format(($provision_value - $used_points), 2, '.', '');*/
            $sum = number_format(($provision_logged_value + $provision_unlogged_value - $used_points), 2, '.', '');

            $restaurant_stat = [];
            $restaurant_stat['name'] = '<a href="javascript:;" onclick="showModalRestaurant('.$restaurant->id.');">'.$restaurant->name.'</a>';
            $restaurant_stat['order_number'] = $order_number;
            $restaurant_stat['order_confirmed_number'] = $order_confirmed_number;
            $restaurant_stat['order_ratio_paid'] = $order_ratio_paid;
            $restaurant_stat['value'] = $value;
            $restaurant_stat['provision_logged'] = $restaurant->provision_logged;
            $restaurant_stat['provision_unlogged'] = $restaurant->provision_unlogged;
            $restaurant_stat['provision_logged_value'] = $provision_logged_value;
            $restaurant_stat['provision_unlogged_value'] = $provision_unlogged_value;
            $restaurant_stat['provision_value'] = number_format(($provision_logged_value + $provision_unlogged_value), 2, '.', '');
            $restaurant_stat['used_points'] = $used_points;
            $restaurant_stat['sum'] = $sum;
            $stats['result'][] = $restaurant_stat;
        } else {
            $restaurants = Restaurant::where('visibility', 1)->get();
            if (count($restaurants)) {
                foreach ($restaurants as $restaurant) {
                    $environment = app(\Hyn\Tenancy\Environment::class);
                    $website = \Hyn\Tenancy\Models\Website::where('uuid', $restaurant->hostname)->first();
                    $hostname = \Hyn\Tenancy\Models\Hostname::where('website_id', $website->id)->first();
                    $environment->tenant($website);

                    $select = self::where('cart', 0);
                    if (! empty($criteria)) {
                        $select->where($criteria);
                    }

                    $order_number = $select->count();

                    $select->where('paid', true);
                    $value = $select->sum(\DB::raw('price - discount'));
                    /*$provision_value = number_format(($value * ($restaurant->provision/100)), 2, '.', '');*/

                    /*
                     * Provision logged
                     */
                    $select_provision_logged = clone $select;
                    $select_provision_logged->where('user_res_id', $restaurant->id);
                    $value_to_provision_logged = $select_provision_logged->sum(\DB::raw('price - discount'));
                    $provision_logged_value = number_format(($value_to_provision_logged * ($restaurant->provision_logged / 100)), 2, '.', '');

                    /*
                     * Provision unlogged
                     */
                    $select_provision_unlogged = clone $select;
                    $select_provision_unlogged->where(function ($q) use ($restaurant) {
                        $q->where('user_res_id', null)
                            ->orWhere('user_res_id', '!=', $restaurant->id);
                    });
                    $value_to_provision_unlogged = $select_provision_unlogged->sum(\DB::raw('price - discount'));
                    $provision_unlogged_value = number_format(($value_to_provision_unlogged * ($restaurant->provision_unlogged / 100)), 2, '.', '');

                    $order_paid = $select->count();

                    $select->where('status', '>', self::STATUS_NEW);
                    $order_confirmed_number = $select->count();

                    $order_ratio_paid = $order_number ? number_format(($order_paid / $order_number), 2, '.', '') : 0;

                    $used_points = $select->sum('points_value');

                    /*$sum = number_format(($provision_value - $used_points), 2, '.', '');*/
                    $sum = number_format(($provision_logged_value + $provision_unlogged_value - $used_points), 2, '.', '');

                    $restaurant_stat = [];
                    $restaurant_stat['name'] = '<a href="javascript:;" onclick="showModalRestaurant('.$restaurant->id.');">'.$restaurant->name.'</a>';
                    $restaurant_stat['order_number'] = $order_number;
                    $restaurant_stat['order_confirmed_number'] = $order_confirmed_number;
                    $restaurant_stat['order_ratio_paid'] = $order_ratio_paid;
                    $restaurant_stat['value'] = $value;
                    $restaurant_stat['provision_logged'] = $restaurant->provision_logged;
                    $restaurant_stat['provision_unlogged'] = $restaurant->provision_unlogged;
                    $restaurant_stat['provision_logged_value'] = $provision_logged_value;
                    $restaurant_stat['provision_unlogged_value'] = $provision_unlogged_value;
                    $restaurant_stat['provision_value'] = number_format(($provision_logged_value + $provision_unlogged_value), 2, '.', '');
                    $restaurant_stat['used_points'] = $used_points;
                    $restaurant_stat['sum'] = $sum;
                    $stats['result'][] = $restaurant_stat;
                }
            }
        }
        $stats['columns'] = [
            __('stats.Restaurant name'),
            __('stats.Orders number'),
            __('stats.Confirmed orders number'),
            __('stats.Paid to all orders ratio'),
            __('stats.Value'),
            __('admin.Provision logged'),
            __('admin.Provision unlogged'),
            __('stats.Provision logged value'),
            __('stats.Provision unlogged value'),
            __('stats.Provision value'),
            __('stats.Used points value'),
            __('stats.Sum'),
        ];

        return collect($stats);
    }

    public function getAvailablePoints()
    {
        $ratio = config('admanager.ratio') ? config('admanager.ratio') : 100;
        $userData = User::where('id', $this->user_id)->get()->first();
        $user = UserSystem::where('id', $this->user_id)->first();
        //$user = UserSystem::where('email', $userData->email)->first();

        //$balance_points = $user->getBalance();
        $balance_points = floor($user->getBalance());
        $balance = $balance_points ? (float) number_format(($balance_points / $ratio), 2, '.', '') : 0.00;
        $price_full = (float) $this->getFullPrice();
        $discount = (float) $this->calculateDiscount();

        return (int) min(($price_full - $discount - 1) * $ratio, $balance * $ratio);
    }

    public function calculateDiscount()
    {
        $total_discount = 0.0;
        foreach ($this->orders as $order) {
            $discounts = Promotion::where('gift_dish_id', $order->dish_id)->get();
            $current_dish = Dish::find($order->dish_id);
            $dish_price = Dish::find($order->dish_id)->price;
            foreach ($discounts as $discount) {
                // type_value 0 - procenty
                // type_value 1 - stała wartość
                if ($discount->type_value == 0) {
                    $total_discount = $total_discount + (($dish_price * $discount->value / 100.0) * $order->quantity);
                } elseif ($discount->type_value == 1) {
                    $total_discount = $total_discount + ($discount->value * $order->quantity);
                }
            }
        }

        return (float) $total_discount;
    }

    /**
     * @param int $points
     *
     * @return bool
     */
    public function spendPoints($points): bool
    {
        $userSystem = UserSystem::find($this->user_id);
        if ($userSystem) {
            $userSystem->reklamy_referring_user->wallet->balance -= $points;
            $userSystem->reklamy_referring_user->wallet->save();

            return true;
        }

        return false;
    }

    public function canBeAccepted()
    {
        return $this->status == self::STATUS_NEW ? true : false;
    }

    public function canBeReady()
    {
        return $this->status == self::STATUS_ACCEPTED ? true : false;
    }

    public function grantCashback()
    {
        $cashback = config('admanager.cashback');
        if ($cashback > 0) {
            $ratio = config('admanager.ratio', 100);

            $user = UserSystem::where('id', $this->user->id)->first();
            $object_value = number_format($ratio * $cashback * (($this->price - $this->discount)), 2, '.', '');
            $referringUserService = app(ReferringUserService::class);

            $referringUser = $referringUserService->getReferringUser($user);
            $referringUserService->modifyBalanceForReferringUser($referringUser, $object_value);
        }
    }

    /**
     * @return bool
     */
    public function refundPoints(): bool
    {
        $referringUserService = app(ReferringUserService::class);
        $referringUser = $referringUserService->getReferringUser($this->user);

        return $referringUserService->modifyBalanceForReferringUser($referringUser, $this->points);
    }

    public static function cronBillTickets()
    {
        $rows = self::where('ticket', 1)->whereDate('updated_at', Carbon::now()->format('Y-m-d'))->get();
        if (count($rows)) {
            $ticketService = new TicketService();
            /*if(!$ticketService->test())
                return false;*/
            foreach ($rows as $row) {
                $row->sendTickets();
            }
        }

        return true;
    }

    public static function cronCancelOrders()
    {
        $tenant = app(\Hyn\Tenancy\Environment::class)->tenant();
        $website = TenancyFacade::website();
        if ($tenant && $website) {
            $restaurant = Restaurant::where('hostname', $website->uuid)->first();

            $bills = self::where('status', self::STATUS_NEW)
                ->where('paid', self::PAID_YES)
                ->where('payment_at', '<', Carbon::now()->subMinutes(115)->format('Y-m-d H:i:s'))
                ->get();
            if (count($bills)) {
                $restaurant_email = Settings::getSetting('kontakt', 'email', true, false);
                foreach ($bills as $bill) {
                    \DB::connection('tenant')->transaction(function () use ($bill, $restaurant, $restaurant_email) {
                        $bill->status = Bill::STATUS_CANCELED;
                        $bill->save();
                        $user = $bill->user;
                        if (isset($user) && $user->email) {
                            \Mail::to($user->email)->send(new OrderCancelClientMail($user, $bill, $restaurant));
                        }

                        $refunded = false;
                        $payments = Payment::where('bill_id', $bill->id)->where('paid', 1)->where('type', 'tpay')->get();
                        if (count($payments)) {
                            foreach ($payments as $payment) {
                                $refund = Refund::create([
                                    'payment_id' => $payment->id,
                                    'bill_id' => $bill->id,
                                    'amount' => $payment->p24_amount / 100,
                                    'status' => Refund::STATUS_TO_REFUNDED,
                                ]);
                                $refunded = true;
                            }
                        }
                        if ($restaurant_email) {
                            \Mail::to($restaurant_email)->send(new OrderCancelClientRestaurant($bill, $restaurant));
                        }
                    });
                }
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isRefund()
    {
        return $this->paid && isset($this->payment) && $this->payment->type == 'tpay' && ! isset($this->refund) /*&& \Carbon\Carbon::now()->subMinutes(115)->format('Y-m-d H:i:s') < $this->payment_at */ ;
    }

    public function getAddressAsTextAttribute()
    {
        return (! $this->address) ? 'Polska, Warszawa' : sprintf(
            'Polska, %s, %s, %s, %s',
            $this->address->city,
            $this->address->postcode,
            $this->address->street,
            $this->address->house_number
        );
    }

    public function getTotalPriceAttribute()
    {
        return $this->discount + $this->price + $this->service_charge + $this->delivery_cost;
    }

    public function getItemsPriceAttribute()
    {
        return $this->price - $this->getPackagePrice();
    }

    public function getTotalPriceToPayAttribute()
    {
        return $this->price_to_display();
    }

    public static function findForPhrase(string $phrase = ''): Collection
    {
        if (! TenancyFacade::website()) {
            return new Collection([]);
        }

        $userIds = UserSystem::query()
            ->where(DB::raw('CONCAT(first_name, " ", last_name)'), 'like', '%'.$phrase.'%')
            ->get()
            ->pluck('id', 'id');

        return self::query()
            ->where('id', 'like', '%'.$phrase.'%')
            ->orWhere('phone', 'like', '%'.$phrase.'%')
            ->orWhere('comment', 'like', '%'.$phrase.'%')
            ->orWhereHas('orders', function ($q) use ($phrase) {
                return $q->whereHas('dish', function ($q) use ($phrase) {
                    return $q->where('name', 'like', '%'.$phrase.'%');
                });
            })
            ->orWhereIn('user_id', $userIds)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function getSearchGroupName(): string
    {
        return __('admin.Bills');
    }

    public function getSearchUrl(): string
    {
        return route('admin.bills.show', ['bill' => $this->id]);
    }

    public function getSearchTitle(): string
    {
        return '#'.$this->id.' | '.$this->created_at->format('Y-m-d H:i');
    }

    public function getSearchDescription(): string
    {
        return sprintf(
            '%s, %s, %s, %s',
            $this->user?->first_name.' '.$this->user?->last_name,
            (new MoneyDecorator())->decorate($this->amount(), 'PLN'),
            $this->phone,
            $this->address_as_text
        );
    }

    public function getSearchPhoto(): string
    {
        $dish = $this->orders->first()->dish->photos->first();

        return ($dish instanceof Dish) ? Croppa::url($dish->getPhoto(false), 90, 50) : '';
    }

    public function getPaymentUrlForUnpaidBill(): ?string
    {
        $paymentRenewalAvailabilityInMinutes = Config::get('payment.payment_renewal_availability_in_minutes', 60);

        return $this->payment()
            ->where('paid', false)
            ->where('created_at', '>=', Carbon::now()->subMinutes($paymentRenewalAvailabilityInMinutes))
            ->latest()->first()?->url;
    }
}
