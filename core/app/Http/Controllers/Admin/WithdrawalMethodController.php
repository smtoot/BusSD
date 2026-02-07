<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalMethod;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class WithdrawalMethodController extends Controller
{
    public function methods()
    {
        $pageTitle = 'Withdrawal Methods';
        $methods = WithdrawalMethod::orderBy('name')->get();
        return view('admin.withdraw.method_list', compact('pageTitle', 'methods'));
    }

    public function create()
    {
        $pageTitle = 'New Withdrawal Method';
        return view('admin.withdraw.edit', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $this->validation($request);

        $method = new WithdrawalMethod();
        $this->saveMethod($method, $request);

        $notify[] = ['success', 'Withdrawal method added successfully'];
        return to_route('admin.withdraw.method.index')->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Withdrawal Method';
        $method = WithdrawalMethod::findOrFail($id);
        return view('admin.withdraw.edit', compact('pageTitle', 'method'));
    }

    public function update(Request $request, $id)
    {
        $this->validation($request);

        $method = WithdrawalMethod::findOrFail($id);
        $this->saveMethod($method, $request);

        $notify[] = ['success', 'Withdrawal method updated successfully'];
        return back()->withNotify($notify);
    }

    private function validation($request)
    {
        $request->validate([
            'name'           => 'required',
            'min_limit'      => 'required|numeric|gt:0',
            'max_limit'      => 'required|numeric|gt:min_limit',
            'fixed_charge'   => 'required|numeric|min:0',
            'percent_charge' => 'required|numeric|min:0',
            'delay'          => 'required',
            'image'          => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);
    }

    private function saveMethod($method, $request)
    {
        if ($request->hasFile('image')) {
            try {
                $method->image = fileUploader($request->image, getFilePath('withdrawMethod'), getFileSize('withdrawMethod'), $method->image);
            } catch (\Exception $exp) {
                throw new \Exception('Could not upload the image');
            }
        }

        $method->name           = $request->name;
        $method->min_limit      = $request->min_limit;
        $method->max_limit      = $request->max_limit;
        $method->fixed_charge   = $request->fixed_charge;
        $method->percent_charge = $request->percent_charge;
        $method->delay          = $request->delay;
        $method->description    = $request->description;
        $method->save();
    }

    public function status($id)
    {
        return WithdrawalMethod::changeStatus($id);
    }
}
