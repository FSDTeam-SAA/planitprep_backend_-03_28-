@extends('layouts.front')

@section('content')
    <section>
        <div class="container">
            <div class="card-main  bmi-card mb-2 mt-3">
                <div class="row">
                    <div class="back-icon-main">
                        <a href="dashboard.php"> <i class="fa fa-chevron-left back-icon"></i></a>
                    </div>
                    <div class="d-heading text-center mb-3">BMI</div>

                    <div class="col-6 p-0">
                        <div class="food-list bmi-main">
                            <ul>
                                <li>
                                    <h5><img src="imgs/bmi1.png"> Weight</h5>
                                </li>
                                <li>
                                    <h5><img src="imgs/bmi2.png"> BMI</h5>
                                </li>
                                <li>
                                    <h5><img src="imgs/bmi3.png"> Fat</h5>
                                </li>
                                <li>
                                    <h5><img src="imgs/bmi4.png"> Muscle</h5>
                                </li>
                                <li>
                                    <h5><img src="imgs/bmi5.png"> Water</h5>
                                </li>
                                <li>
                                    <h5><img src="imgs/bmi6.png"> Visceral Fat</h5>
                                </li>
                                <li>
                                    <h5><img src="imgs/bmi7.png"> Bone Mass</h5>
                                </li>
                                <li>
                                    <h5><img src="imgs/bmi8.png"> Metabolism</h5>
                                </li>
                                <li>
                                    <h5><img src="imgs/bmi11.png"> Body Age</h5>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-4 text-right p-0">
                        <div class="food-list bmi-main">
                            <ul>
                                <li>{{ $weight }} kg</li>
                                <li>{{ $bmi }}</li>
                                <li>{{ $body_fat }}%</li>
                                <li>{{ $muscle_mass }}Kg</li>
                                <li>{{ $water }}Kg</li>
                                <li>{{ $visceral_fat }}</li>
                                <li>{{ $bone_mass }}</li>
                                <li>{{ $metabolism }}</li>
                                <li>{{ $body_age }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-2 text-right p-0">
                        <div class="food-list bmi-main">
                            <ul>
                                <li><span class="bmi-tab {{ $bg['weight'] }}">{{ $stat['weight'] }}</span></li>
                                <li><span class="bmi-tab {{ $bg['bmi'] }}">{{ $stat['bmi'] }}</span></li>
                                <li><span class="bmi-tab {{ $bg['body_fat'] }}">{{ $stat['body_fat'] }}</span></li>
                                <li><span class="bmi-tab {{ $bg['muscle_mass'] }}">{{ $stat['muscle_mass'] }}</span></li>
                                <li><span class="bmi-tab {{ $bg['water'] }}">{{ $stat['water'] }}</span></li>
                                <li><span class="bmi-tab {{ $bg['visceral'] }}">{{ $stat['visceral'] }}</span></li>
                                <li><span class="bmi-tab {{ $bg['bone_mass'] }}">{{ $stat['bone_mass'] }}</span></li>
                                <li><span class="bmi-tab {{ $bg['metabolism'] }}">{{ $stat['metabolism'] }}</span></li>
                                <li><span class="bmi-tab {{ $bg['body_age'] }}">{{ $stat['body_age'] }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
