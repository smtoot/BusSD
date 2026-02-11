@extends('owner.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-12 col-lg-8 mb-30">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('owner.settings.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group ">
                            <label> @lang('Company Name')</label>
                            <input class="form-control" type="text" name="company_name" required
                                value="{{ @$owner->general_settings->company_name }}">
                        </div>
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
