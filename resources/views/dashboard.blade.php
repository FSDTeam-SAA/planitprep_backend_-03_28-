@extends('layouts.front')

@section('content')
    <section class="dashboard-main">
        <div class="container">
            <div class="row">
                <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="yellow-bg status-outer">
                        <div class="row">
                            <div class="col-7">
                                <h3 class="d-heading">Today Status</h3>
                                <ul>
                                    <li><i class="fa fa-circle color1"></i> Protein</li>
                                    <li><i class="fa fa-circle color2"></i> Carb</li>
                                    <li><i class="fa fa-circle color3"></i> Fat</li>
                                    <li><i class="fa fa-circle color4"></i> Fiber</li>
                                </ul>
                            </div>
                            <div class="col-5">
                                <canvas id="dashboard-chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 ">
                    <div class="current-weight">
                        <div class="row">
                            <div class="col-9">
                                <div class="">
                                    <h3 class="d-heading">Current Weight</h3>
                                    <h4>{{ $data['my_current_weight'] }} <span>KG</span></h4>
                                    <h5>{{ $data['weight_diff'] }} Kg ({{ $data['weight_diff_percentage'] }})%</h5>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="current-weight-w-icon">
                                    <img src="{{ asset('imgs/current-weight-w.svg') }}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="current-weight">
                        <div class="row">
                            <div class="col-9">
                                <div class="">
                                    <h3 class="d-heading">Today Calories</h3>
                                    <h4>{{ $intake['total']['calories'] }} <span>Kcal</span></h4>
                                    <h5>
                                        @if ($user->daily_calorie_intake)
                                            {{ (int) (($intake['total']['calories'] / $user->daily_calorie_intake) * 100) }}%
                                        @endif
                                    </h5>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="gripfire-w-icon"><img src="{{ asset('imgs/gripfire.svg') }}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @forelse ($intake['meals'] as $meal)
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-3">
                        <div class="breakfast bg-color{{ $loop->iteration }}">
                            <div class="row">
                                <div class="col-10">
                                    <div class="m-icons pt-1">
                                        @if ($meal['image'] == '')
                                            <img src="{{ asset('imgs/tea-icon.svg') }}" alt="meal">
                                        @else
                                            <img src="{{ asset(Storage::url('meals/' . $meal['image'])) }}" alt="meal">
                                        @endif
                                    </div>
                                    <h3 class="d-heading mt-2">{{ $meal['name'] }}</h3>
                                </div>
                                <div class="col-2 text-right">
                                    <a href="{{ route('add-food', ['meal_id' => $meal['id']]) }}">
                                        <i class="fa fa-plus edit-icon" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="row text-center mt-2">
                                <div class="col-3">
                                    <h5>{{ $meal['protein'] }}</h5>
                                    <p>Protein</p>
                                </div>
                                <div class="col-3">
                                    <h5>{{ $meal['carbohydrates'] }}</h5>
                                    <p>Carbs</p>
                                </div>
                                <div class="col-3">
                                    <h5>{{ $meal['fat'] }}</h5>
                                    <p>Fat</p>
                                </div>
                                <div class="col-3">
                                    <h5>{{ $meal['fiber'] }}</h5>
                                    <p>Fiber</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    {{--  --}}
                @endforelse

                <!-- Dinner start -->
                <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-3">
                    <div class="breakfast bg-color6">
                        <div class="row">
                            <div class="col-8">
                                <h3 class="d-heading mt-2">Water {{ number_format($user->daily_water, 1) }}L <span
                                        id="water-percent">(100%)</span></h3>
                                <div class="water-drop-icons">
                                    <ul>
                                        <li><img src="imgs/water-drop1.svg" alt=""></li>
                                        <li><img src="imgs/water-drop1.svg" alt=""></li>
                                        <li><img src="imgs/water-drop1.svg" alt=""></li>
                                        <li><img src="imgs/water-drop2.svg" alt=""></li>
                                        <li><img src="imgs/water-drop3.svg" alt=""></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-4 ">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div id="w1" class="w-val"></div>
                                        <div class="d-flex justify-content-between">
                                            <div id="water-minus" class="water-btn ps-1 pt-1">-</div>
                                            <div id="w2" class="w-val"></div>
                                            <div id="water-plus" class="water-btn">+</div>
                                        </div>
                                        <div id="w3" class="w-val"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Dinner end -->
            </div>
        </div>
    </section>
    <section>
        <div class="container">
            <div class="row">
                <div class="col-7 text-center ">
                    <div class="map-main">
                        <h3 class="map-heading text-left">Macros</h3>
                        <div class="progress-bar-container d-flex justify-content-between">
                            <div>
                                <div class="progress-bar">
                                    <div class="progress-fill"
                                        data-fill="{{ ($intake['total']['calories'] / $user->daily_calorie_intake) * 100 }}">
                                    </div>
                                    <div class="progress-circle">
                                       <h5>
                                          @if ($user->daily_calorie_intake)
                                              {{ (int) (($intake['total']['calories'] / $user->daily_calorie_intake) * 100) }}%
                                          @endif
                                       </h5>
                                    </div>
                                </div>
                                <div class="fw-bold">
                                    Calories
                                </div>
                            </div>
                            <div>
                                <div class="progress-bar">
                                    <div class="progress-fill"
                                        data-fill="{{ ($intake['total']['carbohydrates'] / $user->daily_carbs_intake) * 100 }}">
                                    </div>
                                    <div class="progress-circle">
                                        {{ (int) (($intake['total']['carbohydrates'] / $user->daily_carbs_intake) * 100) }}%
                                    </div>
                                </div>
                                <div class="fw-bold">
                                    Carbs
                                </div>
                            </div>
                            <div>
                                <div class="progress-bar">
                                    <div class="progress-fill"
                                        data-fill="{{ ($intake['total']['fiber'] / $user->daily_fiber_intake) * 100 }}">
                                    </div>
                                    <div class="progress-circle">
                                        {{ (int) (($intake['total']['fiber'] / $user->daily_fiber_intake) * 100) }}%</div>
                                </div>
                                <div class="fw-bold">
                                    Fiber
                                </div>
                            </div>
                            <div>
                                <div class="progress-bar">
                                    <div class="progress-fill"
                                        data-fill="{{ ($intake['total']['protein'] / $user->daily_protein_intake) * 100 }}">
                                    </div>
                                    <div class="progress-circle">
                                        {{ (int) (($intake['total']['protein'] / $user->daily_protein_intake) * 100) }}%
                                    </div>
                                </div>
                                <div class="fw-bold">
                                    Protein
                                </div>
                            </div>
                            <div>
                                <div class="progress-bar">
                                    <div class="progress-fill"
                                        data-fill="{{ ($intake['total']['fat'] / $user->daily_fats_intake) * 100 }}">
                                    </div>
                                    <div class="progress-circle">
                                        {{ (int) (($intake['total']['fat'] / $user->daily_fats_intake) * 100) }}%</div>
                                </div>
                                <div class="fw-bold">
                                    Fats
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-5">
                    <div class="today-status">
                        <h3 class="map-heading mb-4">Today Status</h3>
                        <div class="row">
                            <div class="col-6">
                                <ul>
                                    <li>Calories</li>
                                    <li>Carbs</li>
                                    <li>Fiber</li>
                                    <li>Protein</li>
                                    <li>Fats</li>
                                </ul>
                            </div>
                            <div class="col-6 text-right">
                                <ul>
                                    <li>{{ $intake['total']['calories'] }}/{{ $user->daily_calorie_intake }}</li>
                                    <li>{{ $intake['total']['carbohydrates'] }}/{{ $user->daily_carbs_intake }}</li>
                                    <li>{{ $intake['total']['fiber'] }}/{{ $user->daily_fiber_intake }}</li>
                                    <li>{{ $intake['total']['protein'] }}/{{ $user->daily_protein_intake }}</li>
                                    <li>{{ $intake['total']['fat'] }}/{{ $user->daily_fats_intake }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!--Slider1-->
    <section id="demos" class="  ">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="food-slider">
                        <h3 class="map-heading text-left">Food</h3>
                        <div class="large-12 columns">
                            <div id="food-slider" class="owl-carousel  owl-theme">
                                @forelse ($food_groups as $item)
                                    <div class="item food-slide">
                                        <a href="#">
                                            @if ($item->image != '')
                                                <img class="img-fluid" src="{{ asset(Storage::url('food-groups/' . $item->image)) }}">
                                            @else
                                                <img class="img-fluid" src="{{ asset('imgs/f1.jpg') }}">
                                            @endif
                                            <h4>{{ $item->name }}</h4>
                                        </a>
                                    </div>
                                @empty
                                    {{--  --}}
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="{{ asset('js/chart.js') }}"></script>

    @push('scripts')
        <script>
            const totalData = @json($intake['total']);
            const ctx = document.getElementById('dashboard-chart').getContext('2d');
            const nutritionChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    datasets: [{
                        data: [
                            totalData.protein,
                            totalData.carbohydrates,
                            totalData.fat,
                            totalData.fiber
                        ],
                        backgroundColor: ['#ffda60', '#97d923', '#2196f3', '#e56433'],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    const nutrientNames = ['Protein', 'Carbohydrates', 'Fat', 'Fiber'];
                                    const nutrientIndex = tooltipItem.dataIndex;
                                    return nutrientNames[nutrientIndex] + ': ' + tooltipItem.raw;
                                },
                                beforeLabel: function() {
                                    return '';
                                }
                            }
                        }
                    }
                }
            });

            $(document).ready(function() {
                let owl = $('#food-slider');
                owl.owlCarousel({
                    margin: 10,
                    nav: true,
                    loop: true,
                    autoplay: true,
                    autoplayTimeout: 5000,
                    autoplayHoverPause: true,
                    responsive: {
                        0: {
                            items: 1
                        },
                        600: {
                            items: 1
                        },
                        1000: {
                            items: 5
                        }
                    }
                });

                $('.progress-fill').each(function(x, el) {
                    let level = $(el).attr('data-fill');
                    level = parseInt(level);

                    if (level > 100) {
                        level = 100;
                    }

                    let circleTop = ((100 - level) + 1) + '%';
                    level = level + '%';

                    $(el).height(level)
                    $(el).next().css('top', circleTop);
                });
            });

            const water = [
                '0.1L', '0.2L', '0.3L', '0.4L', '0.5L', '0.6L', '0.7L', '0.8L', '0.9L', '1L',
                '1.1L', '1.2L', '1.3L', '1.4L', '1.5L', '1.6L', '1.7L', '1.8L', '1.9L', '2L',
                '2.1L', '2.2L', '2.3L', '2.4L', '2.5L', '2.6L', '2.7L', '2.8L', '2.9L', '3L',
                '3.1L', '3.2L', '3.3L', '3.4L', '3.5L', '3.6L', '3.7L', '3.8L', '3.9L', '4L',
                '4.1L', '4.2L', '4.3L', '4.4L', '4.5L', '4.6L', '4.7L', '4.8L', '4.9L', '5L',
                '5.1L', '5.2L', '5.3L', '5.4L', '5.5L', '5.6L', '5.7L', '5.8L', '5.9L', '6L',
                '6.1L', '6.2L', '6.3L', '6.4L', '6.5L', '6.6L', '6.7L', '6.8L', '6.9L', '7L',
                '7.1L', '7.2L', '7.3L', '7.4L', '7.5L', '7.6L', '7.7L', '7.8L', '7.9L', '8L',
                '8.1L', '8.2L', '8.3L', '8.4L', '8.5L', '8.6L', '8.7L', '8.8L', '8.9L', '9L',
            ];

            $(document).ready(function() {
                let daily_water = parseFloat('{{ $user->daily_water }}') + 'L';
                let water_index = water.indexOf(daily_water);

                let waterIndex1 = water_index - 1;
                let waterIndex2 = water_index;
                let waterIndex3 = water_index + 1;

                $('#w1').text(water[waterIndex1]);
                $('#w2').text(water[waterIndex2]);
                $('#w3').text(water[waterIndex3]);

                $('#water-minus').click(function() {
                    if (waterIndex2 == 0) {
                        waterIndex2 = 89;
                    }

                    if (waterIndex1 == 0) {
                        waterIndex1 = 89;
                    }

                    if (waterIndex3 == 0) {
                        waterIndex3 = 89;
                    }

                    waterIndex1 -= 1;
                    waterIndex2 -= 1;
                    waterIndex3 -= 1;

                    $('#w1').text(water[waterIndex1]);
                    $('#w2').text(water[waterIndex2]);
                    $('#w3').text(water[waterIndex3]);
                });

                $('#water-plus').click(function() {
                    if (waterIndex2 == 89) {
                        waterIndex2 = -1;
                    }

                    if (waterIndex1 == 89) {
                        waterIndex1 = -1;
                    }

                    if (waterIndex3 == 89) {
                        waterIndex3 = -1;
                    }

                    waterIndex1 += 1;
                    waterIndex2 += 1;
                    waterIndex3 += 1;

                    $('#w1').text(water[waterIndex1]);
                    $('#w2').text(water[waterIndex2]);
                    $('#w3').text(water[waterIndex3]);
                });

                $('.water-btn').click(function() {
                    let x = '{{ $user->daily_water }}';
                    let y = $('#w2').text();
                    y = y.slice(0, -1);
                    let z = y / x * 100;
                    z = '(' + Math.round(z) + '%' + ')';
                    $('#water-percent').text(z);
                });
            });
        </script>
    @endpush
@endsection
