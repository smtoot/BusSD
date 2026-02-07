<?php

namespace App\Http\Controllers\Owner\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Owner;
use App\Models\OwnerLogin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    use RegistersUsers;

    public function __construct()
    {
        parent::__construct();
    }

    protected function guard()
    {
        return auth()->guard('owner');
    }

    public function showRegistrationForm()
    {
        if (!gs('registration')) {
            $notify[] = ['error', 'Registration currently disabled'];
            return to_route('home')->withNotify($notify);
        }
        $pageTitle = "Register";
        return view('owner.auth.register', compact('pageTitle'));
    }

    protected function validator(array $data)
    {
        $passwordValidation = Password::min(6);
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $agree = 'nullable';
        if (gs('agree')) {
            $agree = 'required';
        }

        $validate     = Validator::make(
            $data,
            [
                'firstname' => 'required',
                'lastname'  => 'required',
                'email'     => 'required|string|email|unique:owners',
                'password'  => ['required', 'confirmed', $passwordValidation],
                'captcha'   => 'sometimes|required',
                'agree'     => $agree
            ],
            [
                'firstname.required' => 'The first name field is required',
                'lastname.required'  => 'The last name field is required'
            ]
        );
        return $validate;
    }

    public function register(Request $request)
    {
        if (!gs('registration')) {
            $notify[] = ['error', 'Registration not allowed'];
            return back()->withNotify($notify);
        }
        $this->validator($request->all())->validate();

        $request->session()->regenerateToken();

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    protected function create(array $data)
    {
        $owner            = new Owner();
        $owner->email     = strtolower($data['email']);
        $owner->firstname = $data['firstname'];
        $owner->lastname  = $data['lastname'];
        $owner->password  = Hash::make($data['password']);
        $owner->ev        = gs('ev') ? Status::NO : Status::YES;
        $owner->sv        = gs('sv') ? Status::NO : Status::YES;
        $owner->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->owner_id  = $owner->id;
        $adminNotification->title     = 'New owner registered';
        $adminNotification->click_url = urlPath('admin.users.detail', $owner->id);
        $adminNotification->save();

        //Login Log Create
        $ip        = getRealIP();
        $exist     = OwnerLogin::where('owner_ip', $ip)->first();
        $ownerLogin = new OwnerLogin();

        if ($exist) {
            $ownerLogin->longitude    = $exist->longitude;
            $ownerLogin->latitude     = $exist->latitude;
            $ownerLogin->city         = $exist->city;
            $ownerLogin->country_code = $exist->country_code;
            $ownerLogin->country      = $exist->country;
        } else {
            $info                     = json_decode(json_encode(getIpInfo()), true);
            $ownerLogin->longitude    = isset($info['long']) ? implode(',', $info['long']) : '';
            $ownerLogin->latitude     = isset($info['lat']) ? implode(',', $info['lat']) : '';
            $ownerLogin->city         = isset($info['city']) ? implode(',', $info['city']) : '';
            $ownerLogin->country_code = isset($info['code']) ? implode(',', $info['code']) : '';
            $ownerLogin->country      = isset($info['country']) ? implode(',', $info['country']) : '';
        }

        $userAgent            = osBrowser();
        $ownerLogin->owner_id = $owner->id;
        $ownerLogin->owner_ip = $ip;
        $ownerLogin->browser = isset($userAgent['browser']) ? $userAgent['browser'] : '';
        $ownerLogin->os      = isset($userAgent['os_platform']) ? $userAgent['os_platform'] : '';
        $ownerLogin->save();

        return $owner;
    }

    public function checkUser(Request $request)
    {
        $exist['data'] = false;
        $exist['type'] = null;
        if ($request->email) {
            $exist['data'] = Owner::where('email', $request->email)->exists();
            $exist['type'] = 'email';
            $exist['field'] = 'Email';
        }
        if ($request->mobile) {
            $exist['data'] = Owner::where('mobile', $request->mobile)->where('dial_code', $request->mobile_code)->exists();
            $exist['type'] = 'mobile';
            $exist['field'] = 'Mobile';
        }
        if ($request->username) {
            $exist['data'] = Owner::where('username', $request->username)->exists();
            $exist['type'] = 'username';
            $exist['field'] = 'Username';
        }
        return response($exist);
    }

    public function registered()
    {
        return to_route('owner.dashboard');
    }
}
