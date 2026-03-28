@extends('admin.layouts.admin')
@section('content')
    <style>
        .meals-div {
            height: 450px;
            overflow-y: auto;
        }

        .meal-heading-outer {
            background-color: bisque;
            color: black;
        }

        .meals-div::-webkit-scrollbar {
            width: 1px;
        }

        .meals-div::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .meals-div::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        .table-responsive {
            max-height: 420px;
            overflow-y: auto;
            position: relative;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead {
            position: sticky;
            top: 0;
            background-color: white;
            z-index: 10;
        }

        .table th, .table td {
            padding: 8px;
            text-align: left;
            border: 1px solid #dee2e6;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .food-item-img {
            width: 63px;
        }

        .qty-input {
            width: 80px;
            height: 30px;
        }

        .addModalCloseBtn {
            font-size: 18px;
            color: white;
        }

        .modal-carb {
            height: 20px;
            padding-right: 2px;
            padding-right: 2px;
            padding-left: 5px;
            padding-right: 5px;
            font-size: 13px;
        }

        .modal-item-add-btn {
            height: 30px;
            width: 87px;
        }

        .food-item-name {
            color: black;
            font-weight: 500;
        }

        .food-item-serving {
            font-size: 12px;
        }

        .food-item-qty {
            width: 80px;
        }

        .food-item-td {
            padding-top: 11px !important;
            padding-bottom: 4px !important;
        }

        .plus-item {
            font-size: 30px;
            color: rgb(8, 100, 8);
            cursor: pointer;
        }

        .plus-item:hover {
            color: rgb(28, 129, 28);
        }

        #search-result {
            height: 400px;
            overflow-y: scroll;
        }

        #search-result::-webkit-scrollbar {
            width: 1px;
        }

        .removeItemBtn {
            cursor: pointer;
            opacity: 0.5;
            padding: 5px;
        }

        .removeItemBtn:hover {
            opacity: 1;
        }

        .flex1 {
            flex: 1;
        }

        .macro-span {
            text-align: center;
            font-size: 12px;
            width: fit-content;
            padding-top: 2px;
        }
    </style>
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.meal-plans') }}">Meal Plans</a></li>
                                <li class="breadcrumb-item" aria-current="page">{{ $title }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row pt-0">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header pb-3">
                            <h5>{{ $title }}</h5>
                        </div>

                        <div class="card-body pt-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="nav flex-row nav-pills mb-3" id="v-pills-tab" role="tablist">
                                        <button class="mb-2 nav-link bg-gray-600 active" id="v-pills-1-tab" data-bs-toggle="pill" data-bs-target="#v-pills-1" type="button" role="tab" aria-controls="v-pills-1" aria-selected="true" onclick="changeDay(1)">Monday</button>
                                        <button class="mb-2 nav-link bg-gray-600 mx-1" id="v-pills-2-tab" data-bs-toggle="pill" data-bs-target="#v-pills-2" type="button" role="tab" aria-controls="v-pills-2" aria-selected="false" onclick="changeDay(2)">Tuesday</button>
                                        <button class="mb-2 nav-link bg-gray-600 mx-1" id="v-pills-3-tab" data-bs-toggle="pill" data-bs-target="#v-pills-3" type="button" role="tab" aria-controls="v-pills-3" aria-selected="false" onclick="changeDay(3)">Wednesday</button>
                                        <button class="mb-2 nav-link bg-gray-600 mx-1" id="v-pills-4-tab" data-bs-toggle="pill" data-bs-target="#v-pills-4" type="button" role="tab" aria-controls="v-pills-4" aria-selected="false" onclick="changeDay(4)">Thursday</button>
                                        <button class="mb-2 nav-link bg-gray-600 mx-1" id="v-pills-5-tab" data-bs-toggle="pill" data-bs-target="#v-pills-5" type="button" role="tab" aria-controls="v-pills-5" aria-selected="false" onclick="changeDay(5)">Friday</button>
                                        <button class="mb-2 nav-link bg-gray-600 mx-1" id="v-pills-6-tab" data-bs-toggle="pill" data-bs-target="#v-pills-6" type="button" role="tab" aria-controls="v-pills-6" aria-selected="false" onclick="changeDay(6)">Saturday</button>
                                        <button class="mb-2 nav-link bg-gray-600 mx-1" id="v-pills-7-tab" data-bs-toggle="pill" data-bs-target="#v-pills-7" type="button" role="tab" aria-controls="v-pills-7" aria-selected="false" onclick="changeDay(7)">Sunday</button>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div id="meals" class="row">
                                        <div class="col-md-2">
                                            <div>
                                                <ul class="list-group">
                                                    <li class="list-group-item">
                                                        <div>
                                                            <strong class="text-red-400">Total Calories</strong>
                                                            <br>
                                                            <strong id="total-calories">0</strong> Kcal
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div>
                                                            <strong  class="text-orange-600">Total Carbohydrates</strong>
                                                            <br>
                                                            <strong id="total-carbs">0</strong> g
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div>
                                                            <strong class="text-blue-600">Total Fat</strong>
                                                            <br>
                                                            <strong id="total-fats">0</strong> g
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div>
                                                            <strong class="text-gray-600">Total Fiber</strong>
                                                            <br>
                                                            <strong id="total-fibers">0</strong> g
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div>
                                                            <strong class="text-green-700">Total Protein</strong>
                                                            <br>
                                                            <strong id="total-protein">0</strong> g
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-10">
                                            <div id="meal-plan-html" class="tab-content w-100" id="v-pills-tabContent">
                                                <div class="d-flex align-items-center">
                                                    <div class="spinner-border spinner-border-sm text-secondary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    <div>
                                                        &nbsp;&nbsp;Fetching details...
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="removeModal" tabindex="-1" aria-labelledby="removeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeModalLabel">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure to remove this item?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <button id="confirmYesBtn" type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="removeItem()">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="d-flex justify-content-between align-items-baseline px-3 py-2 fw-medium bg-primary text-white">
                        <h6 class="text-white fw-normal">Add Items</h6>
                        <div>
                            <span role="button" class="addModalCloseBtn" data-bs-dismiss="modal">&times;</span>
                        </div>
                    </div>
                    <div>
                        <input id="search-input" type="search" class="form-control border-0" placeholder="Start typing..." autocomplete="off">
                    </div>
                    <div id="search-result" class="mt-1">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var targetDay = 1;
        var targetMealPlan = 0;
        var targetItem = 0;
        var targetMealId = 0;

        var modalMealId = 0;
        var modalMealPlanId = 0;

        var initialMealPlanId = '{{$mealPlanId}}';

        function setMealAndMealPlanId(x, y) {
            modalMealId = x;
            modalMealPlanId = y;
            initSearchResult();
        }

        function setRemoveItem(mealId, mealPlanId, day, item) {
            targetMealId = mealId;
            targetMealPlanId = mealPlanId;
            targetDay = day;
            targetItem = item;
        }

        function changeDay(d) {
            targetDay = d;
        }

        function handleDayChange(day) {
            targetDay = day;

            let url = "{{ route('admin.meal-plans.fetch-meals') }}";

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    'mealPlanId' : '{{$mealPlanId}}',
                    'day': day,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    data = JSON.parse(data);

                    $('#meal-plan-html').html(data.html);
                    $('[data-toggle="tab"]').tab();

                    $('#total-calories').text(data.total_calories);
                    $('#total-carbs').text(data.total_carbs);
                    $('#total-fats').text(data.total_fats);
                    $('#total-fibers').text(data.total_fibers);
                    $('#total-protein').text(data.total_protein);

                    $('#v-pills-1-tab').trigger('click');
                    $('#v-pills-'+ day + '-tab').trigger('click');
                }
            });
        }

        function addItem(ele, itemId) {
            let qty = $(ele).closest('tr').find('.qty-input').val();
            let url = "{{ route('admin.users.add-items-to-meal') }}";

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    'mealId': modalMealId,
                    'mealPlanId': modalMealPlanId,
                    'itemId': itemId,
                    'qty': qty,
                    'day': targetDay
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    let addBtn = $(ele).closest('tr').find('.modal-item-add-btn');
                    let qtyInput = $(ele).closest('tr').find('.qty-input');

                    $(addBtn).attr('disabled', 'disabled');
                    $(addBtn).removeClass('btn');
                    $(addBtn).removeClass('btn-primary');
                    $(addBtn).addClass('border-0');

                    $(addBtn).text('Added').css('color','green');

                    $(qtyInput).attr('disabled', 'disabled');

                    handleDayChange(targetDay);
                }
            });
        }

        function removeItem(mealId) {
            let url = "{{ route('admin.meal-plans.remove-item') }}";

            $('#spinner'+targetMealId).removeClass('d-none');

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    'mealId': targetMealId,
                    'mealPlan': targetMealPlanId,
                    'day': targetDay,
                    'item': targetItem,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    handleDayChange(targetDay);
                }
            });
        }

        function initSearchResult() {
            let url = "{{ route('admin.users.fetch-items-randomly') }}";

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    'mealId': modalMealId,
                    'mealPlan': modalMealPlanId,
                    'day': targetDay,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    $('#search-result').html(data);
                }
            });
        }

        function processInputValue(value) {
            let url = "{{ route('admin.users.fetch-items') }}";

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    'key': value,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    $('#search-result').html(data);
                }
            });
        }

        $(document).ready(function() {
            handleDayChange(targetDay);

            const debounce = (func, delay) => {
                let debounceTimer
                return function () {
                    const context = this
                    const args = arguments
                    clearTimeout(debounceTimer)
                    debounceTimer
                        = setTimeout(() => func.apply(context, args), delay)
                }
            }

            $('#search-input').keyup(debounce(function() {
                let searchValue = $(this).val();

                if (searchValue == "") {
                    $('#search-result').empty();
                } else {
                    processInputValue(searchValue);
                }
            }, 400));

            $('#addModal').on('shown.bs.modal', function () {
                $('#search-input').val('');
                $('#search-input').focus();
            });
        })
    </script>
@endpush
