<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DynamicPricingRule;
use App\Models\Owner;
use Illuminate\Http\Request;

class DynamicPricingController extends Controller
{
    public function index()
    {
        $pageTitle = __('All Dynamic Pricing Rules');
        $rules = DynamicPricingRule::query()
            ->with(['owner', 'route', 'fleetType'])
            ->searchable(['name'])
            ->filter(['owner_id', 'route_id', 'rule_type'])
            ->orderBy('priority')
            ->orderByDesc('id')
            ->paginate(getPaginate());

        return view('admin.dynamic-pricing.index', compact('pageTitle', 'rules'));
    }

    public function create()
    {
        $pageTitle = __('Create Pricing Rule');
        $owners = Owner::active()->orderBy('fullname')->get();
        return view('admin.dynamic-pricing.create', compact('pageTitle', 'owners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'owner_id' => 'nullable|integer|exists:owners,id',
            'route_id' => 'nullable|integer|exists:routes,id',
            'fleet_type_id' => 'nullable|integer|exists:fleet_types,id',
            'rule_type' => 'required|in:surge,early_bird,last_minute,weekend,holiday,custom',
            'operator' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_hours_before_departure' => 'nullable|integer|min:0',
            'max_hours_before_departure' => 'nullable|integer|min:0',
            'applicable_days' => 'nullable|array',
            'applicable_days.*' => 'integer|between:0,6',
            'applicable_dates' => 'nullable|array',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'min_seats_available' => 'nullable|integer|min:0',
            'max_seats_available' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'priority' => 'nullable|integer|min:0',
        ]);

        $rule = new DynamicPricingRule();
        $rule->owner_id = $request->owner_id ?? 0; // 0 for global
        $rule->route_id = $request->route_id;
        $rule->fleet_type_id = $request->fleet_type_id;
        $rule->name = $request->name;
        $rule->rule_type = $request->rule_type;
        $rule->operator = $request->operator;
        $rule->value = $request->value;
        $rule->min_hours_before_departure = $request->min_hours_before_departure;
        $rule->max_hours_before_departure = $request->max_hours_before_departure;
        $rule->applicable_days = $request->applicable_days;
        $rule->applicable_dates = $request->applicable_dates;
        $rule->start_time = $request->start_time;
        $rule->end_time = $request->end_time;
        $rule->min_seats_available = $request->min_seats_available;
        $rule->max_seats_available = $request->max_seats_available;
        $rule->is_active = $request->is_active ?? true;
        $rule->valid_from = $request->valid_from;
        $rule->valid_until = $request->valid_until;
        $rule->priority = $request->priority ?? 0;
        $rule->save();

        $notify[] = ['success', __('Pricing rule created successfully')];
        return to_route('admin.dynamic-pricing.index')->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = __('Edit Pricing Rule');
        $rule = DynamicPricingRule::findOrFail($id);
        $owners = Owner::active()->orderBy('fullname')->get();
        return view('admin.dynamic-pricing.edit', compact('pageTitle', 'rule', 'owners'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'owner_id' => 'nullable|integer|exists:owners,id',
            'route_id' => 'nullable|integer|exists:routes,id',
            'fleet_type_id' => 'nullable|integer|exists:fleet_types,id',
            'rule_type' => 'required|in:surge,early_bird,last_minute,weekend,holiday,custom',
            'operator' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_hours_before_departure' => 'nullable|integer|min:0',
            'max_hours_before_departure' => 'nullable|integer|min:0',
            'applicable_days' => 'nullable|array',
            'applicable_days.*' => 'integer|between:0,6',
            'applicable_dates' => 'nullable|array',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'min_seats_available' => 'nullable|integer|min:0',
            'max_seats_available' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'priority' => 'nullable|integer|min:0',
        ]);

        $rule = DynamicPricingRule::findOrFail($id);
        $rule->owner_id = $request->owner_id ?? 0;
        $rule->route_id = $request->route_id;
        $rule->fleet_type_id = $request->fleet_type_id;
        $rule->name = $request->name;
        $rule->rule_type = $request->rule_type;
        $rule->operator = $request->operator;
        $rule->value = $request->value;
        $rule->min_hours_before_departure = $request->min_hours_before_departure;
        $rule->max_hours_before_departure = $request->max_hours_before_departure;
        $rule->applicable_days = $request->applicable_days;
        $rule->applicable_dates = $request->applicable_dates;
        $rule->start_time = $request->start_time;
        $rule->end_time = $request->end_time;
        $rule->min_seats_available = $request->min_seats_available;
        $rule->max_seats_available = $request->max_seats_available;
        $rule->is_active = $request->is_active ?? true;
        $rule->valid_from = $request->valid_from;
        $rule->valid_until = $request->valid_until;
        $rule->priority = $request->priority ?? 0;
        $rule->save();

        $notify[] = ['success', __('Pricing rule updated successfully')];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        $rule = DynamicPricingRule::findOrFail($id);
        $rule->is_active = !$rule->is_active;
        $rule->save();

        $notify[] = ['success', __('Pricing rule status updated successfully')];
        return back()->withNotify($notify);
    }

    public function delete($id)
    {
        $rule = DynamicPricingRule::findOrFail($id);
        $rule->delete();

        $notify[] = ['success', __('Pricing rule deleted successfully')];
        return back()->withNotify($notify);
    }
}
