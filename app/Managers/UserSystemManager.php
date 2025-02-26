<?php

namespace App\Managers;

use App\Events\User\UserUpdated;
use App\Exceptions\ApiExceptions\Auth\EmailAlreadyExistsException;
use App\Exceptions\ApiExceptions\Auth\PhoneNumberAlreadyExistsException;
use App\Facades\ReferringUserService;
use App\Http\Controllers\ParametersTrait;
use App\Http\Requests\Api\PhoneRequest;
use App\Http\Requests\Api\VoucherRequest;
use App\Models\AddressSystem;
use App\Models\Friend;
use App\Models\PlayerId;
use App\Models\ReklamyReflink;
use App\Models\Review;
use App\Models\User;
use App\Models\UserAddressSystem;
use App\Models\UserSystem;
use App\Services\SerwerSMSService;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserSystemManager
{
    use ParametersTrait;

    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @param TranslationService $service
     */
    public function __construct(TranslationService $service)
    {
        $this->transService = $service;
    }

    /**
     * @param Request $request
     *
     * @return UserSystem
     */
    public function create(Request $request): UserSystem
    {
        $params = $this->getParams($request, ['first_name', 'login', 'last_name', 'email', 'password', 'roles', 'blocked' => 0, 'isRoom' => 0, 'referred_user_id', 'phone']);
        $params['login'] = $params['login'] ?? $params['email'];
        $references = $this->getParams($request, ['playerIds', 'table']);
        $addresses = (array) $request->addresses;
        if (! empty($params['roles']) && false !== ($ix = array_search(UserSystem::ROLE_ROOM, $params['roles']))) {
            unset($params['roles'][$ix]);
        }
        if (! empty($params['roles']) && false !== ($ix = array_search(UserSystem::ROLE_ADMIN, $params['roles']))) {
            unset($params['roles'][$ix]);
        }
        if (! empty($params['roles']) && false !== ($ix = array_search(UserSystem::ROLE_MANAGER, $params['roles']))) {
            unset($params['roles'][$ix]);
        }
        if (! empty($params['isRoom'])) {
            $params['roles'][] = UserSystem::ROLE_ROOM;
        }
        if (! empty($params['phone'])) {
            $params['phone'] = str_replace(' ', '', $params['phone']);
            if (preg_match('/^\s*$/', $params['phone'])) {
                $params['phone'] = null;
            }
        }
        $user = DB::connection('tenant')->transaction(function () use ($params, $references, $addresses) {
            ! isset($params['password']) ?: $params['password'] = bcrypt($params['password']);
            //$params['auth_code'] = str_random(20);
            $params['auth_code'] = mt_rand(100000, 999999);

            if (! empty($params['phone']) && UserSystem::where('phone', $params['phone'])->exists()) {
                throw new PhoneNumberAlreadyExistsException();
            }

            if (! empty($params['email']) && UserSystem::where('email', $params['email'])->exists()) {
                throw new EmailAlreadyExistsException();
            }

            $user = UserSystem::create($params)->fresh();
            if ($user->hasRoles([UserSystem::ROLE_TABLE])) {
                $user->table()->create(['name' => empty($references['table']['name']) ? trim($user->first_name.' '.$user->last_name) : $references['table']['name']]);
            }
            if (count($addresses)) {
                foreach ($addresses as $address) {
                    $address = array_diff_key($address, ['id', 'company_name', 'nip', 'name', 'surname', 'city', 'postcode', 'street', 'building_number', 'house_number', 'floor', 'phone']);
                    $address_row = AddressSystem::updateOrCreate(['id' => $address['id'] ?? null], $address);
                    UserAddressSystem::updateOrCreate(['address_id' => $address_row->id, 'user_id' => $user->id]);
                }
                $user->fresh();
            }

            $hasError = false;

            try {
                $serwer_sms = new SerwerSMSService();
                $serwer_sms->sendAuth($user->phone, $user->auth_code, $user->id);
            } catch (\Throwable $e) {
                $hasError = true;
            }
            // \Mail::to($user->email)->send(new \App\Mail\VerifyMail($user));

            if (! $hasError) {
                if (empty($references['playerIds'])) {
                    return $user;
                }
                $references['playerIds'] = array_unique($references['playerIds']);
                foreach ($references['playerIds'] as $playerId) {
                    $player = PlayerId::findPlayerIdForUser($playerId, $user);
                    $player ?: $player = PlayerId::create(['player_id' => $playerId, 'user_id' => $user->id]);
                    $player->save();
                }
            }

            return $user;
        });

        return $user;
    }

    /**
     * @param Request $request
     * @param UserSystem $user
     * @param bool $isAdmin
     *
     * @return UserSystem
     */
    public function update(Request $request, UserSystem $user, bool $isAdmin = false): UserSystem
    {
        $params = $this->getParams($request, ['first_name', 'last_name', 'password', 'phone', 'birth_date' => null]);
        $references = $this->getParams($request, ['playerIds', 'table']);
        $addresses = (array) $request->addresses;

        if (! empty($params['phone'])) {
            $params['phone'] = str_replace(' ', '', $params['phone']);
            if (preg_match('/^\s*$/', $params['phone'])) {
                $params['phone'] = null;
            }
        }
        if($user->birth_date != null) {
            $params['birth_date'] = $user->birth_date;
        }

        DB::connection('tenant')->transaction(function () use ($params, $references, $user, $isAdmin, $addresses) {
            if (! empty($params)) {
                ! isset($params['password']) ?: $params['password'] = bcrypt($params['password']);

                $user->update($params);
                $user->fresh();
            }
            if (! $user->hasRoles([UserSystem::ROLE_TABLE])) {
                $user->table()->delete();
            } else {
                if (! $user->table) {
                    $user->table()->create(['name' => empty($references['table']['name']) ? trim($user->first_name.' '.$user->last_name) : $references['table']['name']]);
                    $user->fresh();
                } else {
                    $user->table->name = empty($references['table']['name']) ? $user->table()->name : $references['table']['name'];
                    $user->table->save();
                }
            }
            if (count($addresses)) {
                foreach ($addresses as $address) {
                    $address = array_diff_key($address, ['id', 'company_name', 'nip', 'name', 'surname', 'city', 'postcode', 'street', 'building_number', 'house_number', 'floor', 'phone']);

                    if ($address['id'] && $user->isEndUserRole()) {
                        $address_exists = AddressSystem::where('id', $address['id'])->first();
                        $address_user = AddressSystem::whereHas('user_addresses', function ($q) use ($user) {
                            $q->where('user_id', $user->id);
                        })->where('id', $address['id'])->first();
                        throw_if($address_exists && ! $address_user, new AccessDeniedHttpException(gtrans('admin.Action prohibited')));
                    }

                    $address_row = AddressSystem::updateOrCreate(['id' => $address['id'] ?? null], $address);
                    UserAddressSystem::updateOrCreate(['address_id' => $address_row->id, 'user_id' => $user->id]);
                }
                $user->fresh();
            }

            if (empty($references['playerIds'])) {
                if ($isAdmin) {
                    $user->fresh()->playerIds()->delete();
                }

                return;
            }

            $ids = [];
            $references['playerIds'] = array_unique($references['playerIds']);
            foreach ($references['playerIds'] as $playerId) {
                $player = PlayerId::findPlayerIdForUser($playerId, $user);
                $player ?: $player = PlayerId::create(['player_id' => $playerId, 'user_id' => $user->id]);
                $ids[] = $player->id;
            }
            $user->fresh()->playerIds()->whereKeyNot($ids)->delete();
        });


        try {
            dispatch(new UserUpdated($user->id, [
                'message' => 'Your profile has been updated.',
                'user' => $user
            ]));
        } catch (\Throwable $e) {}

        return $user;
    }

    /**
     * @param Request $request
     * @param UserSystem $user
     * @param bool $isAdmin
     *
     * @return UserSystem
     */
    public function update_profile(Request $request, UserSystem $user, bool $isAdmin = false): UserSystem
    {
        $params = $this->getParams($request, ['first_name', 'login', 'last_name', 'email', 'password']);
        $address = (array) $request->address;

        DB::transaction(function () use ($params, $user, $isAdmin) {
            if (! empty($params)) {
                ! isset($params['password']) ?: $params['password'] = bcrypt($params['password']);

                $user->update($params);
                $user->fresh();
            }
        });

        return $user;
    }

    /**
     * @param UserSystem $user
     *
     * @return UserSystem
     */
    public function delete(UserSystem $user): UserSystem
    {
        DB::transaction(function () use ($user) {
            $user->table()->delete();

            Friend::where('receiver_id', $user->id)
                ->orWhere('sender_id', $user->id)
                ->delete();

            Review::where('user_id', $user->id)->delete();

            $this->anonymizeUser($user);
        });

        return $user;
    }

    public function voucherRedeem(VoucherRequest $request): void
    {
        $referringUser = ReferringUserService::getReferringUser(auth()->user());
        ReferringUserService::redeemVoucherForReferringUser(auth()->user(), $referringUser, $request->get(VoucherRequest::VOUCHER_PARAM_KEY));
    }

    public function setPhone(PhoneRequest $request, UserSystem $userSystem)
    {
        $phone = $request->get('phone', null);

        $userSystem->phone = $phone;

        return $userSystem->save();
    }

    /**
     * @param Request $request
     * @param User $user
     *
     * @return bool
     */
    public function register_auth($request, $user): bool
    {
        if ($user->activated || $user->auth_code != $request->get('auth_code')) {
            return false;
        }

        DB::transaction(function () use ($user) {
            $user->update(['activated' => 1, 'auth_code' => null]);
            $referringUser = ReferringUserService::getReferringUser($user);

            if (! ReklamyReflink::isRegistered($referringUser->id)) {
                ReferringUserService::grantSignUpBonus($user, $referringUser);
            }
        });

        return true;
    }

    public function verifyAuthCode(Request $request)
    {
        $code = $request->get('auth_code', null);
        if (! $code || auth()->user()->auth_code != $code) {
            return false;
        }
        $user = auth()->user();
        $user->update(['activated' => 1, 'auth_code' => null]);

        return true;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function register_auth_again($user): bool
    {
        if ($user && ! $user->activated) {
            $user = DB::transaction(function () use ($user) {
                $params['auth_code'] = mt_rand(100000, 999999);
                $user->update($params);

                $serwer_sms = new SerwerSMSService();
                $serwer_sms->sendAuth($user->phone, $user->auth_code, $user->id);

                return $user;
            });

            return true;
        }

        return false;
    }

    public function anonymizeUser(UserSystem $user): void
    {
        $randomHash = substr(md5('DELETED'.uniqid().'WK'.microtime()), 0, 16);

        $user->updateQuietly([
            'first_name' => 'deleted_user',
            'last_name' => null,
            'phone' => null,
            'login' => $randomHash,
            'email' => $randomHash,
            'external_auth_id' => null,
            'activated' => 0,
        ]);
    }
}
