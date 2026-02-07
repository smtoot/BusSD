<?php

namespace App\Http\Controllers\Admin;

use App\Models\Feature;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    public function index()
    {
        $pageTitle = 'All Features';
        $features = Feature::searchable(['name'])->orderByDesc('id')->paginate(getPaginate());
        return view('admin.feature.index', compact('pageTitle', 'features'));
    }

    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        if ($id) {
            $feature = Feature::findOrFail($id);
            $message = 'Feature update successfully';
        } else {
            $feature = new Feature();
            $message = 'Feature created successfully';
        }

        $feature->name = $request->name;
        $feature->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function changeStatus($id)
    {
        return Feature::changeStatus($id);
    }
}
