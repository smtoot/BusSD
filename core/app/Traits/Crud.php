<?php

namespace App\Traits;

use App\Rules\FileTypeValidate;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

trait Crud
{
    public $owner;

    public function index()
    {
        $pageTitle = 'All ' . Str::plural($this->title);
        $users = $this->model::where('owner_id', $this->owner->id)
            ->searchable(['username', 'email', 'mobile'])
            ->orderByDesc('id')
            ->paginate(getPaginate());
        return view($this->view . '.index', compact('pageTitle', 'users'));
    }

    public function login($id)
    {
        $guard = $this->guard;
        $isOwner = Auth::guard('owner')->user();
        abort_if(!$isOwner, 404);
        Auth::guard('owner')->logout();
        Auth::guard($guard)->loginUsingId($id);
        return to_route("$guard.dashboard");
    }

    public function form($id = 0)
    {
        if ($id) {
            $pageTitle = 'Edit ' . $this->title;
            $user = $this->model::findOrFail($id);
        } else {
            $pageTitle = 'Add New ' . $this->title;
            $user = [];
        }
        $info       = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = isset($info['code']) ? implode(',', $info['code']) : '';
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view($this->view . '.form', compact('pageTitle', 'user', 'countries', 'mobileCode'));
    }

    public function store(Request $request, $id = 0)
    {
        $countryData  = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes  = implode(',', array_column($countryData, 'dial_code'));
        $countries    = implode(',', array_column($countryData, 'country'));

        $request->validate([
            'firstname'    => 'required|string|max:40',
            'lastname'     => 'required|string|max:40',
            'password'     => 'nullable|string|min:6|confirmed',
            'address'      => 'nullable|string',
            'city'         => 'nullable|string',
            'state'        => 'nullable|string',
            'zip'          => 'nullable',
            'country_code' => 'required|in:' . $countryCodes,
            'country'      => 'required|in:' . $countries,
            'mobile_code'  => 'required|in:' . $mobileCodes,
            'mobile'       => ['required', 'regex:/^([0-9]*)$/', Rule::unique($this->tableName)->where('dial_code', $request->mobile_code)->ignore($id)],
            'image'        => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ], [
            'firstname.required' => 'The first name field is required',
            'lastname.required'  => 'The last name field is required'
        ]);

        if ($id) {
            $user = $this->model::findOrFail($id);
            $message = $this->title . ' updated successfully';
        } else {
            $tableName = $this->tableName;
            $request->validate([
                'username'     => "required|min:6|unique:$tableName,username",
                'email'        => "required|string|email|max:40|unique:$tableName,email",
            ]);

            if (preg_match("/[^a-z0-9_]/", trim($request->username))) {
                $notify[] = ['info', 'Username can contain only small letters, numbers and underscore.'];
                $notify[] = ['error', 'No special character, space or capital letters in username.'];
                return back()->withNotify($notify)->withInput($request->all());
            }

            $user               = new $this->model();
            $user->owner_id     = $this->owner->id;
            $user->password     = Hash::make($request->password ?? 123456);
            $user->username     = $request->username;
            $user->email        = strtolower($request->email);

            $message = $this->title . ' created successfully';
        }

        if ($request->hasFile('image')) {
            try {
                $oldImage = $user->image;
                $user->image = fileUploader($request->image, getFilePath($this->fileInfo), getFileSize($this->fileInfo), $oldImage);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the image.'];
                return back()->withNotify($notify);
            }
        }

        $user->firstname    = $request->firstname;
        $user->lastname     = $request->lastname;
        $user->dial_code    = $request->mobile_code;
        $user->mobile       = $request->mobile;
        $user->country_code = $request->country_code;
        $user->country_name = @$request->country;
        $user->address      = $request->address;
        $user->city         = $request->city;
        $user->state        = $request->state;
        $user->zip          = $request->zip;
        $user->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }



    public function status($id)
    {
        return $this->model::changeStatus($id);
    }
}
