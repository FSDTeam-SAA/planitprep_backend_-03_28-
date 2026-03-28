@extends('layouts.blank')

@section('content')
    <section class="login-main">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-9 col-xl-9 col-lg-12 col-md-12 col-sm-12 col-md-12">
                    <div class="card-main login-card step8 shadow-n">
                        <form action="{{ route('store-step8') }}" method="POST">
                            @csrf
                            <h3>Get Started</h3>
                            <h4>Step 8 of 12</h4>
                            <div class="step-loading">
                                <div class="loading8"></div>
                            </div>
                            <h5 class="login-lable">What's your medical condition?</h5>
                            <div class="medical">
                                <ul>
                                    @foreach ($medical_issues as $item)
                                        <li>
                                            <label class="labl">
                                                <input type="checkbox" name="medical_issue[]" value="{{ $item->id }}" {{ in_array($item->id, session('step8.medical_issue', [])) ? 'checked' : '' }} />
                                                <div>{{ $item->name }}</div>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="clearfix"></div>

                            <div>
                                @if ($errors->step8_error_bag->any())
                                    <div class="alert alert-danger pb-0 text-start">
                                        @foreach ($errors->step8_error_bag->all() as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex login-b-buttons">
                                <a class="back-button" href="{{ route('step7') }}">Back</a>
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
