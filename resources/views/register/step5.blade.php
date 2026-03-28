@extends('layouts.blank')

@section('content')
    <section class="login-main">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-9 col-xl-9 col-lg-12 col-md-12 col-sm-12 col-md-12">
                    <div class="card-main login-card step5 shadow-n">
                        <form action="{{ route('store-step5') }}" method="POST">
                            @csrf
                            <h3>Get Started</h3>
                            <h4>Step 5 of 12</h4>
                            <div class="step-loading">
                                <div class="loading5"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="login-lable">Your Current Weight</h5>
                                    <div class="qty-input mb-5">
                                        <button id="weight-minus-btn" class="qty-count qty-count--minus" data-action="minus" type="button">-</button>
                                        <input id="weight-input" class="product-qty" type="text" name="current_weight" placeholder="" value="{{ old('current_weight', session('step5.current_weight')) }}" oninput="restrictToInteger(event)" autofocus>
                                        <button id="weight-plus-btn" class="qty-count qty-count--add" data-action="add" type="button">+</button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="login-lable">Your Target Weight</h5>
                                    <div class="qty-input mb-5">
                                        <button id="weight-minus-btn-2" class="qty-count qty-count--minus" data-action="minus" type="button">-</button>
                                        <input id="weight-input-2" class="product-qty" type="text" name="target_weight" placeholder="" value="{{ old('target_weight', session('step5.target_weight')) }}" oninput="restrictToInteger(event)" autofocus>
                                        <button id="weight-plus-btn-2" class="qty-count qty-count--add" data-action="add" type="button">+</button>
                                    </div>
                                </div>
                            </div>

                            <div>
                                @if ($errors->step5_error_bag->any())
                                    <div class="alert alert-danger pb-0 text-start">
                                        @foreach ($errors->step5_error_bag->all() as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex login-b-buttons">
                                <a class="back-button" href="{{ route('step4') }}">Back</a>
                                <button class="btn continue-button mx-auto" type="submit">Continue</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            function restrictToInteger(event) {
                let inputValue = event.target.value;
                event.target.value = inputValue.replace(/[^0-9]/g, '');
            }

            $(document).ready(function(){
                $('#weight-minus-btn').click(function() {
                    let x = Number($('#weight-input').val()) - 1;
                    if (x >= 0) {
                        $('#weight-input').val(x);
                    }
                });
                $('#weight-plus-btn').click(function() {
                    let x = Number($('#weight-input').val()) + 1;
                    $('#weight-input').val(x);
                });

                $('#weight-minus-btn-2').click(function() {
                    let x = Number($('#weight-input-2').val()) - 1;
                    if (x >= 0) {
                        $('#weight-input-2').val(x);
                    }
                });
                $('#weight-plus-btn-2').click(function() {
                    let x = Number($('#weight-input-2').val()) + 1;
                    $('#weight-input-2').val(x);
                });
            });
        </script>
    @endpush

@endsection
