<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use Illuminate\Http\Request;

class CounterController extends Controller
{
    public function index()
    {
        $pageTitle = 'All Counters';
        $counters = Counter::query()
            ->with(['owner'])
            ->searchable(['name', 'city:name', 'location'])
            ->filter(['owner_id', 'status'])
            ->orderByDesc('id')
            ->paginate(getPaginate());

        return view('admin.counters.index', compact('pageTitle', 'counters'));
    }
}
