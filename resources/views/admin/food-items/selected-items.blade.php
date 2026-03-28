<div class="d-flex justify-content-between  pb-2 p-2 border-bottom list-{{ isset($item->food_item) ? $item->food_item->id : 0 }}">

        <div class="d-flex justify-content-between">
            <div>
                @if(isset($item->food_item->image) && $item->food_item->image!="")
                @php $image=asset(Storage::url('public/food-items')).'/'.$item->food_item->image @endphp
                @else
                @php $image = asset('admin/images/noimg.jpg'); @endphp
                @endif

                @php
                $s_size = round($item->quantity);
              		$protein = 0;
                    $carbs = 0;
                    $fat = 0;
                    $icon = '';
                @endphp
                @if(isset($item->food_item))
                 	@php $protein = number_format($item->food_item->protein, 1);
                      $carbs = number_format($item->food_item->total_carbohydrates, 1);
                      $fat = number_format($item->food_item->total_fat, 1);
                	@endphp
                   @if ($item->food_item->type == 'VG')
                    @php $icon = asset('admin/images/vg.png'); @endphp
                    @elseif ($item->food_item->type == 'NV')
                    @php $icon = asset('admin/images/nv.png'); @endphp
                    @endif
                @endif


                <img src="{{ $image }}" class="food-item-img">
            </div>
            <div class="ps-2 text-start">
                <div>
                    <span class="fw-medium text-start">{{ isset($item->food_item) ? $item->food_item->name  : '' }}</span>
                    <img src="{{$icon}}">
                </div>
                {{$s_size}} | {{ isset($item->food_item) ? $item->food_item->serving_unit : '' }}
                <div class="d-flex justify-content-start">
                    <span class="macro-span bg-green-100 modal-carb rounded p-1">P {{$protein}}g</span>&nbsp;&nbsp;&nbsp;
                    <span class="macro-span bg-orange-100 modal-carb rounded p-1">C {{$carbs}}g</span>&nbsp;&nbsp;&nbsp;
                    <span class="macro-span bg-blue-100 modal-carb rounded p-1">F {{$fat}}g</span>&nbsp;&nbsp;&nbsp;
                </div>
            </div>
        </div>

        <div class="p-2 text-center" data-id="{{ isset($item->food_item) ? $item->food_item->id : 0 }}">
            <input type="text" name="qty[]" class="form-control item_qty" value="{{ $item->quantity}}" style="width:70px;padding:10px;height: 34px;">
            <label>{{ isset($item->food_item) ? $item->food_item->serving_unit : '' }}</label>
        </div>
        <div class="p-2">
            <input type="hidden" class="selected_ids" value="{{ isset($item->food_item) ? $item->food_item->id : 0 }}" name="similar_item_ids[]" />
            <i class="fa fa-minus removeItem" data-id="{{ isset($item->food_item) ? $item->food_item->id : 0 }}"></i>
        </div>

</div>
