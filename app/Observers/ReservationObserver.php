<?php

namespace App\Observers;

use App\Enum\ReservationStatus;
use App\Events\ChangeLogs;
use App\Events\ReservationMobileEvent;
use App\Mail\ContactFormMail;
use App\Mail\ReservationMail;
use App\Models\Address;
use App\Models\FireBaseNotificationV2;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\Settings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use function Laravel\Prompts\search;

class ReservationObserver
{
    /**
     * @param Reservation $model
     */
    public function creating(Reservation $model)
    {
    }

    /**
     * @param Reservation $model
     */
    public function created(Reservation $model)
    {
        try {
            $restaurant = Restaurant::getCurrentRestaurant();

            $model->sendNotificationsEmail($restaurant);
        }catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        event(new ChangeLogs($model, 'created'));
        //$model->sendTickets();
        // $model->sendNotificationEmail();
    }

    /**
     * @param Reservation $model
     */
    public function updating(Reservation $model)
    {
    }

    /**
     * @param Reservation $model
     */
    public function updated(Reservation $model)
    {
        event(new ChangeLogs($model, 'updated'));

        $reservationStatus = ReservationStatus::tryFrom($model->status);

        if ($model->isDirty('active') || ($model->active == 0 && $model->isDirty('closed'))) {
            event(new ReservationMobileEvent($model->fresh()));
        }

        if ($model->isDirty('status') && $reservationStatus !== ReservationStatus::PENDING) {
            $reservationDetails = [
                'restaurant' => Restaurant::getCurrentRestaurant()->name,
                'date' => Carbon::parse($model->start)->format('d-m-Y'),
                'time' => Carbon::parse($model->start)->format('H:i'),
                'phoneNumber' => isset(Restaurant::getCurrentRestaurant()->address->phone) ? Restaurant::getCurrentRestaurant()->address->phone : "",
                'email' => Restaurant::getCurrentRestaurant()->manager_email,
                'tableName' => isset($model->table) ? $model->table->name : "",
            ];

            $dataToSend = [
                'To' => $model->user->email,
                'name' => __('emails.name'),
                'greeting' => __('emails.greeting',$reservationDetails),

            ];


            $restaurant_email= ['email' => Settings::getSetting('kontakt', 'email', true, false)??null];
            $restaurant_phone = ['phoneNumber'  => Settings::getSetting('kontakt', 'telefon', true, false)??null];

            if ($reservationStatus === ReservationStatus::CONFIRMED) {
                $notificationContent = __('firebase.Reservation has been accepted', $reservationDetails);
                $dataToSend +=[
                  "message"=>__("emails.reservation.accepted",$reservationDetails).__("emails.reservation.restaurant_contact",$reservationDetails),
                    "title"=>__('emails.reservation.title'),
                    "subject"=>__('emails.reservation.accepted_subject',$reservationDetails),
                ];
                if (!is_null($restaurant_email)) {
                    $dataToSend["message"] .= __("emails.reservation.restaurant_email", $restaurant_email);
                }

                if (!is_null($restaurant_phone)) {
                    $dataToSend["message"] .= __("emails.reservation.restaurant_phone",  $restaurant_phone);
                }
                $dataToSend["message"].=__("emails.reservation.reservation_last_part",$reservationDetails);
            }

            if ($reservationStatus === ReservationStatus::CANCELLED) {
                $notificationContent = __('firebase.Reservation has been cancelled', $reservationDetails);
                $dataToSend +=[
                    "message"=>__("emails.reservation.canceled",$reservationDetails),
                    "title"=>__('emails.reservation.title'),
                    "subject"=>__('emails.reservation.canceled_subject',$reservationDetails),
                ];

            }

//            NotificationService::sendPushToUser($model->user_id, $notificationContent, 'reservations/'.$model->id, $model->id, NotificationTitle::RESERVATION);
            FireBaseNotificationV2::create([
                'user_id' => $model->user_id,
                'title' => __('firebase.E-waiter'),
                'body' => $notificationContent,
                'data' => json_encode([
                    'title' => __('firebase.E-waiter'),
                    'body' => $notificationContent,
                    'url' => '/account/reservations_history/' . $model->id,
                    'object_id' => $model->id,
                    'reservation' => $model,
                    'hostname' => Restaurant::getCurrentRestaurant() ? Restaurant::getCurrentRestaurant()->hostname : null,
                ]),
            ]);

            try {
                Mail::mailer('reservation_smtp')
                    ->to($model->user->email)
                    ->send(new ReservationMail($dataToSend));
            }
            catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }
    }

    /**
     * @param Reservation $model
     */
    public function saving(Reservation $model)
    {
    }

    /**
     * @param Reservation $model
     */
    public function saved(Reservation $model)
    {
        if ($model->isDirty('active') && $model->active && ! $model->closed) {
            $model->sendReservationNotifications();
        }
    }

    /**
     * @param Reservation $model
     */
    public function deleting(Reservation $model)
    {
    }

    /**
     * @param Reservation $model
     */
    public function deleted(Reservation $model)
    {
        event(new ChangeLogs($model, 'deleted'));
    }

    /**
     * @param Reservation $model
     */
    public function restoring(Reservation $model)
    {
    }

    /**
     * @param Reservation $model
     */
    public function restored(Reservation $model)
    {
    }

    /**
     * @param Reservation $model
     */
    public function retrieved(Reservation $model)
    {
    }
}
