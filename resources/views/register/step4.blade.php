@extends('layouts.blank')

@section('content')
    <section class="login-main">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-9 col-xl-9 col-lg-12 col-md-12 col-sm-12 col-md-12">
                    <div class="card-main login-card step4 shadow-n">
                        <form action="{{ route('store-step4') }}" method="POST">
                            @csrf
                            <h3>Get Started</h3>
                            <h4>Step 4 of 12</h4>
                            <div class="step-loading">
                                <div class="loading4"></div>
                            </div>
                            <h5 class="login-lable">What's your Height?</h5>
                            <div class="row justify-content-center">
                                <div class="col-2">
                                    <h4> Feet</h4>
                                    <input name="feet" type="text" class="form-control ageinput" placeholder="" value="{{ old('feet', session('step4.feet')) }}" autofocus>
                                </div>
                                <div class="col-2">
                                    <h4> Inch</h4>
                                    <input name="inch" type="text" class="form-control ageinput" placeholder="0" value="{{ old('inch', session('step4.inch')) }}">
                                </div>
                            </div>

                            <div>
                                @if ($errors->step4_error_bag->any())
                                    <div class="alert alert-danger pb-0 text-start">
                                        @foreach ($errors->step4_error_bag->all() as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex login-b-buttons">
                                <a class="back-button" href="{{ route('step3') }}">Back</a>
                                <button class="btn continue-button mx-auto" type="submit">Continue</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
