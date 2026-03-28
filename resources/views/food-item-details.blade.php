<section>
    <div class="container">
        <div class="card-main add-food-main mb-2 mt-3 pt-0">
            <div class="row">
                <div class="col-md-12 d-flex justify-content-center">
                    <img class="img-fluid food-detail-img" src="{{ $food_item->image }}" alt="food item">
                </div>
                <div class="row mt-3 item-detail justify-content-center">
                    <div class="col-md-12">
                        <div class="">
                            <h4>{{ $food_item->name }}</h4>
                            <p>{{ $food_item->serving_size }}{{ $food_item->serving_unit }} - {{ $food_item->calories }}  Cal</p>
                        </div>
                    </div>
                    <div class="col-12 text-center">
                        <div class="Gram-main">
                            <h4>{{ $food_item->serving_unit }}</h4>
                            <div class="qty-input">
                                <button id="qty-minus" class="qty-count qty-count--minus" data-action="minus" type="button">-</button>
                                <input id="qty" class="product-qty" type="number" name="product-qty" min="0"
                                    max="10" value="1">
                                <button id="qty-plus" class="qty-count qty-count--add" data-action="add" type="button">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 ">
                        <img class="img-fluid mt-4 mb-4" src="imgs/map1.jpg" alt="">
                        <div class="add-b">
                            <a class="add-b" href="javascript:void(0);" onclick="save()">ADD</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function save() {
        let url = '{{ route("save-intake") }}';

        $.post(url, {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'meal_id' : '{{ $meal_id }}',
            'food_item_id' : '{{ $food_item->id }}',
            'qty' : $('#qty').val()
        }, function(data, status) {
            window.location.href = '{{ route("front-dashboard") }}';
        });
    }

    function restrictToInteger(event) {
        let inputValue = event.target.value;
        event.target.value = inputValue.replace(/[^0-9]/g, '');
    }

    $(document).ready(function(){
        $('#qty-minus').click(function() {
            let x = Number($('#qty').val()) - 1;
            if (x >= 0) {
                $('#qty').val(x);
            }
        });
        $('#qty-plus').click(function() {
            let x = Number($('#qty').val()) + 1;
            $('#qty').val(x);
        });
    });
</script>
