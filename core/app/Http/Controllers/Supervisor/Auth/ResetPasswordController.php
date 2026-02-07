<?php

namespace App\Http\Controllers\supervisor\Auth;

use App\Models\OwnerPasswordReset;
use App\Http\Controllers\Controller;
use App\Models\Supervisor;
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
            return to_route('supervisor.password.request')->withNotify($notify);
        }
        return view('supervisor.auth.passwords.reset', compact('pageTitle', 'email', 'token'));
    }

    public function reset(Request $request)
    {
        $request->validate($this->rules());

        $reset = OwnerPasswordReset::where('token', $request->token)->orderBy('created_at', 'desc')->first();
        if (!$reset) {
            $notify[] = ['error', 'Invalid verification code'];
            return to_route('supervisor.login')->withNotify($notify);
        }

        $supervisor = Supervisor::where('email', $reset->email)->first();
        $supervisor->password = Hash::make($request->password);
        $supervisor->save();

        $userIpInfo = getIpInfo();
        $userBrowser = osBrowser();
        notify($supervisor, 'PASS_RESET_DONE', [
            'operating_system' => @$userBrowser['os_platform'],
            'browser'          => @$userBrowser['browser'],
            'ip'               => @$userIpInfo['ip'],
            'time'             => @$userIpInfo['time']
        ], ['email']);

        $notify[] = ['success', 'Password changed successfully'];
        return to_route('supervisor.login')->withNotify($notify);
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
