@extends('supervisor.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('supervisor.scanner.verify') }}" method="POST">
                        @csrf
                        <div class="form-group text-center">
                            <label class="fw-bold">@lang('Enter Ticket Number / Scan QR')</label>
                            <input type="text" name="pnr" class="form-control form-control-lg mt-2" placeholder="@lang('Ticket ID')" autofocus required>
                        </div>
                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn--primary btn-lg w-100"><i class="las la-check-circle"></i> @lang('Verify Ticket')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
