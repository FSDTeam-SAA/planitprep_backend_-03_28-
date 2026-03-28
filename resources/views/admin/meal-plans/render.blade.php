<div class="tab-content w-100" id="v-pills-tabContent">
    @for ($i = 1; $i < 8; $i++)
        <div class="tab-pane fade {{ $i == 1 ? 'show active' : '' }}" id="v-pills-{{$i}}" role="tabpanel" aria-labelledby="v-pills-{{$i}}-tab">
            <div class="row d-flex justify-content-start">
                @if (count($meals))
                    @php
                        $removeBtn = asset('admin/images/close.png');
                    @endphp

                    @foreach ($meals as $meal)
                        <div class="col-md-12 col-xl-4 pt-0">
                            <div class="meals-div card border border-warning">
                                <div class="d-flex meal-heading-outer justify-content-between align-items-center border-bottom">
                                    <div class="d-flex align-items-baseline">
                                        <div>
                                            <strong class="f-16 align-content-center px-3 py-1 fw-medium">{{ $meal->name }}</strong>
                                        </div>
                                        <div id="spinner{{$meal['id']}}" class="text-center d-none">
                                            <div class="spinner-border spinner-border-sm" role="status">
                                              <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div role="button" class="plus-item pe-3" data-bs-toggle="modal" data-bs-target="#addModal" title="Add Item" onclick="setMealAndMealPlanId({{ $meal->id }}, {{ $mealPlanId }})">+</div>
                                </div>
                                @if (count($foodItems[$i]) > 0)
                                    <div class="meals-div table-responsive">
                                        <table class="table table-hover">
                                            <tbody>
                                                @foreach ($foodItems[$i][$meal->id] as $foodItem)
                                                    <tr data-meal-id="{{ $meal->id }}">
                                                        <td class="d-flex justify-content-between align-items-center border-0 food-item-td">
                                                            <div class="flex1">
                                                                <div class="d-flex justify-content-between">
                                                                    <div>
                                                                        <div class="d-flex justify-content-between">
                                                                            <div>
                                                                                <img src="{{ $foodItem['image'] }}" class="img-fluid food-item-img">
                                                                            </div>
                                                                            <div class="ps-2 text-start">
                                                                                <span class="fw-medium">{{ $foodItem['name'] }} ({{(int)$foodItem['quantity']}})</span>
                                                                                <br>
                                                                                {{ $foodItem['serving_size'] }} {{ $foodItem['serving_unit'] }}
                                                                                <div class="d-flex justify-content-start">
                                                                                    <span class="macro-span rounded modal-carb bg-green-100">P {{ number_format($foodItem['protein'], 1) }}g</span>&nbsp;&nbsp;&nbsp;
                                                                                    <span class="macro-span rounded modal-carb bg-orange-100">C {{ number_format($foodItem['total_carbohydrates'], 1) }}g</span>&nbsp;&nbsp;&nbsp;
                                                                                    <span class="macro-span rounded modal-carb bg-blue-100">F {{ number_format($foodItem['total_fat'], 1) }}g</span>&nbsp;&nbsp;&nbsp;
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <div>
                                                                            <img class="removeItemBtn pe-2" src="{{ $removeBtn }}" onclick="setRemoveItem({{ $meal->id }}, {{ $mealPlanId }}, {{ $i }}, {{ $foodItem['id'] }})" data-bs-toggle="modal" data-bs-target="#removeModal">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    @endfor
</div>
