@extends('layouts.front')

@section('content')
    <section>
        <div class="container">
            <div class="card-main add-food-main mb-2 mt-3">
                <div class="row">
                    <div class="back-icon-main">
                        <a href="{{ route('front-dashboard') }}"> <i class="fa fa-chevron-left back-icon"></i></a>
                    </div>
                    <div class="d-heading text-center mb-3">Today Status</div>

                    <img class="img-fluid" src="{{ asset('imgs/status.png') }}" alt="">
                    <div class="today-status-main">
                        <div class="row">
                            <div class="col-6">
                                <h3>Current</h3>
                                <h4> Weight </h4>
                                <h2>{{ $current_weight }}kg</h2>
                            </div>
                            <div class="col-6 text-right">
                                <h3>Recommended</h3>
                                <h4> Weight </h4>
                                <h2>80.0kg</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="container">
            <div class="row">
                @forelse ($data as $item)
                    @if ($item['data'] != [])
                        <div class="col-12">
                            <h3 class="d-heading mt-4">{{ $item['name'] }} <span>({{ $item['total_calories'] }})</span></h3>
                        </div>
                        @forelse ($item['data'] as $x)
                            <div class="col-6 today-diet mb-3">
                                <div class="card-border">
                                    <div class="row">
                                        <div class="col-3">
                                            <img class="img-fluid diet-pics" src="{{ asset(Storage::url('food-items/' . $x['image'])) }}" alt="">
                                        </div>
                                        <div class="col-9">
                                            @if ($x['type'] == 'VG' || $x['type'] == 'VE')
                                                <img class="non-veg-icon" src="{{ asset('imgs/veg.png') }}" alt="">
                                            @else
                                                <img class="non-veg-icon" src="{{ asset('imgs/non.png') }}" alt="">
                                            @endif

                                            <h3>{{ $x['food_item_name'] }}</h3>
                                            <p>{{ $x['quantity'] }} {{ $x['serving_unit'] }} | {{ (int) $x['calories'] }} calories</p>
                                            <ul>
                                                <li>P:{{ round($x['protein']) }}g</li>
                                                <li>C:{{ round($x['total_carbohydrates']) }}g</li>
                                                <li>F:{{ round($x['total_fat']) }}g</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            {{--  --}}
                        @endforelse
                    @endif
                @empty
                    {{--  --}}
                @endforelse
            </div>
        </div>
    </section>
@endsection
