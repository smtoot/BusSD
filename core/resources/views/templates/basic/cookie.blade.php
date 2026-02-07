@extends('Template::layouts.frontend')
@php
    $breadcrumb = @getContent('breadcrumb.content', true)->data_values;
@endphp
@section('content')
    <div class="feature-section ptb-80">
        <div class="container">
            <p>@php echo $cookie->data_values->description; @endphp</p>
        </div>
    </div>
@endsection
