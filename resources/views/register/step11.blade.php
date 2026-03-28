@extends('layouts.blank')

@section('content')
    <section class="login-main">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-9 col-xl-9 col-lg-12 col-md-12 col-sm-12 col-md-12">
                    <div class="card-main login-card step11 shadow-n">
                        <form action="{{ route('store-step11') }}" method="POST">
                            @csrf
                            <h3>Get Started</h3>
                            <h4>Step 11 of 12</h4>
                            <div class="step-loading">
                                <div class="loading11"></div>
                            </div>

                            <h5 class="login-lable">How many days do you plan to exercise?</h5>
                            <div class="maingoal">
                                @foreach ($days as $item)
                                    <label class="labl">
                                        <input type="radio" name="days" value="{{ $item->id }}" {{ session('step11.days') == $item->id ? 'checked' : '' }} />
                                        <div class="exercise-img"><img src="{{ $item->image }}">{{ $item->name }}</div>
                                    </label>
                                    @if ($loop->first)
                                        <div class="clearfix"></div>
                                    @endif
                                @endforeach
                            </div>

                            <div>
                                @if ($errors->step11_error_bag->any())
                                    <div class="alert alert-danger pb-0 text-start">
                                        @foreach ($errors->step11_error_bag->all() as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex login-b-buttons">
                                <a class="back-button" href="{{ route('step10') }}">Back</a>
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
                var options = {
                    chart: {
                        height: 340,

                        type: 'donut',
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                            }
                        }
                    },
                    series: [40, 60],
                    colors: ["#81d5cd", "#20534e"],
                    labels: ["Pending Plan", "Creating Plan"],
                    legend: {
                        show: false
                    }
                };
                var chart = new ApexCharts(document.querySelector("#incomeByCategory"), options);
                chart.render();
            })();
        </script>
    @endpush
@endsection
