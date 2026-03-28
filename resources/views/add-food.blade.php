@extends('layouts.front')

@section('content')
    <section>
        <div class="container">
            <div class="card-main add-food-main mb-2 mt-3">
                <div class="row">
                    <div class="back-icon-main">
                        <a href="{{ route('front-dashboard') }}">
                            <i class="fa fa-chevron-left back-icon"></i>
                        </a>
                    </div>
                    <div class="d-heading text-center mb-3">Add Food</div>
                    <input id="search-input" type="text" class="form-control" placeholder="Search">
                    <div class="col-12 p-0">
                        <div class="food-list">
                            <ul>
                                @forelse ($food_items as $item)
                                    <li class="food-item" data-val="{{ $item->name }}">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                {{ $item->name }}
                                                @if (in_array($item->id, $intake_keys))
                                                    <span class="text-success">{{ $intake[$item->id]['qty'] }}</span>
                                                    <i class="fa fa-minus text-danger remove-food-item"
                                                        onclick="removeItem({{ $intake[$item->id]['record_id'] }})"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <a href="javascript:void(0)" onclick="foodItemDetails({{ $meal_id }}, {{ $item->id }})">
                                                    <i class="fa fa-plus"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    {{--  --}}
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="foodModal" tabindex="-1" aria-labelledby="foodModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="foodModalLabel">Item Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div id="food-modal-body" class="modal-body">
                    ...
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmRemoveModal" tabindex="-1" aria-labelledby="confirmRemoveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmRemoveModalLabel">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div id="food-modal-body" class="modal-body">
                    Are you sure to remove this item?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="confirmRemove()">Yes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                  </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            var targetItemId = 0;

            function removeItem(id) {
                targetItemId = id;
                $('#confirmRemoveModal').modal('show');
            }

            function confirmRemove() {
                let url = '{{ route('remove-intake-item') }}';

                $.post(url, {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': targetItemId,
                }, function(data, status) {
                    window.location.reload();
                });
            }

            function foodItemDetails(mealId, itemId) {
                let url = '{{ route('food-item-details') }}';

                $.post(url, {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'meal_id': mealId,
                    'food_item_id': itemId,
                }, function(data, status) {
                    console.log(data)
                    $('#food-modal-body').html(data);
                    $('#foodModal').modal('show');
                });
            }

            $(document).ready(function() {
                const $foodItems = $('.food-item');
                const $searchInput = $('#search-input');

                $searchInput.keyup(function() {
                    let key = $searchInput.val().trim();

                    if (key === '') {
                        $foodItems.removeClass('d-none');
                    } else {
                        let escapedKey = key.replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&');
                        let pattern = new RegExp(escapedKey, 'i');

                        $foodItems.each(function() {
                            let dataVal = $(this).attr('data-val');

                            if (pattern.test(dataVal)) {
                                $(this).removeClass('d-none');
                            } else {
                                $(this).addClass('d-none');
                            }
                        });
                    }
                });
            });
        </script>
    @endpush
@endsection
