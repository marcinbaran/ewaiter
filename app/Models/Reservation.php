<?php

namespace App\Models;

use App\Mail\ReservationMail;
use App\Services\GlobalSearch\Searchable;
use App\Services\TicketService;
use Carbon\Carbon;
use Hyn\Tenancy\Facades\TenancyFacade;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Reservation extends Model implements Searchable
{
    use ModelTrait, Notifiable;
    use UsesTenantConnection;

    /**
     * @var bool
     */
    public const KID_NO = false;

    /**
     * @var bool
     */
    public const KID_YES = true;

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
        'table_id',
        'user_id',
        'start',
        'end',
        'kid',
        'active',
        'people_number',
        'closed',
        'name',
        'phone',
        'description',
        'ticket',
        'status',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'kid' => self::KID_NO,
        'active' => 0,
        'closed' => 0,
        'people_number' => 0,
        'ticket' => self::TICKET_NA,
    ];

    protected static $kidsName = [
        self::KID_NO => 'no',
        self::KID_YES => 'yes',
    ];

    protected static $activeName = [
        0 => 'no',
        1 => 'yes',
    ];

    protected static $ticketStatusName = [
        self::TICKET_NA => 'Not applicable',
        self::TICKET_TO_SEND => 'To send',
        self::TICKET_SENT => 'Sent',
    ];

    /**
     * @return BelongsTo
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class, 'table_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserSystem::class, 'user_id', 'id');
    }

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
        //        if (!empty($criteria['bill'])) {
        //            $query->whereIn('bill_id', $criteria['bill']);
        //        }

        if (! empty($criteria['fromDate'])) {
            $query->whereDate('start', '>=', $criteria['fromDate']);
        }

        if (! empty($criteria['toDate'])) {
            $query->whereDate('start', '<=', $criteria['toDate']);
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
     * @param int   $limit
     * @param int   $offset
     *
     * @return Collection
     */
    public static function getRestaurantsReservations(array $criteria, array $order, int $limit, int $offset): Collection
    {
        $restaurants = ! empty($criteria['restaurant_id']) ? Restaurant::whereIn('id', $criteria['restaurant_id'])->get() : Restaurant::get();
        $collection_reservations = new \Illuminate\Database\Eloquent\Collection;

        if ($restaurants) {
            foreach ($restaurants as $key => $restaurant) {
                config(['database.connections.tenant.database' => $restaurant->hostname]);
                \DB::reconnect('tenant');

                $query = self::select(\DB::raw('*,"'.$restaurant->name.'" AS restaurant_name,"'.$restaurant->id.'" AS restaurant_id'))->with('table');

                if (! empty($criteria['user'])) {
                    $query->whereIn('user_id', $criteria['user']);
                }

                if (! empty($criteria['fromDate'])) {
                    $query->where('start', '>=', $criteria['fromDate']);
                }

                if (! empty($criteria['toDate'])) {
                    $query->where('start', '<=', $criteria['toDate']);
                }

                $reservations = $query->get();
                foreach ($reservations as $reservation) {
                    $reservation->restaurant = $key;
                }

                $collection_reservations = $collection_reservations->toBase()->merge($reservations);
            }

            if (! empty($order)) {
                foreach ($order as $column => $direction) {
                    $collection_reservations = $collection_reservations->sortBy(
                        function ($reservation) use ($column) {
                            return $reservation->{self::decamelize($column)};
                        },
                        SORT_REGULAR,
                        strtolower($direction) === 'desc'
                    );
                }
            }

            if (! $criteria['noLimit']) {
                $collection_reservations = $collection_reservations->slice($offset, $limit);
            }
        }

        return $collection_reservations->values();
    }


    /**
     * @param string|null $filter
     * @param int         $paginateSize
     * @param bool        $onlyGroup
     * @param array       $order
     *
     * @return LengthAwarePaginator
     */
    public static function getPaginatedForPanel(string $filter = null, int $paginateSize, array $order = null, $filter_columns = null): LengthAwarePaginator
    {
        $query = self::distinct();

        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                $query->orderBy(self::decamelize($column), $direction);
            }
        }

        if (! empty($filter_columns)) {
            foreach ($filter_columns as $filter_column => $value) {
                if ($value !== null) {
                    $query->where($filter_column, $value);
                }
            }
        }

        if (! empty($filter)) {
            $query->where('id', 'LIKE', '%'.$filter.'%');
            $query->orWhere('start', 'LIKE', '%'.$filter.'%');
            $query->orWhere('phone', 'LIKE', '%'.$filter.'%');
            $query->orWhere('name', 'LIKE', '%'.$filter.'%');
            $query->orWhere('description', 'LIKE', '%'.$filter.'%');
            $query->orWhere('people_number', 'LIKE', '%'.$filter.'%');
        }

        return $query->paginate($paginateSize, ['reservations.*']);
    }

    public function getKidsName()
    {
        return self::$kidsName[$this->kid];
    }

    public function getActiveName()
    {
        return self::$activeName[$this->active];
    }

    public function getClosedName()
    {
        return self::$activeName[$this->closed];
    }

    public static function getFree(array $criteria, int $limit, int $offset)
    {
        $tables = Table::get()->pluck('id');

        $reserved_tables = self::whereDate('start', $criteria['date'])->where('active', 1)->where('closed', false)->get()->pluck('table_id');

        $query = Table::select();

        $query->where('active', 1);
        $query->whereNotIn('id', $reserved_tables);

        if (! $criteria['noLimit']) {
            $query->offset($offset)->limit($limit);
        }

        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                $query->orderBy(self::decamelize($column), $direction);
            }
        }

        return $query->get();
    }

    public function sendReservationNotifications()
    {
        $user = UserSystem::where('id', $this->user_id)->first();
        $validator = \Validator::make(['email' => $user->email], [
            'email' => 'required|email',
        ]);
        if ($validator->passes()) {
//            \Mail::to($user->email)->send(new ReservationMail($user, $this));
        }
    }

    public function sendTickets()
    {
        $ticketService = new TicketService();

        /*if(!$ticketService->test())
            return false;*/
        $ticketService->method('/api/DaneRachunku/PrintSystemRezerwacji');

        $printer_name = Settings::getSetting('drukarka', 'nazwa_drukarki', true, false);
        if (! $printer_name) {
            $printer_name = 'Brak drukarki';
        }

        $logo_file = base64_encode(file_get_contents(public_path('/images/logo_w.png')));

        if (isset($this->table()->first()->number)) {
            $table_number = $this->table()->first()->number;
        } else {
            $table_number = '';
        }

        $data = [
            'systemRezerwacji' => [
                'logo' => $logo_file,
                'nrStolika' => $table_number,
                'czas' => Carbon::parse($this->start)->format('H:i:s'),
                'rezerwacjaData' => Carbon::parse($this->start)->format('Y-m-d H:i:s'),
                'iloscOsob' => $this->people_number,
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

    public function sendNotificationsEmail(Restaurant $restaurant)
    {
        try {
            $table = Table::find($this->table_id);
            $user = UserSystem::find($this->user_id);
        }
        catch (\Exception $e) { Log::error($e->getMessage()); }

        $restaurant_email = Settings::getSetting('kontakt', 'email', true, false)??null;
        $restaurant_phone = Settings::getSetting('kontakt', 'telefon', true, false)??null;


        $reservationDetails = [
            'restaurant' => $restaurant->name,
            'date' => Carbon::parse($this->start)->format('d-m-Y'),
            'time' => Carbon::parse($this->start)->format('H:i'),
            'phoneNumber' =>  $restaurant_phone,
            'email' => $restaurant_email,
            'tableName' => $table->name??'',
            "name"=>$this->name,
        ];
        $dataToSend = [
            'To' => $user->email ?? '',
            'From'=>$restaurant->manager_email,
            'name' => __('emails.name'),
            "message"=>__("emails.reservation.new_reservation",$reservationDetails),
            "title"=>__('emails.reservation.title'),
            "subject"=>__('emails.reservation.created_subject',$reservationDetails),
            'greeting' => __('emails.greeting',$reservationDetails),
        ];

        try {
            Mail::mailer('reservation_smtp')
                ->to($restaurant->manager_email)
                ->send(new ReservationMail($dataToSend));

            $dataToSend["message"]=__("emails.reservation.created",$reservationDetails);


            Mail::mailer('reservation_smtp')
                ->to($user->email??'kontakt@e-waiter.pl')
                ->send(new ReservationMail($dataToSend));



        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public static function cronReservationTickets()
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

    public static function findForPhrase(string $phrase = ''): \Illuminate\Database\Eloquent\Collection
    {
        if (! TenancyFacade::website()) {
            return new \Illuminate\Database\Eloquent\Collection([]);
        }

        $userIds = UserSystem::query()
            ->where(DB::raw('CONCAT(first_name, " ", last_name)'), 'like', '%'.$phrase.'%')
            ->get()
            ->pluck('id', 'id');

        return self::query()
            ->where('phone', 'like', '%'.$phrase.'%')
            ->orWhere('name', 'like', '%'.$phrase.'%')
            ->orWhere('description', 'like', '%'.$phrase.'%')
            ->orWhereIn('user_id', $userIds)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function getSearchGroupName(): string
    {
        return __('admin.Reservations');
    }

    public function getSearchUrl(): string
    {
        return route('admin.reservations.show', ['reservation' => $this->id]);
    }

    public function getSearchTitle(): string
    {
        return $this->room_number ? __('admin.Room').': '.$this->room_number : __('admin.Table').': '.$this->table;
    }

    public function getSearchDescription(): string
    {
        return sprintf(
            '%s, %s, %s',
            $this->user?->first_name.' '.$this->user?->last_name,
            $this->phone,
            $this->address_as_text
        );
    }

    public function getSearchPhoto(): string
    {
        return '';
    }
}
