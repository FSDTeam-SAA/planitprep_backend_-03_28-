<div class="border">
@foreach($items as $item)
<div class="d-flex justify-content-between pb-2 p-2 border-bottom">
    <div>
        <div class="d-flex justify-content-between">
            <div>
                @if(isset($item->image) && $item->image!="")
                @php $image=asset(Storage::url('public/food-items')).'/'.$item->image @endphp
                @else
                @php $image = asset('admin/images/noimg.jpg'); @endphp
                @endif

                @php
                $s_size = round($item->serving_size);
                $protein = number_format($item->protein, 1);
                $carbs = number_format($item->total_carbohydrates, 1);
                $fat = number_format($item->total_fat, 1);
                $icon = '';
                @endphp
                @if ($item->type == 'VG')
                @php $icon = asset('admin/images/vg.png'); @endphp
                @elseif ($item->type == 'NV')
                @php $icon = asset('admin/images/nv.png'); @endphp
                @endif

                <img src="{{ $image }}" class="food-item-img">
            </div>
            <div class="ps-2 text-start">
                <div>
                    <span class="fw-medium text-start">{{$item->name}}</span>
                    <img src="{{$icon}}">
                </div>
                {{$s_size}} | {{$item->serving_unit}}
                <div class="d-flex justify-content-start">
                    <span class="macro-span bg-green-100 modal-carb rounded p-1">P {{$protein}}g</span>&nbsp;&nbsp;&nbsp;
                    <span class="macro-span bg-orange-100 modal-carb rounded p-1">C {{$carbs}}g</span>&nbsp;&nbsp;&nbsp;
                    <span class="macro-span bg-blue-100 modal-carb rounded p-1">F {{$fat}}g</span>&nbsp;&nbsp;&nbsp;
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="p-2">
            <i class="fa fa-plus addItem" data-id="{{ $item->id }}"></i>
        </div>
    </div>
</div>
@endforeach
</div>
