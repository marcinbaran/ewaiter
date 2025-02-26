<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\PhoneNotification;
use App\Models\Settings;
use Illuminate\Support\Facades\Log;

class SerwerSMSService
{
    protected $phone;

    protected $test_mode;

    protected $active;

    protected $api_url;

    protected $username;

    protected $password;

    protected $sender_number;

    protected $text;

    protected $notify_after_minutes;

    protected $notify_after_minutes_next_phone;

    protected $notify_after_minutes_break;

    protected $time_passed;

    protected $time_passed_24;

    protected $time_passed_next_phone;

    protected $time_passed_break;

    protected $counter;

    protected $client;

    protected $object_id;

    protected $object;

    public function __construct()
    {
        $this->phone = Settings::getSetting('kontakt', 'telefon', true, false);
        $this->test_mode = config('serwersms.test_mode');
        $this->active = config('serwersms.active');
        $this->api_url = config('serwersms.url');
        $this->username = config('serwersms.username');
        $this->password = config('serwersms.password');
        $this->sender_number = config('serwersms.sender_number');
        $this->text = config('serwersms.text');

        $this->notify_after_minutes = config('serwersms.notify_after_minutes') ? config('serwersms.notify_after_minutes') : 8;
        $this->notify_after_minutes_next_phone = config('serwersms.notify_after_minutes_next_phone') ? config('serwersms.notify_after_minutes_next_phone') : 5;
        $this->notify_after_minutes_break = config('serwersms.notify_after_minutes_break') ? config('serwersms.notify_after_minutes_break') : 10;

        $this->time_passed = \Carbon\Carbon::now()->subMinutes($this->notify_after_minutes)->format('Y-m-d H:i:s');
        $this->time_passed_24 = \Carbon\Carbon::now()->subDays(1)->format('Y-m-d H:i:s');
        $this->time_passed_next_phone = \Carbon\Carbon::now()->subMinutes($this->notify_after_minutes_next_phone)->format('Y-m-d H:i:s');
        $this->time_passed_break = \Carbon\Carbon::now()->subMinutes($this->notify_after_minutes_break)->format('Y-m-d H:i:s');

        $this->client = new \GuzzleHttp\Client();

        $this->object = 'bills';

        $this->counter = 0;
    }

    public function sendResetPasswordCodeSMS(string $phone, string $code)
    {
        $this->text = $code;
        $this->phone = $phone;

        return $this->makeSMS();
    }

    public function sendAuth($phone, $token, $object_id)
    {
        $this->object = 'users';
        $this->object_id = $object_id;
        $this->phone = $phone;
        $this->text = 'Token do potwierdzenia: '.$token;
        $this->makeSMS();
    }

    public function cronCall()
    {
        $check_message = $this->check();
        if ($check_message != 'checked') {
            return $check_message;
        }

        return $this->test_mode ? $this->makeTestCall() : $this->makeCall();
    }

    public function check()
    {
        if (! $this->phone) {
            return 'Message: no phone';
        }
        if (! $this->active) {
            return 'Message: not active';
        }

        $there_was_phone = PhoneNotification::
            where('active', true)
            ->where('send_at', '>', $this->time_passed_next_phone)
            ->where('object', 'bills')
            ->orderBy('id', 'desc')
            ->first();
        if ($there_was_phone) {
            return 'Message: waiting after phone';
        }

        $there_was_action = PhoneNotification::
            where('active', false)
            ->where('object', 'bills')
            ->whereNotNull('send_at')
            ->where('updated_at', '>', $this->time_passed_break)
            ->orderBy('id', 'desc')
            ->first();
        if ($there_was_action) {
            return 'Message: waiting after action';
        }

        $last_phone_notification = PhoneNotification::
            where('active', true)
            ->where('object', 'bills')
            ->orderBy('id', 'desc')
            ->first();

        if ($last_phone_notification && $last_phone_notification->counter >= 3) {
            return 'Message: counter';
        } else {
            $this->counter = $last_phone_notification ? $last_phone_notification->counter : 0;
        }

        $bills = Bill::where('status', 0)
            ->where('cart', 0)
            //->where('paid',1)
            ->where('updated_at', '<', $this->time_passed)
            ->where('updated_at', '>', $this->time_passed_24)
            ->orderBy('id', 'desc')
            ->first();
        if (! $bills) {
            PhoneNotification::where('active', true)->where('object', 'bills')
                ->update([
                    'active' => false,
                ]);

            return 'Message: no bills';
        }
        $this->object_id = $bills->id;

        return 'checked';
    }

    public function makeCall()
    {
        $response = $this->client->request('POST', $this->api_url.'/messages/send_voice', [
            'body' => json_encode([
                'username' => $this->username,
                'password' => $this->password,
                'phone' => $this->phone,
                'text' => $this->text,
                'sender_number' => $this->sender_number,
            ]),
        ]);
        Log::info('Make call response: '.$response->getBody());
        $body = json_decode((string) $response->getBody(), true);
        if ($response->getStatusCode() == 200 && ! empty($body['success']) && ! empty($body['queued']) && $body['queued']) {
            $this->pushSuccess($body);
        } else {
            $this->pushFailure($body);
        }

        return 'success';
    }

    public function makeSMS()
    {
        $response = $this->client->request('POST', $this->api_url.'/messages/send_sms', [
            'body' => json_encode([
                'username' => $this->username,
                'password' => $this->password,
                'phone' => $this->phone,
                'text' => $this->text,
                'sender' => 'E-Waiter',
                //"sender_number"=> $this->sender_number
            ]),
        ]);
        $body = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() == 200 && ! empty($body['success']) && ! empty($body['queued']) && $body['queued']) {
            $this->pushSuccess($body);
        } else {
            $this->pushFailure($body);
        }

        return 'success';
    }

    public function makeTestCall()
    {
        $body = 'tests';
        $this->pushSuccess($body);

        return 'success';
    }

    public function makeTestSMS()
    {
        $body = 'tests';
        $this->pushSuccess($body);

        return 'success';
    }

    public function pushSuccess($body)
    {
        PhoneNotification::create([
            'counter' => $this->counter + 1,
            'phone' => $this->phone,
            'sender_number' => $this->sender_number,
            'content' => $this->text,
            'object_id' => $this->object_id,
            'object' => $this->object,
            'response' => json_encode($body),
            'send_at' => date('Y-m-d H:i:s'),
            'active' => true,
        ]);
    }

    public function pushFailure($body)
    {
        PhoneNotification::create([
            'counter' => $this->counter,
            'phone' => $this->phone,
            'sender_number' => $this->sender_number,
            'content' => $this->text,
            'object_id' => $this->object_id,
            'object' => $this->object,
            'response' => json_encode($body),
            'send_at' => null,
            'active' => false,
        ]);
    }

    public function setTargetPhoneNumber($phone)
    {
        $this->phone = $phone;
    }

    public function setText($text)
    {
        $this->text = $text;
    }
}
