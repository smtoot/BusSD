<?php

namespace App\Http\Controllers\Supervisor\Auth;

use App\Http\Controllers\Controller;
use App\Models\supervisor;
use App\Models\OwnerPasswordReset;
use Carbon\Carbon;
use Illuminate\Http\Request;


class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        $pageTitle = 'Account Recovery';
        return view('supervisor.auth.passwords.email', compact('pageTitle'));
    }

    public function sendResetCodeEmail(Request $request)
    {
        $request->validate([
            'value' => 'required',
        ]);

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $fieldType = $this->findFieldType();
        $supervisor = Supervisor::where($fieldType, $request->value)->first();
        if (!$supervisor) {
            $notify[] = ['error', 'The account could not be found'];
            return back()->withNotify($notify);
        }

        OwnerPasswordReset::where('email', $supervisor->email)->delete();
        $code = verificationCode(6);
        $ownerPasswordReset             = new OwnerPasswordReset();
        $ownerPasswordReset->email      = $supervisor->email;
        $ownerPasswordReset->token      = $code;
        $ownerPasswordReset->created_at = Carbon::now();
        $ownerPasswordReset->save();

        $ownerBrowser = osBrowser();
        notify($supervisor, 'PASS_RESET_CODE', [
            'code' => $code,
            'operating_system' => isset($ownerBrowser['os_platform']) ? $ownerBrowser['os_platform'] : '',
            'browser' => isset($ownerBrowser['browser']) ? $ownerBrowser['browser'] : '',
            'ip' => getRealIp(),
            'time' => date('Y-m-d h:i:s A')
        ],['email']);

        $email = $supervisor->email;
        session()->put('pass_res_mail', $email);

        $notify[] = ['success', 'Password reset email sent successfully'];
        return to_route('supervisor.password.code.verify')->withNotify($notify);
    }

    public function codeVerify()
    {
        $pageTitle = 'Verify Code';
        $email = session()->get('pass_res_mail');
        if (!$email) {
            $notify[] = ['error', 'Oops! session expired'];
            return to_route('supervisor.password.reset')->withNotify($notify);
        }
        return view('supervisor.auth.passwords.code_verify', compact('pageTitle', 'email'));
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code'  => 'required',
            'email' => 'required'
        ]);
        $code =  str_replace(' ', '', $request->code);

        $ownerPasswordReset = OwnerPasswordReset::where('token', $code)->where('email', $request->email)->first();
        if (!$ownerPasswordReset) {
            $notify[] = ['error', 'Verification code doesn\'t match'];
            return to_route('supervisor.password.request')->withNotify($notify);
        }
        session()->flash('fpass_email', $request->email);

        $notify[] = ['success', 'You can change your password'];
        return to_route('supervisor.password.reset', $code)->withNotify($notify);
    }

    public function findFieldType()
    {
        $input = request()->input('value');

        $fieldType = filter_var($input, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$fieldType => $input]);
        return $fieldType;
    }
}
