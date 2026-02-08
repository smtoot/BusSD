@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Layout Name')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($seatLayouts as $layout)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $layout->name }}</span>
                                        </td>
                                        <td>
                                            @if($layout->owner_id == 0)
                                                <span class="badge badge--primary">@lang('Global Template')</span>
                                            @else
                                                <span class="badge badge--info">{{ @$layout->owner->username }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php echo $layout->statusBadge @endphp
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn-sm btn-outline--primary editBtn" 
                                                    data-id="{{ $layout->id }}"
                                                    data-name="{{ $layout->name }}"
                                                    data-schema="{{ json_encode($layout->schema) }}">
                                                    <i class="la la-pencil"></i> @lang('Edit Builder')
                                                </button>
                                                @if ($layout->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-question="@lang('Are you sure to enable this layout?')"
                                                        data-action="{{ route('admin.fleet.seat_layouts.status', $layout->id) }}">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-question="@lang('Are you sure to disable this layout?')"
                                                        data-action="{{ route('admin.fleet.seat_layouts.status', $layout->id) }}">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($seatLayouts->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($seatLayouts) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Builder Modal -->
    <div id="builderModal" class="modal fade" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Seat Layout Visual Builder')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.fleet.seat_layouts.store') }}">
                    @csrf
                    <input type="hidden" name="schema" id="layoutSchema">
                    <div class="modal-body">
                        <div class="row">
                            <!-- Sidebar Tools -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Template Name')</label>
                                    <input type="text" name="name" class="form-control" placeholder="@lang('e.g. Luxury 2x2')" required>
                                </div>
                                <hr>
                                <label class="mb-2 fw-bold">@lang('Element Palette')</label>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <div class="pallete-item" data-type="seat" title="Standard Seat">
                                        <i class="las la-chair"></i>
                                    </div>
                                    <div class="pallete-item" data-type="driver" title="Driver Area">
                                        <i class="las la-user-tie"></i>
                                    </div>
                                    <div class="pallete-item" data-type="door" title="Entry/Exit">
                                        <i class="las la-door-open"></i>
                                    </div>
                                    <div class="pallete-item" data-type="toilet" title="Restroom">
                                        <i class="las la-restroom"></i>
                                    </div>
                                    <div class="pallete-item" data-type="aisle" title="Clear Aisle">
                                        <i class="las la-vector-square text-muted"></i>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label>@lang('Grid Size')</label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="number" id="gridRows" class="form-control" placeholder="Rows" value="10">
                                        </div>
                                        <div class="col-6">
                                            <input type="number" id="gridCols" class="form-control" placeholder="Cols" value="5">
                                        </div>
                                    </div>
                                    <button type="button" id="resizeGrid" class="btn btn-sm btn--dark w-100 mt-2">@lang('Update Grid')</button>
                                </div>
                                <hr>
                                <div class="form-group" id="seatLabelGroup" style="display:none;">
                                    <label>@lang('Seat Label')</label>
                                    <div class="input-group">
                                        <input type="text" id="seatLabelInput" class="form-control" 
                                               placeholder="@lang('e.g. A1, B2')" maxlength="10">
                                        <button type="button" class="btn btn--primary" id="addSeatBtn">
                                            <i class="las la-plus"></i> @lang('Add')
                                        </button>
                                    </div>
                                    <small class="text-muted">@lang('Enter a unique label for this seat')</small>
                                </div>
                            </div>
                            <!-- Canvas -->
                            <div class="col-md-9 border-start">
                                <div id="canvas-container" class="layout-canvas-outer">
                                    <div id="seat-grid" class="seat-grid-canvas">
                                        <!-- Grid Rendered via JS -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Save Template')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />

    <style>
        .layout-canvas-outer {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            min-height: 500px;
            overflow: auto;
            display: flex;
            justify-content: center;
        }
        .seat-grid-canvas {
            display: grid;
            gap: 10px;
            background: white;
            padding: 15px;
            border: 2px dashed #ddd;
            width: fit-content;
        }
        .grid-cell {
            width: 45px;
            height: 45px;
            border: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.2s;
            position: relative;
        }
        .grid-cell:hover {
            border-color: #ef5050;
            background: #fff5f5;
        }
        .grid-cell.active-element {
            background: #ef5050;
            color: white;
            border-color: #ef5050;
        }
        .grid-cell i {
            font-size: 20px;
        }
        .grid-cell .seat-label {
            position: absolute;
            font-size: 8px;
            bottom: 2px;
            width: 100%;
            text-align: center;
        }
        .pallete-item {
            width: 50px;
            height: 50px;
            border: 2px solid #ddd;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 24px;
            transition: all 0.3s;
        }
        .pallete-item:hover, .pallete-item.selected {
            border-color: #ef5050;
            color: #ef5050;
        }
    </style>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
        <x-search-form placeholder="Layout Name" />
        <button class="btn btn-sm btn-outline--primary addBtn">
            <i class="las la-plus"></i> @lang('Create Template')
        </button>
    </div>
@endpush

