@php
    $layout = $trip->fleetType->seatLayout;
    $schema = $layout->schema ?? null;
    $rows = $layout->grid_rows ?? 10;
    $cols = $layout->grid_cols ?? 5;
    $grid = $schema->layout ?? [];
    
    // Map of booked seats for quick lookup
    $bookedSeats = [];
    foreach($passengers as $p) {
        $bookedSeats[$p->seat_no] = $p;
    }
@endphp

<div class="visual-seat-map">
    <div class="bus-container shadow-sm border p-4 bg-white rounded-4 mx-auto" style="width: fit-content;">
        <!-- Front of Bus -->
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
                                $booking = $bookedSeats[$element->label] ?? null;
                                $seatClass = 'seat-available';
                                $seatContent = $element->label;
                                
                                if($booking) {
                                    if($booking->source == 'App') {
                                        $seatClass = 'seat-booked-app';
                                    } else {
                                        $seatClass = 'seat-booked-manual';
                                        $seatContent = 'M';
                                    }
                                }
                            @endphp
                            <div class="seat-item {{ $seatClass }}" title="{{ $booking ? $booking->name . ' (' . $element->label . ')' : __('Available: ') . $element->label }}">
                                {{ $seatContent }}
                                @if($booking && $booking->is_boarded)
                                    <div class="boarded-indicator"><i class="las la-check"></i></div>
                                @endif
                            </div>
                        @else
                            <div class="seat-utility" title="{{ ucfirst($element->type) }}">
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
        
        <!-- Back of Bus -->
        <div class="bus-back text-center mt-4 border-top pt-3">
            <div class="small text-muted">@lang('Rear of Bus')</div>
        </div>
    </div>
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
        font-size: 12px;
        cursor: default;
        position: relative;
        transition: transform 0.2s;
        border: 1px solid #dee2e6;
    }
    .seat-item:hover {
        transform: scale(1.1);
        z-index: 10;
    }
    .seat-available { background-color: #f8f9fa; color: #6c757d; border-style: dashed; }
    .seat-booked-app { background-color: #1F78D1; color: white; border-color: #1F78D1; }
    .seat-booked-manual { background-color: #343a40; color: white; border-color: #343a40; }
    
    .seat-utility {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #adb5bd;
    }
    .seat-empty { width: 45px; height: 45px; }
    
    .boarded-indicator {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #00A34D;
        color: white;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 8px;
        border: 2px solid white;
    }
    
    .bus-container {
        border-radius: 40px 40px 15px 15px;
        border-width: 3px !important;
    }
</style>
