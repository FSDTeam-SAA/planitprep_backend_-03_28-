@extends('layouts.blank')

@section('content')
    @php
        $colors = ['bg-color7', 'bg-color8', 'bg-color9'];
    @endphp

    <section class="login-main">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-7 col-lg-12 col-md-6 col-sm-12 col-md-12">
                    <div class="card-main">
                        <div class="">
                            <h3 id="buy-membership-h">Select Membership Plan</h3>
                            <hr>
                        </div>
                        @forelse ($memberships as $item)
                            <a href="{{ route('checkout', $item->id) }}">
                                <div class="my-3 py-2 {{ $colors[$loop->index % count($colors)] }}">
                                    <span class="fs-4">{{ $item->title }}</span>
                                    &nbsp;&nbsp;
                                    {{ $item->month }} Month
                                    &nbsp;&nbsp;
                                    <i class="fa fa-inr" aria-hidden="true"></i> {{ number_format($item->price, 2) }}
                                </div>
                            </a>
                        @empty
                            {{--  --}}
                        @endforelse
                        <div class="py-3">
                            <a href="{{ route('front-dashboard') }}" class="text-secondary">Skip</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
