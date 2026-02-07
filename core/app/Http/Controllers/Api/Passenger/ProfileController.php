<?php

namespace App\Http\Controllers\Api\Passenger;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    use ApiResponse;

    public function show(Request $request)
    {
        $passenger = $request->user();

        return $this->apiSuccess(null, [
            'passenger' => $passenger->only([
                'id', 'firstname', 'lastname', 'email', 'dial_code',
                'mobile', 'image', 'status', 'ev', 'sv', 'profile_complete',
                'created_at'
            ])
        ]);
    }

    public function update(Request $request)
    {
        $passenger = $request->user();

        $validator = Validator::make($request->all(), [
            'firstname' => 'sometimes|string|max:40',
            'lastname'  => 'sometimes|string|max:40',
            'image'     => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->apiError($validator->errors()->all(), 422);
        }

        if ($request->has('firstname')) $passenger->firstname = $request->firstname;
        if ($request->has('lastname'))  $passenger->lastname  = $request->lastname;

        if ($request->hasFile('image')) {
            $passenger->image = fileUploader($request->file('image'), getFilePath('passengerProfile'), getFileSize('passengerProfile'), $passenger->image);
        }

        $passenger->save();

        return $this->apiSuccess('Profile updated successfully.', [
            'passenger' => $passenger->only([
                'id', 'firstname', 'lastname', 'email', 'dial_code',
                'mobile', 'image', 'status', 'ev', 'sv', 'profile_complete'
            ])
        ]);
    }
}
