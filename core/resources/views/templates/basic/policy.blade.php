@extends('Template::layouts.frontend')
@section('content')
    <div class="feature-section ptb-80">
        <div class="container">
            <p>@php echo $policy->data_values->details; @endphp</p>
        </div>
    </div>
@endsection
