@extends('layouts.blank')

@section('content')
    <section class="login-main">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-9 col-xl-9 col-lg-12 col-md-12 col-sm-12 col-md-12">
                    <div class="card-main login-card step1 shadow-n mt-5">
                        <form class="login-form" action="{{ route('process-login') }}" method="POST">
                            @csrf
                            <img class="mb-3" src="{{ $logo }}" alt="">
                            <h3>Welcome Back</h3>
                            <h4>Login</h4>
                            <h5 class="login-lable">What's your Mobile?</h5>
                            <input name="mobile" type="text" maxlength="20" placeholder="Enter your mobile number." class="form-control login-input" autofocus>
                            @include('includes.errors')
                            <button class="btn continue-button mx-auto" type="submit">Continue</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
