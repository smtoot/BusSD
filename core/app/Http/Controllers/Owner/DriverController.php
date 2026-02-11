<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Traits\Crud;

class DriverController extends Controller
{
    use Crud;

    protected $model = Driver::class;
    protected $view = 'owner.driver';
    protected $title = 'Driver';
    protected $fileInfo = 'driver';
    protected $tableName = 'drivers';
    protected $guard = 'driver';

    public function __construct()
    {
        $this->owner = authUser();
    }

    public function index()
    {
        $pageTitle = 'Driver Management';
        $owner = $this->owner;
        
        $users = Driver::where('owner_id', $owner->id)
            ->searchable(['username', 'email', 'mobile', 'firstname', 'lastname'])
            ->orderByDesc('id')
            ->paginate(getPaginate());

        // KPI Stats
        $stats = [
            'total' => Driver::where('owner_id', $owner->id)->count(),
            'active' => Driver::where('owner_id', $owner->id)->active()->count(),
            'expiring_soon' => Driver::where('owner_id', $owner->id)->expiringSoon()->count(),
        ];

        return view($this->view . '.index', compact('pageTitle', 'users', 'stats'));
    }

    public function store(\Illuminate\Http\Request $request, $id = 0)
    {
        $this->validateDriver($request, $id);

        if ($id) {
            $user = Driver::where('owner_id', $this->owner->id)->findOrFail($id);
            $message = 'Driver updated successfully';
        } else {
            $user = new Driver();
            $user->owner_id = $this->owner->id;
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password ?? 123456);
            $user->username = $request->username;
            $user->email = strtolower($request->email);
            $message = 'Driver created successfully';
        }

        if ($request->hasFile('image')) {
            $user->image = fileUploader($request->image, getFilePath($this->fileInfo), getFileSize($this->fileInfo), $user->image);
        }

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->dial_code = $request->mobile_code;
        $user->mobile = $request->mobile;
        $user->country_code = $request->country_code;
        $user->country_name = $request->country;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->zip = $request->zip;
        
        // Saudi Model Fields
        $user->nationality = $request->nationality;
        $user->id_type = $request->id_type;
        $user->id_number = $request->id_number;
        $user->license_number = $request->license_number;
        $user->license_expiry_date = $request->license_expiry_date;

        $user->permissions = $request->permissions ? json_encode($request->permissions) : null;
        $user->save();

        $notify[] = ['success', $message];
        return to_route('owner.driver.index')->withNotify($notify);
    }

    protected function validateDriver($request, $id)
    {
        $countryData = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes = implode(',', array_column($countryData, 'dial_code'));
        $countries = implode(',', array_column($countryData, 'country'));

        $rules = [
            'firstname' => 'required|string|max:40',
            'lastname' => 'required|string|max:40',
            'password' => $id ? 'nullable|string|min:6|confirmed' : 'nullable|string|min:6',
            'country_code' => 'required|in:' . $countryCodes,
            'country' => 'required|in:' . $countries,
            'mobile_code' => 'required|in:' . $mobileCodes,
            'mobile' => ['required', 'regex:/^([0-9]*)$/', \Illuminate\Validation\Rule::unique('drivers')->where('dial_code', $request->mobile_code)->ignore($id)],
            'image' => ['nullable', 'image', new \App\Rules\FileTypeValidate(['jpg', 'jpeg', 'png'])],
            
            // Saudi Fields Validation
            'nationality' => 'required|string|max:40',
            'id_type' => 'required|string|max:40',
            'id_number' => 'required|string|max:40',
            'license_number' => 'required|string|max:40',
            'license_expiry_date' => 'required|date',
        ];

        if (!$id) {
            $rules['username'] = 'required|min:6|unique:drivers,username';
            $rules['email'] = 'required|email|max:40|unique:drivers,email';
        }

        $request->validate($rules);

        if (!$id && preg_match("/[^a-z0-9_]/", trim($request->username))) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'username' => 'Username can contain only small letters, numbers and underscore.'
            ]);
        }
    }
}
