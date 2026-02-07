<?php

namespace App\Http\Controllers\CoOwner\Auth;

use App\Models\OwnerPasswordReset;
use App\Http\Controllers\Controller;
use App\Models\CoOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token)
    {
        $pageTitle = "Reset Password";
        $email = session('fpass_email');
        $token = session()->has('token') ? session('token') : $token;
        $resetToken = OwnerPasswordReset::where('token', $token)->where('email', $email)->first();
        if (!$resetToken) {
            $notify[] = ['error', 'Invalid token'];
            return to_route('co-owner.password.request')->withNotify($notify);
        }
        return view('co_owner.auth.passwords.reset', compact('pageTitle', 'email', 'token'));
    }


    public function reset(Request $request)
    {
        $request->validate($this->rules());

        $reset = OwnerPasswordReset::where('token', $request->token)->orderBy('created_at', 'desc')->first();
        if (!$reset) {
            $notify[] = ['error', 'Invalid verification code'];
            return to_route('co-owner.login')->withNotify($notify);
        }

        $owner = CoOwner::where('email', $reset->email)->first();
        $owner->password = Hash::make($request->password);
        $owner->save();

        $userBrowser = osBrowser();
        notify($owner, 'PASS_RESET_DONE', [
            'operating_system' => isset($userBrowser['os_platform']) ? $userBrowser['os_platform'] : '',
            'browser' => isset($userBrowser['browser']) ? $userBrowser['browser'] : '',
            'ip' => getRealIp(),
            'time' => date('Y-m-d h:i:s A')
        ],['email']);

        $notify[] = ['success', 'Password changed successfully'];
        return to_route('co-owner.login')->withNotify($notify);
    }

    protected function rules()
    {
        $passwordValidation = Password::min(6);
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }
        return [
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => ['required', 'confirmed', $passwordValidation],
        ];
    }
}
