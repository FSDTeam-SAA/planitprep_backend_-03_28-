@extends('layouts.front')
@section('content')
    <section>
        <div class="container">
            <div class="card-main  mb-2 mt-3">
                <div class="row">
                    <div class="back-icon-main">
                        <a href="{{ route('front-dashboard') }}"> <i class="fa fa-chevron-left back-icon"></i></a>
                        <div class="edit-icon cursor-p" onclick="updateWeightModal()"><i class="fa fa-pencil"></i></div>
                    </div>
                    <div class="d-heading text-center mb-3">Achievenments</div>

                    <div class="col-12 p-0">
                        <div class="card-main  shadow-n">
                            <div class="creating-plan-main">
                                <div class="" id="weight-chart">
                                    <div class="fruits-icon"><img src="{{ asset('imgs/weight.png') }}" alt="">
                                    </div>
                                </div>
                                <h5 class="text-center d-heading">
                                    {{ $goal_name }} {{ $weight_diff }}Kg
                                </h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="active-days">
                            <img src="{{ asset('imgs/weight.png') }}" alt="">
                            <h3>{{ $current_weight }}Kg</h3>
                            <p>Current Weight</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="active-days">
                            <img src="{{ asset('imgs/weight.png') }}" alt="">
                            <h3>{{ $target_weight }}Kg</h3>
                            <p>Target Weight</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mt-3">
                        <div class="input-group">
                            <span class="input-group-text">Start Date</span>
                            <input type="date" id="start-date" class="form-control date-range" value="{{ $start_date }}" onchange="dateRangeChart()">
                        </div>
                    </div>
                    <div class="col-md-3 mt-3">
                        <div class="input-group">
                            <span class="input-group-text">End Date</span>
                            <input type="date" id="end-date" class="form-control date-range" value="{{ $end_date }}" onchange="dateRangeChart()">
                        </div>
                    </div>

                    <div class="card-border mt-3" id="weight"></div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="updateWeightModal" tabindex="-1" aria-labelledby="updateWeightModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="updateWeightModalLabel">Update Weight</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div id="food-modal-body" class="modal-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-auto">
                            <label for="weight-field" class="col-form-label">Your Current Weight (Kg)</label>
                        </div>
                        <div class="col-auto">
                            <input id="weight-field" type="number" min="0" max="999" name="current_weight" class="form-control w-100" value="{{ (int)$current_weight }}">
                        </div>
                    </div>
                    <button class="btn btn-primary mt-4" onclick="updateWeight()">Save</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/apexcharts.min.js') }}"></script>

        <script>
            function updateWeightModal() {
                $('#updateWeightModal').modal('show');
                setTimeout(() => {
                    $('#weight-field').focus();
                }, 500);
            }

            function updateWeight() {
                let x = $('#weight-field').val();
                let url = '{{ route('update-current-weight') }}';

                $.post(url, {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'current_weight': x,
                    'target_weight': @json($target_weight),
                }, function(data, status) {
                    $('#updateWeightModal').modal('hide');
                    window.location.reload();
                });
            }

            // Top chart
            var options1 = {
                chart: {
                    height: 200,
                    type: 'donut',
                },
                dataLabels: {
                    enabled: false,
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '80%',
                        }
                    }
                },
                series: [@json($target_remaining), @json($target_achieved)],
                colors: ["#696969", "#EA6A00"],
                labels: ["Remaining Target", "Target Achieved"],
                legend: {
                    show: false
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return Math.round(val) + '%';
                        }
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#weight-chart"), options1);
            chart.render();

            // Bottom Chart
            var track_date = @json($weight_tracking_data['date']);
            var track_value = @json($weight_tracking_data['value']);
            var track_target = @json($weight_tracking_data['target']);
            var targetWeight = @json($target_weight);

            var options2 = {
                chart: {
                    type: 'line',
                    height: 400,
                    zoom: {
                        enabled: true,
                        type: 'xy'
                    },
                    toolbar: {
                        show: true,
                        tools: {
                            zoom: false,
                            zoomin: false,
                            zoomout: false,
                            zoomreset: false,
                            pan: false,
                            reset: false,
                        }
                    },
                },
                series: [{
                        name: 'Current Weight',
                        data: track_value
                    },
                    {
                        name: 'Target Weight',
                        data: track_target
                    }
                ],
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                xaxis: {
                    categories: track_date
                },
                yaxis: {
                    title: {
                        text: 'Weight (kg)'
                    },
                    min: 0,
                    max: targetWeight + 20
                },
                markers: {
                    size: 0,
                    enabled: false,
                },
                dataLabels: {
                    enabled: false,
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function(val) {
                            return val + ' kg';
                        }
                    }
                },
                colors: ['#33C1FF', '#018749'],
            };

            // Create the chart and render it in the div
            var chart = new ApexCharts(document.querySelector("#weight"), options2);
            chart.render();

            function dateRangeChart() {
                let startDate = $('#start-date').val();
                let endDate = $('#end-date').val();

                $.ajax({
                    url: "{{ route('weight-tracking-date-wise') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        start_date: startDate,
                        end_date: endDate,
                    },
                    success: function(res) {
                        var new_track_date = res.date;
                        var new_track_value = res.value;
                        var new_track_target = res.target;

                        chart.updateSeries([{
                                name: 'Current Weight',
                                data: new_track_value
                            },
                            {
                                name: 'Target Weight',
                                data: new_track_target
                            }
                        ]);

                        chart.updateOptions({
                            xaxis: {
                                categories: new_track_date
                            },
                            yaxis: {
                                max: Math.max.apply(Math, new_track_value)
                            }
                        });
                    }
                });
            }
        </script>
    @endpush
@endsection
