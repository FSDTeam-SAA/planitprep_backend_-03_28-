@extends('layouts.blank')

@section('content')
    <section class="login-main">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-9 col-xl-9 col-lg-12 col-md-12 col-sm-12 col-md-12">
                    <div class="card-main login-card shadow-n step3">
                        <form action="{{ route('store-step3') }}" method="POST">
                            @csrf

                            <h3>Get Started</h3>
                            <h4>Step 3 of 12</h4>
                            <div class="step-loading">
                                <div class="loading3"></div>
                            </div>

                            <h5 class="login-lable">What's your Age?</h5>
                            <input name="age" type="text" class="form-control ageinput" value="{{ old('age', session('step3.age')) }}" autofocus>

                            <div>
                                @if ($errors->step3_error_bag->any())
                                    <div class="alert alert-danger pb-0 text-start">
                                        @foreach ($errors->step3_error_bag->all() as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex login-b-buttons">
                                <a class="back-button" href="{{ route('step2') }}">Back</a>
                                <button class="btn continue-button mx-auto" type="submit">Continue</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
