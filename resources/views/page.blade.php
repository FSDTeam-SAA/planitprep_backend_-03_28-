@extends('layouts.front')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-12 my-5">
                <h2 class="text-center mb-4">{{ $page->title }}</h2>
                {!! $page->content !!}
            </div>
        </div>
    </div>

@endsection
