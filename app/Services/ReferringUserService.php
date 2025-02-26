<?php

namespace App\Services;

use App\Exceptions\ApiExceptions\Voucher\InvalidVoucherCodeException;
use App\Exceptions\ApiExceptions\Voucher\VoucherAlreadyUsedException;
use App\Helpers\PointsHelper;
use App\Models\ReklamyIncome;
use App\Models\ReklamyReferringUser;
use App\Models\ReklamyReflink;
use App\Models\ReklamyWallet;
use App\Models\Settings;
use App\Models\User;
use App\Models\UserSystem;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReferringUserService
{
    public function getReferringUser(User|UserSystem $user): ReklamyReferringUser
    {
        $referring = ReklamyReferringUser::where('email', $user->email)->first();

        if (! $referring) {
            $referring = new ReklamyReferringUser();
            $referring->email = $user->email;
            $referring->save();
        }

        $wallet = $referring->wallet;
        if (! $wallet) {
            $wallet = new ReklamyWallet();
            $wallet->referring_user_id = $referring->id;
            $wallet->balance = 0;
            $wallet->save();
        }

        return $referring->fresh(['wallet']);
    }

    public function redeemVoucherForReferringUser(UserSystem|User $user, ReklamyReferringUser $referringUser, string $voucherCode): void
    {
        $voucher = Voucher::where('code', $voucherCode)->first();

        if ($voucher === null) {
            throw new InvalidVoucherCodeException();
        }

        if ($voucher->is_used) {
            throw new VoucherAlreadyUsedException();
        }

        $this->settleVoucher($voucher, $user->id);

//        FirebaseServiceV2::saveNotification(
//            $user->id,
//            __('firebase.voucher_points_added', ['points' => $voucher->value * 100]),
//            '/account/points_screen',
//            $voucher->id
//        );

        DB::connection('reklamy')->transaction(function () use ($user, $referringUser, $voucher) {
            $voucherPointsAmount = $voucher->value * PointsHelper::getPointsRatio();

            $reflink = $this->createReflink($referringUser->id, $user->id, 'voucher', $voucher->voucher);
            $this->upsertIncome($reflink, $voucherPointsAmount);
            $user->modify_points($voucherPointsAmount);
        });
    }

    public function grantSignUpBonus(UserSystem|User $user, ReklamyReferringUser $referringUser)
    {
        $pointsAmount = Settings::getSetting('rejestracja_uzytkownika', 'punkty_reklamowe', true);

        if ($pointsAmount) {
            DB::connection('reklamy')->transaction(function () use ($user, $referringUser, $pointsAmount) {
                $pointsValue = $pointsAmount * PointsHelper::getPointsRatio();

                $reflink = $this->createReflink($referringUser->id, $user->id, 'registration', null, $pointsValue);
                $this->upsertIncome($reflink, $pointsAmount);
                $user->modify_points($pointsAmount);
            });
        }
    }

    public function modifyBalanceForReferringUser(ReklamyReferringUser $user, int $amount): bool
    {
        $user->wallet->balance += $amount;
        if ($user->wallet->balance < 0) {
            $user->wallet->balance = 0;
        }

        return (bool) $user->wallet->save();
    }

    private function settleVoucher(Voucher $voucher, int $referringUserId): void // TODO: better name, it may not mark as used when voucher is multiple use
    {
        $voucher->update([
            'vou_id' => $voucher->id,
            'used_at' => Carbon::now(),
            'used_by' => $referringUserId,
            'is_used' => true,
        ]);
    }

    private function upsertIncome(ReklamyReflink $reflink, float $amount): void
    {
        ReklamyIncome::updateOrCreate(['reflink_id'=>$reflink->id], ['cost'=>$amount]);
    }

    private function createReflink(int $referringUserId, int $objectId, string $objectType, string $objectText = null, float $objectValue = null): ReklamyReflink
    {
        return ReklamyReflink::create([
            'system' => request()->getHttpHost(),
            'referring_user_id' => $referringUserId,
            'object_id' => $objectId,
            'object_type' => $objectType,
            'object_value' => $objectValue,
            'object_text' => $objectText,
        ]);
    }
}
