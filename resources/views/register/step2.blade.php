@extends('layouts.blank')

@section('content')
    <section class="login-main">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-9 col-xl-9 col-lg-12 col-md-12 col-sm-12 col-md-12">
                    <div class="card-main shadow-n login-card step2">
                        <form action="{{ route('store-step2') }}" method="POST">
                            @csrf

                            <h3>Get Started</h3>
                            <h4>Step 2 of 12</h4>
                            <div class="step-loading">
                                <div class="loading2"></div>
                            </div>

                            <h5 class="login-lable">What's your Gender?</h5>

                            <div class="row justify-content-center">
                                <div class="col-4">
                                    <div class="male">
                                        <label class="gender-btn">
                                        <input type="radio" name="gender" value="M" {{ session('step2.gender') == 'M' ? 'checked' : '' }}>
                                        <img src="{{ asset('imgs/male.jpg') }}" alt="gender photo">
                                        <h5>Male</h5>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="male">
                                        <label class="gender-btn">
                                        <input type="radio" name="gender" value="F" {{ session('step2.gender') == 'F' ? 'checked' : '' }}>
                                        <img src="{{ asset('imgs/female.jpg') }}" alt="gender photo">
                                        <h5>Female</h5>
                                    </div>
                                </div>
                            </div>

                            <div>
                                @if ($errors->step2_error_bag->any())
                                    <div class="alert alert-danger pb-0 text-start">
                                        @foreach ($errors->step2_error_bag->all() as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex login-b-buttons">
                                <button class="btn continue-button mx-auto" type="submit">Continue</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
