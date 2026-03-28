<!DOCTYPE html>
<html lang="en">

@php
    $ASSET_VER = config('app.ASSET_VER');
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ env('APP_NAME') }}</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') . '?v=' . $ASSET_VER }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') . '?v=' . $ASSET_VER }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/responsive.css') . '?v=' . $ASSET_VER }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome.min.css') . '?v=' . $ASSET_VER }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/owl.carousel.min.css') . '?v=' . $ASSET_VER }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/owl.theme.default.min.css') . '?v=' . $ASSET_VER }}" />
</head>

<body class="backbg">
    @yield('content')

    <script src="{{ asset('js/jquery.min.js') . '?v=' . $ASSET_VER  }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.js') . '?v=' . $ASSET_VER  }}"></script>
    <script src="{{ asset('js/apexcharts.min.js') . '?v=' . $ASSET_VER  }}"></script>

    <script>
        (function () {
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

    @stack('scripts')

</body>
</html>
