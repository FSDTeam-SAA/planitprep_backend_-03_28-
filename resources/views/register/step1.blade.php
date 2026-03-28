@extends('layouts.blank')

@section('content')

<section class="login-main">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-9 col-xl-9 col-lg-12 col-md-12 col-sm-12 col-md-12">
                <div class="card-main login-card step1 shadow-n mt-5">
                    <form action="{{ route('store-step1') }}" method="POST">
                        @csrf

                        <h3>Get Started</h3>
                        <h4>Step 1 of 12</h4>
                        <div class="step-loading">
                            <div class="loading"></div>
                        </div>

                        <h5 class="login-lable">What's your Name?</h5>
                        <input name="name" maxlength="50" type="text" placeholder="Your full name" class="form-control login-input" value="{{ old('name') }}" autofocus>

                        <div>
                            @if ($errors->step1_error_bag->any())
                                <div class="alert alert-danger pb-0 text-start">
                                    @foreach ($errors->step1_error_bag->all() as $error)
                                        <p>{{ $error }}</p>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <button class="btn continue-button mx-auto" type="submit">Continue</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
