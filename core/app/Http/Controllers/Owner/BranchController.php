<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Counter; // Backward compatibility alias
use App\Models\CounterManager;
use App\Models\City;
use App\Traits\Crud;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    use Crud;

    protected $model = CounterManager::class;
    protected $view = 'owner.counter-manager';
    protected $title = 'Branch Manager';
    protected $fileInfo = 'branch_manager';
    protected $tableName = 'counter_managers'; // Keep same for now
    protected $guard = 'manager';


    public  function __construct()
    {
        $this->owner = authUser();
    }

    public function counter()
    {
        $pageTitle = 'All Branches';
        $owner = authUser();
        $counterManagers = $this->model::active()->where('owner_id', $owner->id)->get();
        $cities = City::active()->orderBy('name')->get();
        $branches = Branch::where('owner_id', $owner->id)
            ->with(['counterManager', 'city'])
            ->searchable(['name', 'mobile', 'code'])
            ->orderByDesc('id')
            ->paginate(getPaginate());
        
        // Backward compatibility - also provide as $counters
        $counters = $branches;
        
        return view('owner.counter.index', compact('pageTitle', 'counters', 'branches', 'counterManagers', 'owner', 'cities'));
    }

    public function create()
    {
        $pageTitle = 'Create New Branch';
        $owner = authUser();
        $counterManagers = $this->model::active()->where('owner_id', $owner->id)->get();
        $cities = City::active()->orderBy('name')->get();
        $timezones = \DateTimeZone::listIdentifiers();
        
        return view('owner.counter.form', compact('pageTitle', 'counterManagers', 'cities', 'timezones'));
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Branch';
        $owner = authUser();
        $branch = Branch::where('owner_id', $owner->id)->findOrFail($id);
        $counterManagers = $this->model::active()->where('owner_id', $owner->id)->get();
        $cities = City::active()->orderBy('name')->get();
        $timezones = \DateTimeZone::listIdentifiers();
        
        return view('owner.counter.form', compact('pageTitle', 'branch', 'counterManagers', 'cities', 'timezones'));
    }

    public function counterStore(Request $request)
    {
        $request->validate([
            'name'                    => 'required|string|max:40',
            'mobile'                  => 'required|string|max:40',
            'city_id'                 => 'required|integer|exists:cities,id',
            'location'                => 'nullable|string|max:255',
            'counter_manager'         => 'nullable|integer|exists:counter_managers,id',
            'contact_email'           => 'nullable|email|max:100',
            'type'                    => 'nullable|in:headquarters,branch,sub_branch',
            'autonomy_level'          => 'nullable|in:controlled,semi_autonomous,autonomous',
            'can_set_routes'          => 'nullable|boolean',
            'can_adjust_pricing'      => 'nullable|boolean',
            'pricing_variance_limit'  => 'nullable|integer|min:0|max:100',
            'allows_online_booking'   => 'nullable|boolean',
            'allows_counter_booking'  => 'nullable|boolean',
            'timezone'                => 'nullable|string|max:100',
            'tax_registration_no'     => 'nullable|string|max:100',
        ]);

        $owner = authUser();
        $notify = [];

        // Handle counter manager assignment
        if ($request->counter_manager && $request->counter_manager > 0) {
            $counterManager = CounterManager::where('owner_id', $owner->id)->findOrFail($request->counter_manager);
            
            // If this manager is already assigned to another counter, remove that assignment
            if ($counterManager->counter) {
                $existingCounter = $counterManager->counter;
                $existingCounter->counter_manager_id = 0;
                $existingCounter->save();
                $notify[] = ['info', 'Branch manager removed from previous branch.'];
            }
        }

        $branch = new Branch();
        $branch->owner_id = $owner->id;
        $branch->counter_manager_id = $request->counter_manager ?? 0;
        $branch->name               = $request->name;
        $branch->mobile             = $request->mobile;
        $branch->city_id            = $request->city_id;
        $branch->location           = $request->location;
        
        // New fields
        $branch->contact_email            = $request->contact_email;
        $branch->type                     = $request->type ?? 'branch';
        $branch->autonomy_level           = $request->autonomy_level ?? 'controlled';
        $branch->can_set_routes           = $request->has('can_set_routes') ? 1 : 0;
        $branch->can_adjust_pricing       = $request->has('can_adjust_pricing') ? 1 : 0;
        $branch->pricing_variance_limit   = $request->pricing_variance_limit ?? 0;
        $branch->allows_online_booking    = $request->has('allows_online_booking') ? 1 : 0;
        $branch->allows_counter_booking   = $request->has('allows_counter_booking') ? 1 : 0;
        $branch->timezone                 = $request->timezone;
        $branch->tax_registration_no      = $request->tax_registration_no;
        
        // Bank account details (JSON)
        $bankDetails = [];
        if ($request->bank_account_name || $request->bank_account_number || $request->bank_name || $request->bank_iban) {
            $bankDetails = [
                'name'   => $request->bank_account_name,
                'number' => $request->bank_account_number,
                'bank'   => $request->bank_name,
                'iban'   => $request->bank_iban,
            ];
        }
        $branch->bank_account_details = !empty($bankDetails) ? json_encode($bankDetails) : null;
        
        $branch->save();

        $notify[] = ['success', 'Branch created successfully'];
        return redirect()->route('owner.counter.index')->withNotify($notify);
    }

    public function counterUpdate(Request $request, $id)
    {
        $request->validate([
            'name'                    => 'required|string|max:40',
            'mobile'                  => 'required|string|max:40',
            'city_id'                 => 'required|integer|exists:cities,id',
            'location'                => 'nullable|string|max:255',
            'counter_manager'         => 'nullable|integer|exists:counter_managers,id',
            'contact_email'           => 'nullable|email|max:100',
            'type'                    => 'nullable|in:headquarters,branch,sub_branch',
            'autonomy_level'          => 'nullable|in:controlled,semi_autonomous,autonomous',
            'can_set_routes'          => 'nullable|boolean',
            'can_adjust_pricing'      => 'nullable|boolean',
            'pricing_variance_limit'  => 'nullable|integer|min:0|max:100',
            'allows_online_booking'   => 'nullable|boolean',
            'allows_counter_booking'  => 'nullable|boolean',
            'timezone'                => 'nullable|string|max:100',
            'tax_registration_no'     => 'nullable|string|max:100',
        ]);

        $owner = authUser();
        $branch = Branch::where('owner_id', $owner->id)->findOrFail($id);
        $notify = [];

        // Handle counter manager assignment
        if ($request->counter_manager && $request->counter_manager > 0) {
            $counterManager = CounterManager::where('owner_id', $owner->id)->findOrFail($request->counter_manager);
            
            // If this manager is already assigned to another counter (not this one), remove that assignment
            if ($counterManager->counter && $counterManager->counter->id != $id) {
                $existingCounter = $counterManager->counter;
                $existingCounter->counter_manager_id = 0;
                $existingCounter->save();
                $notify[] = ['info', 'Branch manager removed from previous branch.'];
            }
        }

        $branch->counter_manager_id = $request->counter_manager ?? 0;
        $branch->name               = $request->name;
        $branch->mobile             = $request->mobile;
        $branch->city_id            = $request->city_id;
        $branch->location           = $request->location;
        
        // New fields
        $branch->contact_email            = $request->contact_email;
        $branch->type                     = $request->type ?? 'branch';
        $branch->autonomy_level           = $request->autonomy_level ?? 'controlled';
        $branch->can_set_routes           = $request->has('can_set_routes') ? 1 : 0;
        $branch->can_adjust_pricing       = $request->has('can_adjust_pricing') ? 1 : 0;
        $branch->pricing_variance_limit   = $request->pricing_variance_limit ?? 0;
        $branch->allows_online_booking    = $request->has('allows_online_booking') ? 1 : 0;
        $branch->allows_counter_booking   = $request->has('allows_counter_booking') ? 1 : 0;
        $branch->timezone                 = $request->timezone;
        $branch->tax_registration_no      = $request->tax_registration_no;
        
        // Bank account details (JSON)
        $bankDetails = [];
        if ($request->bank_account_name || $request->bank_account_number || $request->bank_name || $request->bank_iban) {
            $bankDetails = [
                'name'   => $request->bank_account_name,
                'number' => $request->bank_account_number,
                'bank'   => $request->bank_name,
                'iban'   => $request->bank_iban,
            ];
        }
        $branch->bank_account_details = !empty($bankDetails) ? json_encode($bankDetails) : null;
        
        $branch->save();

        $notify[] = ['success', 'Branch updated successfully'];
        return redirect()->route('owner.counter.index')->withNotify($notify);
    }

    public function counterStatus($id)
    {
        return Branch::changeStatus($id);
    }
}
