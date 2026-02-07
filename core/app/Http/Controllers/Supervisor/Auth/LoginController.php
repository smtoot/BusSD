<?php

namespace App\Http\Controllers\Supervisor\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Laramin\Utility\Onumoti;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function showLoginForm()
    {
        $pageTitle = "Supervisor Login";
        return view('supervisor.auth.login', compact('pageTitle'));
    }

    protected function guard()
    {
        return auth()->guard('supervisor');
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
        $this->guard('supervisor')->logout();
        $request->session()->invalidate();
        $notify[] = ['success', 'You have been logged out.'];
        return to_route('supervisor.login')->withNotify($notify);
    }

    public function authenticated(Request $request, $owner)
    {
        if ($owner->status == 0) {
            $this->guard()->logout();
            return redirect()->route('supervisor.login')->withErrors(['Your account has been deactivated.']);
        }
        return to_route('supervisor.dashboard');
    }
}
