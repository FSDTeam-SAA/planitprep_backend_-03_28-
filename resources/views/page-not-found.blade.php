@extends('layouts.front')

@section('content')
    <section>
        <div class="container">
            <div class="row vh-100">
                <div class="col-md-12">
                    <div class="d-flex justify-content-center">
                        <img src="{{ asset('imgs/404.png') }}" alt="page not found image" class="img-fluid">
                    </div>
                    <div class="d-flex justify-content-center py-3">
                        <a href="{{ route('home') }}" class="btn btn-primary px-5 rounded-0">Go to home page</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
