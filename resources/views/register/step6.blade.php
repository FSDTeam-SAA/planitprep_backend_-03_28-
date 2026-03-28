@extends('layouts.blank')

@section('content')
    <section class="login-main">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-9 col-xl-9 col-lg-12 col-md-12 col-sm-12 col-md-12">
                    <div class="card-main login-card step6 shadow-n">
                        <form action="{{ route('store-step6') }}" method="POST">
                            @csrf
                            <h3>Get Started</h3>
                            <h4>Step 6 of 12</h4>
                            <div class="step-loading">
                                <div class="loading6"></div>
                            </div>
                            <h5 class="login-lable">What's your country?</h5>
                            <select id="country-select" name="country" class="form-select form-select-lg mb-3 login-input" aria-label="Large select example">
                                <option selected disabled>Select your country</option>
                                @foreach ($countries as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                            <h5 class="login-lable">What's your state?</h5>
                            <select id="state-select" name="state" class="form-select form-select-lg mb-3 login-input" aria-label="Large select example">
                                <option selected disabled>Select your state</option>
                            </select>
                            <h5 class="login-lable">What's your city?</h5>
                            <select id="city-select" name="city" class="form-select form-select-lg mb-3 login-input" aria-label="Large select example">
                                <option selected disabled>Select your city</option>
                            </select>

                            <div>
                                @if ($errors->step6_error_bag->any())
                                    <div class="alert alert-danger pb-0 text-start">
                                        @foreach ($errors->step6_error_bag->all() as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex login-b-buttons">
                                <a class="back-button" href="{{ route('step5') }}">Back</a>
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
            (function() {
                let country = '{{ session('step6.country') }}';
                let state = '{{ session('step6.state') }}';
                let city = '{{ session('step6.city') }}';

                $('#country-select').change(function() {
                    $.ajax({
                        url: '{{ route("get-states") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            'id': $(this).val()
                        },
                        success: function(res) {
                            $('.state-option').remove();
                            $('.city-option').remove();

                            $('#state-select').append('<option selected disabled>Select your state</option>');

                            res.forEach(function(x) {
                                let option = $('<option class="state-option"></option>').attr('value', x.id).text(x.name);
                                $('#state-select').append(option);
                            });
                        }
                    });

                    $('#state-select').focus();
                });

                $('#state-select').change(function() {
                    $.ajax({
                        url: '{{ route("get-cities") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            'id': $(this).val()
                        },
                        success: function(res) {
                            $('.city-option').remove();
                            $('#city-select').append('<option selected disabled>Select your city</option>');
                            res.forEach(function(x) {
                                let option = $('<option class="city-option"></option>').attr('value', x.id).text(x.name);
                                $('#city-select').append(option);
                            });
                        }
                    });

                    $('#city-select').focus();
                });

                if (country != '') {
                    $('#country-select').val(country).trigger('change');

                    setTimeout(() => {
                        if (state != '') {
                            $('#state-select').val(state).trigger('change');

                            setTimeout(() => {
                                if (city != '') {
                                    $('#city-select').val(city).trigger('change');
                                }
                            }, 300);
                        }
                    }, 300);
                }
            })();
        </script>
    @endpush
@endsection
