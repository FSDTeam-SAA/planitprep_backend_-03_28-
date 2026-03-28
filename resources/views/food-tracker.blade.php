@extends('layouts.front')
@section('content')
    <section>
        <div class="container">
            <div class="card-main  mb-2 mt-3">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-6 d-flex">
                        <div class="input-group mx-1">
                            <span class="input-group-text">Start Date</span>
                            <input type="date" id="start-date" class="form-control date-range" value="{{ $start_date }}" onchange="dateRangeChart()">
                        </div>
                        <div class="input-group mx-1">
                            <span class="input-group-text">End Date</span>
                            <input type="date" id="end-date" class="form-control date-range" value="{{ $end_date }}" onchange="dateRangeChart()">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card-border mt-3" id="calorie-chart"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="card-border mt-3" id="carbs-chart"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="card-border mt-3" id="protein-chart"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="card-border mt-3" id="fats-chart"></div>
                    </div>
                    {{-- <div class="card-border mt-3" id="fiber-chart"></div> --}}
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/apexcharts.min.js') }}"></script>

        <script>
            // Calorie Chart
            var calorieChartOptions = {
                chart: {
                    type: 'line',
                    height: 300,
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
                title: {
                    text: 'Calorie',
                    align: 'center',
                    style: {
                        fontSize: '20px',
                        fontWeight: 'bold',
                        color: '#666666'
                    }
                },
                series: [{
                        name: 'Intake',
                        data: @json($calorie_arr)
                    },
                    {
                        name: 'Target',
                        data: @json($calorieTarget)
                    }
                ],
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                xaxis: {
                    categories: @json($dates)
                },
                yaxis: {
                    min: 0,
                    max: @json($dailyCalorie) + 100
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
                            return val;
                        }
                    }
                },
                colors: ['#33C1FF', '#018749'],
            };

            var calorieChart = new ApexCharts(document.querySelector("#calorie-chart"), calorieChartOptions);
            calorieChart.render();


            // Carbs chart
            var carbsChartOptions = {
                chart: {
                    type: 'line',
                    height: 300,
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
                title: {
                    text: 'Carbs',
                    align: 'center',
                    style: {
                        fontSize: '20px',
                        fontWeight: 'bold',
                        color: '#666666'
                    }
                },
                series: [{
                        name: 'Intake',
                        data: @json($carbs_arr)
                    },
                    {
                        name: 'Target',
                        data: @json($carbsTarget)
                    }
                ],
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                xaxis: {
                    categories: @json($dates)
                },
                yaxis: {
                    min: 0,
                    max: @json($dailyCarbs) + 100
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
                            return val;
                        }
                    }
                },
                colors: ['#33C1FF', '#018749'],
            };

            var carbsChart = new ApexCharts(document.querySelector("#carbs-chart"), carbsChartOptions);
            carbsChart.render();

            // Protein chart
            var proteinChartOptions = {
                chart: {
                    type: 'line',
                    height: 300,
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
                title: {
                    text: 'Protein',
                    align: 'center',
                    style: {
                        fontSize: '20px',
                        fontWeight: 'bold',
                        color: '#666666'
                    }
                },
                series: [{
                        name: 'Intake',
                        data: @json($protein_arr)
                    },
                    {
                        name: 'Target',
                        data: @json($proteinTarget)
                    }
                ],
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                xaxis: {
                    categories: @json($dates)
                },
                yaxis: {
                    min: 0,
                    max: @json($dailyProtein) + 100
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
                            return val;
                        }
                    }
                },
                colors: ['#33C1FF', '#018749'],
            };

            var proteinChart = new ApexCharts(document.querySelector("#protein-chart"), proteinChartOptions);
            proteinChart.render();

            // Fats chart
            var fatsChartOptions = {
                chart: {
                    type: 'line',
                    height: 300,
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
                title: {
                    text: 'Fats',
                    align: 'center',
                    style: {
                        fontSize: '20px',
                        fontWeight: 'bold',
                        color: '#666666'
                    }
                },
                series: [{
                        name: 'Intake',
                        data: @json($fat_arr)
                    },
                    {
                        name: 'Target',
                        data: @json($fatsTarget)
                    }
                ],
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                xaxis: {
                    categories: @json($dates)
                },
                yaxis: {
                    min: 0,
                    max: @json($dailyFats) + 100
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
                            return val;
                        }
                    }
                },
                colors: ['#33C1FF', '#018749'],
            };

            var fatsChart = new ApexCharts(document.querySelector("#fats-chart"), fatsChartOptions);
            fatsChart.render();

            // Fiber chart
            // var fiberChartOptions = {
            //     chart: {
            //         type: 'line',
            //         height: 300,
            //         zoom: {
            //             enabled: true,
            //             type: 'xy'
            //         },
            //         toolbar: {
            //             show: true,
            //             tools: {
            //                 zoom: false,
            //                 zoomin: false,
            //                 zoomout: false,
            //                 zoomreset: false,
            //                 pan: false,
            //                 reset: false,
            //             }
            //         },
            //     },
            //     title: {
            //         text: 'Fiber',
            //         align: 'center',
            //         style: {
            //             fontSize: '20px',
            //             fontWeight: 'bold',
            //             color: '#666666'
            //         }
            //     },
            //     series: [{
            //             name: 'Intake',
            //             data: @json($fiber_arr)
            //         },
            //         {
            //             name: 'Target',
            //             data: @json($fiberTarget)
            //         }
            //     ],
            //     stroke: {
            //         curve: 'smooth',
            //         width: 2
            //     },
            //     xaxis: {
            //         categories: @json($dates)
            //     },
            //     yaxis: {
            //         min: 0,
            //         max: @json($dailyFiber) + 100
            //     },
            //     markers: {
            //         size: 0,
            //         enabled: false,
            //     },
            //     dataLabels: {
            //         enabled: false,
            //     },
            //     tooltip: {
            //         shared: true,
            //         intersect: false,
            //         y: {
            //             formatter: function(val) {
            //                 return val;
            //             }
            //         }
            //     },
            //     colors: ['#33C1FF', '#018749'],
            // };

            // var fiberChart = new ApexCharts(document.querySelector("#fiber-chart"), fiberChartOptions);
            // fiberChart.render();


            function dateRangeChart() {
                let startDate = $('#start-date').val();
                let endDate = $('#end-date').val();

                $.ajax({
                    url: "{{ route('food-tracking-date-wise') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        start_date: startDate,
                        end_date: endDate,
                    },
                    success: function(res) {
                        console.log(res)

                        var new_track_date = res.dates;

                        var new_track_calorie_value = res.values.calorie_arr;
                        var new_track_carbs_value = res.values.carbs_arr;
                        var new_track_fat_value = res.values.fat_arr;
                        var new_track_protein_value = res.values.protein_arr;
                        var new_track_fiber_value = res.values.fiber_arr;

                        var new_track_calorie_target = res.target.dailyCalorie;
                        var new_track_carbs_target = res.target.dailyCarbs;
                        var new_track_fat_target = res.target.dailyFats;
                        var new_track_fiber_target = res.target.dailyFiber;
                        var new_track_protein_target = res.target.dailyProtein;

                        // Update Calorie chart
                        calorieChart.updateSeries([{
                                name: 'Intake',
                                data: new_track_calorie_value
                            },
                            {
                                name: 'Target',
                                data: new_track_calorie_target
                            }
                        ]);

                        calorieChart.updateOptions({
                            xaxis: {
                                categories: new_track_date
                            },
                            yaxis: {
                                max: new_track_calorie_target[0]
                            }
                        });

                        // Update Carbs chart
                        carbsChart.updateSeries([{
                                name: 'Intake',
                                data: new_track_carbs_value
                            },
                            {
                                name: 'Target',
                                data: new_track_carbs_target
                            }
                        ]);

                        carbsChart.updateOptions({
                            xaxis: {
                                categories: new_track_date
                            },
                            yaxis: {
                                max: new_track_carbs_target[0]
                            }
                        });

                        // Update Protein chart
                        proteinChart.updateSeries([{
                                name: 'Intake',
                                data: new_track_protein_value
                            },
                            {
                                name: 'Target',
                                data: new_track_protein_target
                            }
                        ]);

                        proteinChart.updateOptions({
                            xaxis: {
                                categories: new_track_date
                            },
                            yaxis: {
                                max: new_track_protein_target[0]
                            }
                        });

                        // Update Fats chart
                        fatsChart.updateSeries([{
                                name: 'Intake',
                                data: new_track_fat_value
                            },
                            {
                                name: 'Target',
                                data: new_track_fat_target
                            }
                        ]);

                        fatsChart.updateOptions({
                            xaxis: {
                                categories: new_track_date
                            },
                            yaxis: {
                                max: new_track_fat_target[0]
                            }
                        });

                        // Update Fiber chart
                        // fiberChart.updateSeries([{
                        //         name: 'Intake',
                        //         data: new_track_fiber_value
                        //     },
                        //     {
                        //         name: 'Target',
                        //         data: new_track_fiber_target
                        //     }
                        // ]);

                        // fiberChart.updateOptions({
                        //     xaxis: {
                        //         categories: new_track_date
                        //     },
                        //     yaxis: {
                        //         max: new_track_fiber_target[0]
                        //     }
                        // });
                    }
                });
            }
        </script>
    @endpush
@endsection
