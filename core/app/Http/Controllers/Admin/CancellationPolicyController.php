<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CancellationPolicy;
use Illuminate\Http\Request;

class CancellationPolicyController extends Controller
{
    public function index()
    {
        $pageTitle = 'Cancellation Policies';
        $policies = CancellationPolicy::orderBy('sort_order')->paginate(getPaginate());
        
        return view('admin.cancellation_policy.index', compact('pageTitle', 'policies'));
    }

    public function create()
    {
        $pageTitle = 'Create Cancellation Policy';
        
        return view('admin.cancellation_policy.form', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:cancellation_policies,name',
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'rules' => 'required|array|min:1',
            'rules.*.hours_before' => 'required|integer|min:0',
            'rules.*.refund_percentage' => 'required|integer|min:0|max:100',
        ]);

        $policy = new CancellationPolicy();
        $policy->name = $request->name;
        $policy->label = $request->label;
        $policy->description = $request->description;
        $policy->rules = $request->rules;
        $policy->is_default = $request->has('is_default') ? 1 : 0;
        $policy->is_active = $request->has('is_active') ? 1 : 0;
        $policy->sort_order = $request->sort_order ?? 0;
        $policy->save();

        // If set as default, remove default from others
        if ($policy->is_default) {
            CancellationPolicy::where('id', '!=', $policy->id)->update(['is_default' => 0]);
        }

        $notify[] = ['success', 'Cancellation policy created successfully'];
        return redirect()->route('admin.cancellation.policy.index')->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Cancellation Policy';
        $policy = CancellationPolicy::findOrFail($id);
        
        return view('admin.cancellation_policy.form', compact('pageTitle', 'policy'));
    }

    public function update(Request $request, $id)
    {
        $policy = CancellationPolicy::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:cancellation_policies,name,' . $id,
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'rules' => 'required|array|min:1',
            'rules.*.hours_before' => 'required|integer|min:0',
            'rules.*.refund_percentage' => 'required|integer|min:0|max:100',
        ]);

        $policy->name = $request->name;
        $policy->label = $request->label;
        $policy->description = $request->description;
        $policy->rules = $request->rules;
        $policy->is_default = $request->has('is_default') ? 1 : 0;
        $policy->is_active = $request->has('is_active') ? 1 : 0;
        $policy->sort_order = $request->sort_order ?? 0;
        $policy->save();

        // If set as default, remove default from others
        if ($policy->is_default) {
            CancellationPolicy::where('id', '!=', $policy->id)->update(['is_default' => 0]);
        }

        $notify[] = ['success', 'Cancellation policy updated successfully'];
        return redirect()->route('admin.cancellation.policy.index')->withNotify($notify);
    }

    public function status($id)
    {
        return CancellationPolicy::changeStatus($id);
    }

    public function delete($id)
    {
        $policy = CancellationPolicy::findOrFail($id);

        // Prevent deletion of system policies
        if ($policy->is_system) {
            $notify[] = ['error', 'System policies cannot be deleted'];
            return back()->withNotify($notify);
        }

        // Check if policy is in use
        if ($policy->trips()->count() > 0) {
            $notify[] = ['error', 'Cannot delete policy that is being used by trips'];
            return back()->withNotify($notify);
        }

        $policy->delete();

        $notify[] = ['success', 'Cancellation policy deleted successfully'];
        return back()->withNotify($notify);
    }
}
