<?php

namespace App\Http\Controllers\Owner;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Constants\Status;

class AuthorizationController extends Controller
{
    protected function checkCodeValidity($owner, $addMin = 2)
    {
        if (!$owner->ver_code_send_at) {
            return false;
        }
        if ($owner->ver_code_send_at->addMinutes($addMin) < Carbon::now()) {
            return false;
        }
        return true;
    }

    public function authorizeForm()
    {
        $owner = authUser();
        if (!$owner->status) {
            $pageTitle = 'Banned';
            $type = 'ban';
        } elseif (!$owner->ev) {
            $type = 'email';
            $pageTitle = 'Verify Email';
            $notifyTemplate = 'EVER_CODE';
        } elseif (!$owner->sv) {
            $type = 'sms';
            $pageTitle = 'Verify Mobile Number';
            $notifyTemplate = 'SVER_CODE';
        } else {
            return to_route('owner.dashboard');
        }

        if (!$this->checkCodeValidity($owner) && ($type != 'ban')) {
            $owner->ver_code = verificationCode(6);
            $owner->ver_code_send_at = Carbon::now();
            $owner->save();
            notify($owner, $notifyTemplate, [
                'code' => $owner->ver_code
            ], [$type]);
        }
        return view('owner.auth.authorization.' . $type, compact('owner', 'pageTitle'));
    }

    public function sendVerifyCode($type)
    {
        $owner = authUser();

        if ($this->checkCodeValidity($owner)) {
            $targetTime = $owner->ver_code_send_at->addMinutes(2)->timestamp;
            $delay = $targetTime - time();
            throw ValidationException::withMessages(['resend' => 'Please try after ' . $delay . ' seconds']);
        }

        $owner->ver_code = verificationCode(6);
        $owner->ver_code_send_at = Carbon::now();
        $owner->save();

        if ($type == 'email') {
            $type = 'email';
            $notifyTemplate = 'EVER_CODE';
        } else {
            $type = 'sms';
            $notifyTemplate = 'SVER_CODE';
        }

        notify($owner, $notifyTemplate, [
            'code' => $owner->ver_code
        ], [$type]);

        $notify[] = ['success', 'Verification code sent successfully'];
        return back()->withNotify($notify);
    }

    public function emailVerification(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]);

        $owner = authUser();

        if ($owner->ver_code == $request->code) {
            $owner->ev = Status::VERIFIED;
            $owner->ver_code = null;
            $owner->ver_code_send_at = null;
            $owner->save();

            return to_route('owner.dashboard');
        }
        throw ValidationException::withMessages(['code' => 'Verification code didn\'t match!']);
    }

    public function mobileVerification(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);

        $owner = authUser();
        if ($owner->ver_code == $request->code) {
            $owner->sv = Status::VERIFIED;
            $owner->ver_code = null;
            $owner->ver_code_send_at = null;
            $owner->save();

            return to_route('owner.dashboard');
        }
        throw ValidationException::withMessages(['code' => 'Verification code didn\'t match!']);
    }
}
