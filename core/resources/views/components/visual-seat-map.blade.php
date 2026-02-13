<div class="seat-layout-visual-wrapper {{ $class ?? '' }}" id="{{ $id ?? 'seat-layout-' . rand(1000, 9999) }}">
    <div class="seat-grid-container" style="
        display: grid; 
        grid-template-columns: repeat({{ $layout->grid_cols }}, 45px); 
        grid-template-rows: repeat({{ $layout->grid_rows }}, 45px);
        gap: 10px;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        width: fit-content;
        margin: 0 auto;
    ">
        @php
            $schema = json_decode(json_encode($layout->schema), true);
            $mapping = collect($schema['layout'] ?? []);
        @endphp

        @for($r = 0; $r < $layout->grid_rows; $r++)
            @for($c = 0; $c < $layout->grid_cols; $c++)
                @php
                    $item = $mapping->where('x', $r)->where('y', $c)->first();
                @endphp

                <div class="seat-cell @if($item) active-element type-{{ $item['type'] }} @endif" 
                     style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border: 1px solid #eee; border-radius: 4px; position: relative;">
                    @if($item)
                        @php
                            $icon = 'la-chair';
                            if($item['type'] == 'driver') $icon = 'la-user-tie';
                            if($item['type'] == 'door') $icon = 'la-door-open';
                            if($item['type'] == 'toilet') $icon = 'la-restroom';
                            if($item['type'] == 'aisle') $icon = 'la-vector-square';
                        @endphp
                        <i class="las {{ $icon }}" style="font-size: 20px;"></i>
                        @if(!empty($item['label']))
                            <span style="position: absolute; font-size: 8px; bottom: 2px; width: 100%; text-align: center; font-weight: bold;">
                                {{ $item['label'] }}
                            </span>
                        @endif
                    @endif
                </div>
            @endfor
        @endfor
    </div>
</div>

<style>
    .active-element.type-seat { background: #e8f5e9; color: #2e7d32; border-color: #a5d6a7 !important; }
    .active-element.type-driver { background: #fff3e0; color: #ef6c00; border-color: #ffccbc !important; }
    .active-element.type-door { background: #f3e5f5; color: #7b1fa2; border-color: #e1bee7 !important; }
    .active-element.type-toilet { background: #e3f2fd; color: #1565c0; border-color: #bbdefb !important; }
    .active-element.type-aisle { background: #fafafa; color: #bdbdbd; border-color: #eeeeee !important; }
</style>
