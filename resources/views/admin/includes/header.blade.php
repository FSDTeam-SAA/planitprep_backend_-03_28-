<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
@isset($meta_title)
    <title>{{ $meta_title }}</title>
@else
    <title>{{ env('APP_NAME') }}</title>
@endisset

@isset($meta_description)
    <meta name="description" content="{{ $meta_description }}">
@endisset
<link rel="icon" href="{{ asset('imgs/favicon.png') }}" type="image/x-icon">
<link rel="stylesheet" href="{{ asset('admin/fonts/inter/inter.css') }}" id="main-font-link" />
<link rel="stylesheet" href="{{ asset('admin/fonts/tabler-icons.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin/fonts/fontawesome.css') }}">
<link rel="stylesheet" href="{{ asset('admin/fonts/material.css') }}">
<link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" id="main-style-link">
<link rel="stylesheet" href="{{ asset('admin/css/main.css') }}" id="main-style-link">
<link rel="stylesheet" href="{{ asset('admin/css/style-preset.css') }}">
<link rel="stylesheet" href="{{ asset('admin/css/plugins/dataTables.bootstrap5.min.css') }}">
<meta name="_token" content="{{ csrf_token() }}" />
<link rel="stylesheet" href="{{ asset('admin/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin/css/bootstrap.min.css') }}">
@vite('resources/js/app.js')
