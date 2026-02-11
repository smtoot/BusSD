<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AmenityTemplate;
use Illuminate\Http\Request;

class AmenityTemplateController extends Controller
{
    public function index()
    {
        $pageTitle = 'Trip Amenities';
        $amenities = AmenityTemplate::orderBy('sort_order')->paginate(getPaginate());
        
        return view('admin.amenity_template.index', compact('pageTitle', 'amenities'));
    }

    public function create()
    {
        $pageTitle = 'Create Amenity';
        $categories = AmenityTemplate::getCategoryLabels();
        
        return view('admin.amenity_template.form', compact('pageTitle', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:amenity_templates,key',
            'label' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', array_keys(AmenityTemplate::getCategoryLabels())),
        ]);

        $amenity = new AmenityTemplate();
        $amenity->key = $request->key;
        $amenity->label = $request->label;
        $amenity->icon = $request->icon;
        $amenity->category = $request->category;
        $amenity->is_active = $request->has('is_active') ? 1 : 0;
        $amenity->sort_order = $request->sort_order ?? 0;
        $amenity->save();

        $notify[] = ['success', 'Amenity created successfully'];
        return redirect()->route('admin.amenity.template.index')->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Amenity';
        $amenity = AmenityTemplate::findOrFail($id);
        $categories = AmenityTemplate::getCategoryLabels();
        
        return view('admin.amenity_template.form', compact('pageTitle', 'amenity', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $amenity = AmenityTemplate::findOrFail($id);

        $request->validate([
            'key' => 'required|string|max:255|unique:amenity_templates,key,' . $id,
            'label' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', array_keys(AmenityTemplate::getCategoryLabels())),
        ]);

        $amenity->key = $request->key;
        $amenity->label = $request->label;
        $amenity->icon = $request->icon;
        $amenity->category = $request->category;
        $amenity->is_active = $request->has('is_active') ? 1 : 0;
        $amenity->sort_order = $request->sort_order ?? 0;
        $amenity->save();

        $notify[] = ['success', 'Amenity updated successfully'];
        return redirect()->route('admin.amenity.template.index')->withNotify($notify);
    }

    public function status($id)
    {
        return AmenityTemplate::changeStatus($id);
    }

    public function delete($id)
    {
        $amenity = AmenityTemplate::findOrFail($id);

        // Prevent deletion of system amenities
        if ($amenity->is_system) {
            $notify[] = ['error', 'System amenities cannot be deleted'];
            return back()->withNotify($notify);
        }

        $amenity->delete();

        $notify[] = ['success', 'Amenity deleted successfully'];
        return back()->withNotify($notify);
    }
}
