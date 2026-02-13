<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index()
    {
        $pageTitle = 'All Cities';
        $cities = City::orderBy('name')->paginate(getPaginate());
        return view('admin.city.index', compact('pageTitle', 'cities'));
    }

    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:cities,name,' . $id,
        ]);

        if ($id) {
            $city = City::findOrFail($id);
            $message = 'City updated successfully';
        } else {
            $city = new City();
            $message = 'City added successfully';
        }

        $city->name = $request->name;
        $city->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return City::changeStatus($id);
    }
}
