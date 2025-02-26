<?php

namespace App\Models;

use App\Notifications\Alert;
use App\Notifications\RefundMobile;
use App\Notifications\ReservationMobile;
use App\Notifications\StatusBill;
use App\Notifications\StatusBillMobile;
use App\Notifications\StatusOrder;
use App\Notifications\Waiter;
use App\Services\TicketService;
use Carbon\Carbon;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    use ModelTrait;
    use UsesTenantConnection;

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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'notifiable_id',
        'data',
        'read_at',
        'ticket',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'notifiable_type',
        'updated_at',
        'created_at',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'ticket' => self::TICKET_NA,
    ];

    private static $types = [
        'waiter' => Waiter::class,
        'promotion' => Promotion::class,
        'alert' => Alert::class,
        'status_bill' => StatusBill::class,
        'status_bill_mobile' => StatusBillMobile::class,
        'refund_mobile' => RefundMobile::class,
        'status_order' => StatusOrder::class,
        'reservation' => \App\Notifications\Reservation::class,
        'reservation_mobile' => ReservationMobile::class,
    ];

    protected static $ticketStatusName = [
        self::TICKET_NA => 'Not applicable',
        self::TICKET_TO_SEND => 'To send',
        self::TICKET_SENT => 'Sent',
    ];

    /**
     * @param array $criteria
     * @param array $order
     * @param int   $limit
     * @param int   $offset
     *
     * @return Collection
     */
    public static function getRows(array $criteria, array $order, int $limit, int $offset): Collection
    {
        $query = self::limit($limit)->offset($offset);

        if (! empty($criteria['id'])) {
            $query->whereIn('id', $criteria['id']);
        }
        if (! empty($criteria['table'])) {
            $query->whereIn('notifiable_id', $criteria['table']);
            $query->where('notifiable_type', '=', Table::class);
        }
        if (! empty($criteria['bill'])) {
            $query->whereIn('notifiable_id', $criteria['bill']);
            $query->where('notifiable_type', '=', Bill::class);
        }
        if (! empty($criteria['order'])) {
            $query->whereIn('notifiable_id', $criteria['order']);
            $query->where('notifiable_type', '=', Order::class);
        }
        if (! empty($criteria['user'])) {
            $query->whereIn('notifiable_id', $criteria['user']);
            $query->where('notifiable_type', '=', User::class);
        }
        if (! empty($criteria['type'])) {
            array_walk($criteria['type'], function (&$value, $key) {
                $value = Notification::getClassType($value);
            });
            $query->whereIn('type', $criteria['type']);
        }

        if (! empty($criteria['fromDate'])) {
            $query->whereDate('created_at', '>=', $criteria['fromDate']);
        }

        if (! empty($criteria['toDate'])) {
            $query->whereDate('created_at', '<=', $criteria['toDate']);
        }

        if (null !== ($criteria['isRead'] ?? null)) {
            $query->whereNotNull('read_at');
        }

        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                $query->orderBy(self::decamelize($column), $direction);
            }
        }

        return $query->get();
    }

    /**
     * @return array
     */
    public static function getType(string $classType): string
    {
        throw_if(! self::hasClassTypes($classType), new \Exception('Wrong class type of notification!', 500));

        return array_combine(array_values(self::$types), array_keys(self::$types))[$classType];
    }

    /**
     * @param string $type
     *
     * @return string
     *
     * @throws \Exception
     */
    public static function getClassType(string $type): string
    {
        throw_if(! self::hasTypes($type), new \Exception('Wrong type of notification!', 500));

        return self::$types[strtolower($type)];
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public static function hasTypes(string $type): bool
    {
        return array_key_exists($type, self::$types);
    }

    /**
     * @param string $classType
     *
     * @return bool
     */
    public static function hasClassTypes(string $classType): bool
    {
        return in_array($classType, self::$types);
    }

    public function sendTickets()
    {
        $ticketService = new TicketService();
        /*
        if(!$ticketService->test())
            return false;*/
        $ticketService->method('/api/DaneRachunku/PrintWezwijKelnera');
        $settings = Settings::where('key', 'drukarka')->first();
        if ($settings && isset($settings->value['pl']['nazwa_drukarki']) && $settings->value['pl']['nazwa_drukarki']) {
            $printer_name = $settings->value['pl']['nazwa_drukarki'];
        } else {
            $printer_name = 'Brak drukarki';
        }
        $logo_file = base64_encode(file_get_contents(public_path('/images/logo_w.png')));

        if (isset($this->data['description'])) {
            $description = $this->data['description'];
        } else {
            $description = '';
        }

        $data = [
                'wezwijKelnera' => [
                        'logo' => $logo_file,
                        'czas' => Carbon::parse($this->created_at)->format('H:i:s'),
                        'informacja' => $description,
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
            $this->update(['ticket'=>2]);

            return true;
        }

        return false;
    }

    public static function cronNotificationTickets()
    {
        $rows = self::where('ticket', 1)->get();
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

    public function getTitleAttribute()
    {
        $url = explode('\\', $this->notifiable_type);

        return end($url);
    }

    public function getDescriptionAttribute()
    {
        $description = $this->data['description'];

        return $description;
    }
}
