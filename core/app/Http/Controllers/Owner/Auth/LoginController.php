<?php

namespace App\Http\Controllers\Owner\Auth;

use App\Http\Controllers\Controller;
use App\Models\OwnerLogin;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Laramin\Utility\Onumoti;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function showLoginForm()
    {
        $pageTitle = "Owner Login";
        return view('owner.auth.login', compact('pageTitle'));
    }

    protected function guard()
    {
        return auth()->guard('owner');
    }

    public function username()
    {
        return 'username';
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        $request->session()->regenerateToken();

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        Onumoti::getData();

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function logout(Request $request)
    {
        $this->guard('owner')->logout();
        $request->session()->invalidate();
        $notify[] = ['success', 'You have been logged out.'];
        return to_route('owner.login')->withNotify($notify);
    }

    public function authenticated(Request $request, $owner)
    {
        if ($owner->status == 0) {
            $this->guard()->logout();
            return redirect()->route('owner.login')->withErrors(['Your account has been deactivated.']);
        }

        $ip = getRealIP();
        $exist = OwnerLogin::where('owner_ip', $ip)->first();
        $ownerLogin = new OwnerLogin();
        if ($exist) {
            $ownerLogin->longitude =  $exist->longitude;
            $ownerLogin->latitude =  $exist->latitude;
            $ownerLogin->city =  $exist->city;
            $ownerLogin->country_code = $exist->country_code;
            $ownerLogin->country =  $exist->country;
        } else {
            $info = json_decode(json_encode(getIpInfo()), true);
            $ownerLogin->longitude =  isset($info['long']) ? implode(',',$info['long']) : '';
            $ownerLogin->latitude =  isset($info['lat']) ? implode(',',$info['lat']) : '';
            $ownerLogin->city =  isset($info['city']) ? implode(',',$info['city']) : '';
            $ownerLogin->country_code = isset($info['code']) ? implode(',',$info['code']) : '';
            $ownerLogin->country =  isset($info['country']) ? implode(',',$info['country']) : '';
        }

        $userAgent = osBrowser();
        $ownerLogin->owner_id = $owner->id;
        $ownerLogin->owner_ip =  $ip;

        $ownerLogin->browser = isset($userAgent['browser']) ? $userAgent['browser'] : '';
        $ownerLogin->os = isset($userAgent['os_platform']) ? $userAgent['os_platform'] : '';
        $ownerLogin->save();

        return to_route('owner.dashboard');
    }
}
