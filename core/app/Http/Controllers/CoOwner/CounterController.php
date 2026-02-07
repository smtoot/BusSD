<?php

namespace App\Http\Controllers\CoOwner;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use App\Models\CounterManager;
use App\Traits\Crud;
use Illuminate\Http\Request;

class CounterController extends Controller
{
    use Crud;

    protected $model = CounterManager::class;
    protected $view = 'co_owner.counter-manager';
    protected $title = 'Counter Manager';
    protected $fileInfo = 'counter_manager';
    protected $tableName = 'counter_managers';
    protected $guard = 'co-owner';



    public function __construct()
    {
        $this->owner = authUser('co-owner')->owner;
    }

    public function counter()
    {
        $pageTitle = 'All Counters';
        $owner = authUser('co-owner')->owner;
        $counterManagers = $this->model::active()->where('owner_id', $owner->id)->get();
        $counters = Counter::where('owner_id', $owner->id)
            ->with('counterManager')
            ->searchable(['name', 'mobile', 'city'])
            ->orderByDesc('id')
            ->paginate(getPaginate());
        return view('co_owner.counter.index', compact('pageTitle', 'counters', 'counterManagers', 'owner'));
    }

    public function counterStore(Request $request, $id = 0)
    {
        $request->validate([
            'name'              => 'required|string|max:40',
            'mobile'            => 'required|string|max:40',
            'city'              => 'required|string|max:40',
            'location'          => 'nullable|string|max:255',
            'counter_manager'   => 'nullable|integer',
        ]);

        $owner = authUser();
        $counterManager = CounterManager::where('owner_id', $owner->id)->findOrFail($request->counter_manager);
        if ($counterManager->counter) {
            $counter = $counterManager->counter;
            $counter->counter_manager_id = 0;
            $counter->save();

            $notify[] = ['info', 'Counter manager remove from previous counter.'];
        }

        if ($id) {
            $counter = Counter::findOrFail($id);
            $message = 'Counter updated successfully';
        } else {
            $counter = new Counter();
            $counter->owner_id = $owner->id;

            $message = 'Counter created successfully';
        }

        $counter->counter_manager_id = $request->counter_manager;
        $counter->name               = $request->name;
        $counter->mobile             = $request->mobile;
        $counter->city               = $request->city;
        $counter->location           = $request->location;
        $counter->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function counterStatus($id)
    {
        return Counter::changeStatus($id);
    }
}
