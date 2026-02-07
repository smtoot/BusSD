<?php

namespace App\Http\Controllers\Admin;

use App\Models\Package;
use App\Lib\RequiredConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PackageController extends Controller
{
    public function index()
    {
        $pageTitle = 'All Packages';
        $packages = Package::searchable(['name'])->orderByDesc('id')->paginate(getPaginate());
        return view('admin.package.index', compact('pageTitle', 'packages'));
    }

    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'price'      => 'required|numeric|gt:0',
            'time_limit' => 'required|integer|gt:0',
            'unit'       => 'required|integer|gt:0'
        ]);

        if ($id) {
            $package = Package::findOrFail($id);
            $message = 'Package updated successfully';
        } else {
            $package = new Package();
            $message = 'Package created successfully';
        }

        $package->name       = $request->name;
        $package->price      = $request->price;
        $package->time_limit = $request->time_limit;
        $package->unit       = $request->unit;
        $package->save();

        RequiredConfig::configured('package');
        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Package::changeStatus($id);
    }
}
