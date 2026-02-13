@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">@lang('Edit Route')</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.routes.update', $route->id) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Route Name') <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required value="{{ $route->name }}" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Starting City') <span class="text-danger">*</span></label>
                                    <select name="starting_city_id" id="starting_city_id" class="form-control select2" required>
                                        <option value="">@lang('Select One')</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}" {{ $route->starting_city_id == $city->id ? 'selected' : '' }}>{{ __($city->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Destination City') <span class="text-danger">*</span></label>
                                    <select name="destination_city_id" id="destination_city_id" class="form-control select2" required>
                                        <option value="">@lang('Select One')</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}" {{ $route->destination_city_id == $city->id ? 'selected' : '' }}>{{ __($city->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Distance (km)')</label>
                                    <input type="text" name="distance" class="form-control" value="{{ $route->distance }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Time (hours)')</label>
                                    <input type="text" name="time" class="form-control" value="{{ $route->time }}" />
                                </div>
                            </div>

                            <div class="col-md-12 mt-4">
                                <div class="card border--primary">
                                    <div class="card-header bg--primary d-flex justify-content-between align-items-center">
                                        <h6 class="text-white mb-0">@lang('Route Stoppages & Order')</h6>
                                        <span class="badge bg-white text--primary">@lang('Drag to reorder')</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="row align-items-end mb-3">
                                            <div class="col-md-9">
                                                <div class="form-group mb-0">
                                                    <label>@lang('Add a Stop City')</label>
                                                    <select id="stoppagePicker" class="form-control select2">
                                                        <option value="">@lang('Select a city')</option>
                                                        @foreach ($cities as $city)
                                                            <option value="{{ $city->id }}">{{ __($city->name) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="button" id="addStoppageBtn" class="btn btn--success w-100">
                                                    <i class="las la-plus"></i> @lang('Add Stop')
                                                </button>
                                            </div>
                                        </div>

                                        <ul id="stoppageDisplayList" class="list-group">
                                            {{-- Persistent items --}}
                                            @foreach($stoppages as $stop)
                                                <li class="list-group-item d-flex justify-content-between align-items-center draggable-item" draggable="true">
                                                    <span><i class="las la-bars me-2"></i> {{ $stop->name }}</span>
                                                    <input type="hidden" name="stoppages[]" value="{{ $stop->id }}">
                                                    <button type="button" class="btn btn-sm text--danger remove-stop"><i class="las la-times"></i></button>
                                                </li>
                                            @endforeach
                                        </ul>
                                        <div id="emptyListWarning" class="text-center py-4 text-muted {{ count($stoppages) > 0 ? 'd-none' : '' }}">
                                            <i class="las la-info-circle la-2x mb-2"></i>
                                            <p>@lang('Add intermediate stops. Origin and Destination are managed automatically.')</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn--primary btn-lg">@lang('Update Route')</button>
                            <a href="{{ route('admin.routes.index') }}" class="btn btn-outline--secondary btn-lg">@lang('Cancel')</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .draggable-item {
        cursor: move;
        transition: all 0.2s;
        border-left: 5px solid #ef5050;
        margin-bottom: 5px;
    }
    .draggable-item.dragging {
        opacity: 0.5;
        background: #f4f4f4;
    }
</style>
@endpush

@push('script')
<script>
    (function($){
        "use strict";

        const list = document.getElementById('stoppageDisplayList');
        
        $('#addStoppageBtn').on('click', function() {
            const picker = $('#stoppagePicker');
            const id = picker.val();
            const text = picker.find(':selected').text();

            if (!id) return;

            if ($(`#stoppageDisplayList input[value="${id}"]`).length > 0) {
                iziToast.error({message: "@lang('Already in list')"});
                return;
            }

            const html = `
                <li class="list-group-item d-flex justify-content-between align-items-center draggable-item" draggable="true">
                    <span><i class="las la-bars me-2"></i> ${text}</span>
                    <input type="hidden" name="stoppages[]" value="${id}">
                    <button type="button" class="btn btn-sm text--danger remove-stop"><i class="las la-times"></i></button>
                </li>
            `;

            $('#stoppageDisplayList').append(html);
            $('#emptyListWarning').addClass('d-none');
            picker.val('').trigger('change');
            initDraggable();
        });

        $(document).on('click', '.remove-stop', function() {
            $(this).closest('li').remove();
            if ($('#stoppageDisplayList li').length === 0) {
                $('#emptyListWarning').removeClass('d-none');
            }
        });

        function initDraggable() {
            const draggables = document.querySelectorAll('.draggable-item');
            
            draggables.forEach(draggable => {
                draggable.addEventListener('dragstart', () => draggable.classList.add('dragging'));
                draggable.addEventListener('dragend', () => draggable.classList.remove('dragging'));
            });

            list.addEventListener('dragover', e => {
                e.preventDefault();
                const afterElement = getDragAfterElement(list, e.clientY);
                const draggable = document.querySelector('.dragging');
                if (afterElement == null) {
                    list.appendChild(draggable);
                } else {
                    list.insertBefore(draggable, afterElement);
                }
            });
        }

        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('.draggable-item:not(.dragging)')];
            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child };
                } else {
                    return closest;
                }
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }

        initDraggable();
    })(jQuery);
</script>
@endpush
