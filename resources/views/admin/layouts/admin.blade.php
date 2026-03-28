<!DOCTYPE html>
<html lang="en">

<head>
    @include('admin.includes.header')
</head>


<body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-direction="ltr" data-pc-theme_contrast=""
    data-pc-theme="light">
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <input type="hidden" value="{!! route('ckeditor.upload') !!}" id="uploadURL" />

    @include('admin.includes.sidebar')
    @include('admin.includes.topbar')

    @yield('content')

    @include('admin.includes.footer')
</body>

</html>
