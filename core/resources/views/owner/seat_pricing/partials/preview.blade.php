@php
    $layout = $trip->fleetType->seatLayout;
    $schema = $layout->schema ?? null;
    $rows = $layout->grid_rows ?? 10;
    $cols = $layout->grid_cols ?? 5;
    $grid = $schema->layout ?? [];
    
    // Seat pricing map from service
    // Already passed as $seats from controller
@endphp

<div class="visual-seat-map">
    <div class="bus-container shadow-sm border p-4 bg-white rounded-4 mx-auto" style="width: fit-content;">
        <!-- Front -->
        <div class="bus-front text-center mb-4 border-bottom pb-3">
            <div class="steering-wheel bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="las la-dharmachakra fa-2x text-muted"></i>
            </div>
            <div class="small text-muted mt-1">@lang('Front / Driver')</div>
        </div>

        <!-- Seats Grid -->
        <div class="seat-grid" style="display: grid; grid-template-columns: repeat({{ $cols }}, 45px); gap: 10px;">
            @for($r = 0; $r < $rows; $r++)
                @for($c = 0; $c < $cols; $c++)
                    @php
                        $element = collect($grid)->where('x', $r)->where('y', $c)->first();
                    @endphp

                    @if($element)
                        @if($element->type == 'seat')
                            @php
                                $pricing = $seats[$element->label] ?? null;
                                $seatClass = 'seat-available';
                                $borderStyle = '';
                                
                                if($pricing && $pricing['premium'] > 0) {
                                    $seatClass = 'seat-premium';
                                } elseif($pricing && $pricing['discount'] > 0) {
                                    $seatClass = 'seat-discount';
                                }
                            @endphp
                            <div class="seat-item {{ $seatClass }}" 
                                 data-toggle="tooltip" 
                                 title="{{ $element->label }}: {{ gs('cur_sym') }}{{ number_format($pricing['final_price'] ?? $trip->price, 2) }} {{ $pricing && $pricing['premium'] > 0 ? '(Premium: +' . $pricing['premium'] . ')' : '' }}">
                                {{ $element->label }}
                                @if($pricing && $pricing['premium'] > 0)
                                    <div class="premium-star"><i class="las la-star"></i></div>
                                @endif
                            </div>
                        @else
                            <div class="seat-utility">
                                @if($element->type == 'driver') <i class="las la-user-tie"></i> @endif
                                @if($element->type == 'door') <i class="las la-door-open"></i> @endif
                                @if($element->type == 'toilet') <i class="las la-restroom text-primary"></i> @endif
                                @if($element->type == 'aisle') <i class="las la-shoe-prints text-muted" style="transform: rotate(90deg); font-size: 14px;"></i> @endif
                            </div>
                        @endif
                    @else
                        <div class="seat-empty"></div>
                    @endif
                @endfor
            @endfor
        </div>
    </div>
</div>

<div class="legend mt-4 d-flex justify-content-center gap-4">
    <div class="d-flex align-items-center"><div class="legend-box seat-available me-2"></div> @lang('Standard')</div>
    <div class="d-flex align-items-center"><div class="legend-box seat-premium me-2"></div> @lang('Premium')</div>
    <div class="d-flex align-items-center"><div class="legend-box seat-discount me-2"></div> @lang('Discounted')</div>
</div>

<style>
    .seat-item {
        width: 45px;
        height: 45px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 11px;
        cursor: pointer;
        position: relative;
        border: 1px solid #dee2e6;
    }
    .seat-available { background-color: #f8f9fa; color: #6c757d; }
    .seat-premium { background-color: #fff4e5; color: #d97706; border-color: #f59e0b; border-width: 2px; }
    .seat-discount { background-color: #f0fdf4; color: #166534; border-color: #22c55e; }
    
    .premium-star {
        position: absolute;
        top: -5px;
        right: -5px;
        color: #f59e0b;
        font-size: 10px;
    }
    
    .legend-box { width: 20px; height: 20px; border-radius: 4px; border: 1px solid #ddd; }
    .seat-utility { width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; color: #adb5bd; }
</style>
