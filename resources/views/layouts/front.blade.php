<!DOCTYPE html>
<html lang="en">

@php
    $ASSET_VER = config('app.ASSET_VER');
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @isset($meta_title)
        <title>{{ $meta_title }}</title>
    @else
        <title>{{ env('APP_NAME') }}</title>
    @endisset

    @isset($meta_description)
        <meta name="description" content="{{ $meta_description }}">
    @endisset

    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') . '?v=' . $ASSET_VER }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') . '?v=' . $ASSET_VER }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/responsive.css') . '?v=' . $ASSET_VER }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome.min.css') . '?v=' . $ASSET_VER }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/owl.carousel.min.css') . '?v=' . $ASSET_VER }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/owl.theme.default.min.css') . '?v=' . $ASSET_VER }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/aos.css') . '?v=' . $ASSET_VER }}" />
</head>

<body>
    @include('includes.topbar')

    @yield('content')

    @include('includes.bottom-bar')

    <script src="{{ asset('js/jquery.min.js') . '?v=' . $ASSET_VER  }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.js') . '?v=' . $ASSET_VER  }}"></script>
    <script src="{{ asset('js/owl.carousel.js') . '?v=' . $ASSET_VER  }}"></script>
    <script src="{{ asset('js/aos.js') . '?v=' . $ASSET_VER  }}"></script>

    <script>
        AOS.init();

        $(window).scroll(function(){
            if($(this).scrollTop() > 100){
                $('.nav').addClass('sticky')
            } else{
                $('.nav').removeClass('sticky')
            }
        });
    </script>

    @stack('scripts')

</body>
</html>