@push('script')
<script>
(function($) {
    'use strict';
    
    let currentType = 'seat';
    let layoutData = [];
    let rows = 10;
    let cols = 5;

    const modal = $('#builderModal');
    const grid = $('#seat-grid');

    $('.addBtn').on('click', function() {
        modal.find('form').attr('action', "{{ route('admin.fleet.seat_layouts.store') }}");
        modal.find('[name=name]').val('');
        rows = 10; cols = 5;
        layoutData = [];
        renderGrid();
        modal.modal('show');
    });

    $('.editBtn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const schema = $(this).data('schema');
        
        modal.find('form').attr('action', `{{ url('admin/fleet/seat-layouts/store') }}/${id}`);
        modal.find('[name=name]').val(name);
        
        if(schema && schema.meta && schema.meta.grid) {
            rows = schema.meta.grid.rows || 10;
            cols = schema.meta.grid.cols || 5;
            layoutData = schema.layout || [];
            $('#gridRows').val(rows);
            $('#gridCols').val(cols);
        } else {
            rows = 10;
            cols = 5;
            layoutData = [];
            $('#gridRows').val(rows);
            $('#gridCols').val(cols);
        }
        
        renderGrid();
        modal.modal('show');
    });

    $('.pallete-item').on('click', function() {
        $('.pallete-item').removeClass('selected');
        $(this).addClass('selected');
        currentType = $(this).data('type');
    });

    $('#resizeGrid').on('click', function() {
        rows = parseInt($('#gridRows').val()) || 10;
        cols = parseInt($('#gridCols').val()) || 5;
        
        // Enforce limits
        rows = Math.min(Math.max(rows, 1), 50);
        cols = Math.min(Math.max(cols, 1), 20);
        
        $('#gridRows').val(rows);
        $('#gridCols').val(cols);
        
        renderGrid();
    });

    $('#gridRows, #gridCols').on('input', function() {
        let val = parseInt($(this).val());
        const max = $(this).attr('id') === 'gridRows' ? 50 : 20;
        const min = 1;
        
        if (val > max) {
            $(this).val(max);
        } else if (val < min || isNaN(val)) {
            // Keep current value or set min
        }
    });

    function renderGrid() {
        grid.css({
            'grid-template-columns': `repeat(${cols}, 45px)`,
            'grid-template-rows': `repeat(${rows}, 45px)`
        });
        
        grid.empty();
        
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                const cellData = layoutData.find(item => item.x === r && item.y === c);
                const cell = $(`<div class="grid-cell" data-x="${r}" data-y="${c}"></div>`);
                
                if (cellData) {
                    applyElement(cell, cellData.type, cellData.label);
                }
                
                cell.on('click', function() {
                    const x = $(this).data('x');
                    const y = $(this).data('y');
                    
                    if ($(this).hasClass('active-element')) {
                        // Remove element
                        $(this).removeClass('active-element').empty();
                        layoutData = layoutData.filter(item => !(item.x === x && item.y === y));
                        updateSchemaField();
                    } else {
                        // Add element
                        if(currentType === 'seat') {
                            pendingCell = $(this);
                            $('#seatLabelGroup').show();
                            $('#seatLabelInput').val('').focus();
                        } else {
                            // Non-seat elements don't need labels
                            applyElement($(this), currentType, '');
                            layoutData.push({x, y, type: currentType, label: ''});
                            updateSchemaField();
                        }
                    }
                });
                
                grid.append(cell);
            }
        }
        updateSchemaField();
    }

    function applyElement(el, type, label) {
        el.addClass('active-element').empty();
        let icon = 'la-chair';
        if(type === 'driver') icon = 'la-user-tie';
        if(type === 'door') icon = 'la-door-open';
        if(type === 'toilet') icon = 'la-restroom';
        if(type === 'aisle') icon = 'la-vector-square';
        
        el.append(`<i class="las ${icon}"></i>`);
        if(label) {
            el.append(`<span class="seat-label">${label}</span>`);
        }
    }

    let pendingCell = null;

    $('#addSeatBtn').on('click', function() {
        const label = $('#seatLabelInput').val().trim();
        
        if (!label) {
            alert('Please enter a seat label');
            return;
        }
        
        // Check for duplicate labels
        const existingLabel = layoutData.find(item => item.label === label);
        if (existingLabel) {
            alert('Seat label already exists!');
            return;
        }
        
        if (pendingCell) {
            applyElement(pendingCell, currentType, label);
            const x = pendingCell.data('x');
            const y = pendingCell.data('y');
            layoutData.push({x, y, type: currentType, label: label});
            updateSchemaField();
            
            $('#seatLabelGroup').hide();
            pendingCell = null;
        }
    });

    $('#seatLabelInput').on('keypress', function(e) {
        if (e.which === 13) {
            $('#addSeatBtn').click();
        }
    });

    function updateSchemaField() {
        const fullSchema = {
            meta: {
                version: "1.0",
                decks: 1,
                grid: { rows, cols }
            },
            layout: layoutData
        };
        $('#layoutSchema').val(JSON.stringify(fullSchema));
    }

})(jQuery);
</script>
@endpush
