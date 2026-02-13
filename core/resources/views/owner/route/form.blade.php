@extends('owner.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg--primary">
                    <h5 class="card-title text-white mb-0">{{ $pageTitle }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('owner.route.store', @$route->id) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <div class="bg-light p-3 border rounded">
                                    <h6 class="mb-2 text--primary">Step 1: Select Approved Corridor</h6>
                                    <p class="small text-muted mb-3">You can only create variations for city pairs approved by the Admin.</p>
                                    <div class="form-group mb-0">
                                        <label>@lang('Approved Corridor') <span class="text-danger">*</span></label>
                                        <select id="corridorSelector" class="form-control select2" required>
                                            <option value="">@lang('Select an approved city pair')</option>
                                            @foreach ($globalRoutes as $global)
                                                <option value="{{ $global->starting_city_id }}-{{ $global->destination_city_id }}" 
                                                    data-start-id="{{ $global->starting_city_id }}"
                                                    data-end-id="{{ $global->destination_city_id }}"
                                                    {{ @$route->starting_city_id == $global->starting_city_id && @$route->destination_city_id == $global->destination_city_id ? 'selected' : '' }}>
                                                    {{ $global->startingPoint->name }} <i class="las la-arrow-right"></i> {{ $global->destinationPoint->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="starting_city_id" id="starting_city_id_hidden" value="{{ @$route->starting_city_id }}">
                            <input type="hidden" name="destination_city_id" id="destination_city_id_hidden" value="{{ @$route->destination_city_id }}">

                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label>@lang('Variation Name') <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required value="{{ @$route->name }}" placeholder="@lang('Example: Midnight Express, VIP Non-Stop')" />
                                </div>
                            </div>
                            <div class="col-md-3 mt-2">
                                <div class="form-group">
                                    <label>@lang('Distance (km)')</label>
                                    <input type="text" name="distance" class="form-control" value="{{ @$route->distance }}" placeholder="@lang('Example: 150')" />
                                </div>
                            </div>
                            <div class="col-md-3 mt-2">
                                <div class="form-group">
                                    <label>@lang('Travel Time (hrs)')</label>
                                    <input type="text" name="time" class="form-control" value="{{ @$route->time }}" placeholder="@lang('Example: 5.5')" />
                                </div>
                            </div>

                            <div class="col-md-12 mt-4">
                                <div class="card border--primary shadow-sm">
                                    <div class="card-header bg--primary d-flex justify-content-between align-items-center">
                                        <h6 class="text-white mb-0">Step 2: Configure Stop Points & Order</h6>
                                        <span class="badge bg-white text--primary">Drag items to reorder</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="row align-items-end mb-4">
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
                                                <button type="button" id="addStoppageBtn" class="btn btn--success w-100 mt-md-0 mt-3">
                                                    <i class="las la-plus"></i> @lang('Add to Route')
                                                </button>
                                            </div>
                                        </div>

                                        <ul id="stoppageDisplayList" class="list-group">
                                            @if(isset($selectedStoppages) && $selectedStoppages->count() > 0)
                                                @foreach($selectedStoppages as $stop)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center draggable-item" draggable="true" data-id="{{ $stop->id }}">
                                                        <div class="d-flex align-items-center">
                                                            <i class="las la-bars me-3 drag-handle" style="cursor: move;"></i>
                                                            <span>{{ $stop->name }}</span>
                                                            <input type="hidden" name="stoppages[]" value="{{ $stop->id }}">
                                                        </div>
                                                        <button type="button" class="btn btn-sm text--danger remove-stop"><i class="las la-times"></i></button>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                        <div id="emptyListWarning" class="text-center py-4 text-muted {{ isset($selectedStoppages) && $selectedStoppages->count() > 0 ? 'd-none' : '' }}">
                                            <i class="las la-map-marker-alt la-3x mb-2"></i>
                                            <p>@lang('No stops added yet. Your origin and destination will be added automatically.')</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn--primary btn-lg px-5">@lang('Save Route Variation')</button>
                            <a href="{{ route('owner.route.index') }}" class="btn btn-outline--dark btn-lg px-5">@lang('Cancel')</a>
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
        transition: transform 0.2s, background-color 0.2s;
        border-left: 4px solid #ef5050;
        margin-bottom: 5px;
        border-radius: 6px !important;
    }
    .draggable-item.dragging {
        opacity: 0.5;
        background-color: #f8f9fa;
        transform: scale(0.98);
    }
    .draggable-item:hover {
        background-color: #fff8f8;
    }
    .drag-handle {
        font-size: 1.25rem;
        color: #adb5bd;
    }
    .draggable-item .remove-stop {
        opacity: 0.5;
        transition: opacity 0.2s;
    }
    .draggable-item:hover .remove-stop {
        opacity: 1;
    }
</style>
@endpush

@push('script')
<script>
    (function($){
        "use strict";

        // 1. Corridor Selection Logic
        $('#corridorSelector').on('change', function() {
            const selected = $(this).find(':selected');
            const startId = selected.data('start-id');
            const endId = selected.data('end-id');
            
            $('#starting_city_id_hidden').val(startId);
            $('#destination_city_id_hidden').val(endId);

            // Auto-fill variation name if empty
            const nameInput = $('input[name="name"]');
            if (!nameInput.val()) {
                nameInput.val(selected.text().trim() + ' Variation');
            }
        });

        // 2. Stoppage Management
        const list = document.getElementById('stoppageDisplayList');
        
        $('#addStoppageBtn').on('click', function() {
            const picker = $('#stoppagePicker');
            const id = picker.val();
            const text = picker.find(':selected').text();

            if (!id) return;

            // Check if already in list
            if ($(`#stoppageDisplayList input[value="${id}"]`).length > 0) {
                iziToast.error({message: "@lang('This stop is already added')", position: "topRight"});
                return;
            }

            const html = `
                <li class="list-group-item d-flex justify-content-between align-items-center draggable-item" draggable="true" data-id="${id}">
                    <div class="d-flex align-items-center">
                        <i class="las la-bars me-3 drag-handle" style="cursor: move;"></i>
                        <span>${text}</span>
                        <input type="hidden" name="stoppages[]" value="${id}">
                    </div>
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

        // 3. Simple Vanilla JS Drag & Drop
        function initDraggable() {
            const draggables = document.querySelectorAll('.draggable-item');
            
            draggables.forEach(draggable => {
                draggable.addEventListener('dragstart', () => {
                    draggable.classList.add('dragging');
                });

                draggable.addEventListener('dragend', () => {
                    draggable.classList.remove('dragging');
                });
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
