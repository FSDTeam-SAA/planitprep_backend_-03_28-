<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
  <title>Login | Able Pro Dashboard Template</title>
  <!-- [Meta] -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="Able Pro is trending dashboard template made using Bootstrap 5 design framework. Able Pro is available in Bootstrap, React, CodeIgniter, Angular,  and .net Technologies.">
  <meta name="keywords" content="Bootstrap admin template, Dashboard UI Kit, Dashboard Template, Backend Panel, react dashboard, angular dashboard">
  <meta name="author" content="Phoenixcoded">

  <!-- [Favicon] icon -->
  <link rel="icon" href="{{ asset('admin/images/favicon.svg') }}" type="image/x-icon"> <!-- [Font] Family -->
  <link rel="stylesheet" href="{{ asset('admin/fonts/inter/inter.css') }}" id="main-font-link" />
  <!-- [Tabler Icons] https://tablericons.com -->
  <link rel="stylesheet" href="{{ asset('admin/fonts/tabler-icons.min.css') }}">
  <!-- [Feather Icons] https://feathericons.com -->
  <link rel="stylesheet" href="{{ asset('admin/fonts/feather.css') }}">
  <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
  <link rel="stylesheet" href="{{ asset('admin/fonts/fontawesome.css') }}">
  <!-- [Material Icons] https://fonts.google.com/icons -->
  <link rel="stylesheet" href="{{ asset('admin/fonts/material.css') }}">
  <!-- [Template CSS Files] -->
  <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" id="main-style-link">
  <link rel="stylesheet" href="{{ asset('admin/css/style-preset.css') }}">\
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-14K1GBX9FG"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-14K1GBX9FG');
  </script>
  <!-- WiserNotify -->
  <script>
    !(function() {
      if (window.t4hto4) console.log('WiserNotify pixel installed multiple time in this page');
      else {
        window.t4hto4 = !0;
        var t = document,
          e = window,
          n = function() {
            var e = t.createElement('script');
            (e.type = 'text/javascript'),
            (e.async = !0),
            (e.src = 'https://pt.wisernotify.com/pixel.js?ti=1jclj6jkfc4hhry'),
            document.body.appendChild(e);
          };
        'complete' === t.readyState ? n() : window.attachEvent ? e.attachEvent('onload', n) : e.addEventListener('load', n, !1);
      }
    })();
  </script>
  <!-- Microsoft clarity -->
  <script type="text/javascript">
    (function(c, l, a, r, i, t, y) {
      c[a] =
        c[a] ||
        function() {
          (c[a].q = c[a].q || []).push(arguments);
        };
      t = l.createElement(r);
      t.async = 1;
      t.src = 'https://www.clarity.ms/tag/' + i;
      y = l.getElementsByTagName(r)[0];
      y.parentNode.insertBefore(t, y);
    })(window, document, 'clarity', 'script', 'gkn6wuhrtb');
  </script>

</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-direction="ltr" data-pc-theme_contrast="" data-pc-theme="light">
  <!-- [ Pre-loader ] start -->
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>
  <!-- [ Pre-loader ] End -->

  <div class="auth-main">
    <div class="auth-wrapper v1">
      <div class="auth-form">
        <div class="card my-5">
          <div class="card-body">

            <h4 class="text-center f-w-500 mb-3">Login to {{ env('APP_NAME') }}</h4>

            <form class="was-validated mt-4" method="POST" action="{{ route('admin.login') }}">
              @csrf
              <div class="form-group mb-3">
                <input type="email" name="email" class="form-control" id="floatingInput" placeholder="Email Address" required autofocus>
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="form-group mb-3">
                <input type="password" class="form-control" name="password" id="floatingInput1" placeholder="Password" required>
                @error('password')
                <div class="invalid-feedback"> {{ $message }}</div>
                @enderror
              </div>
              <div class="d-flex mt-1 justify-content-between align-items-center">
                <div class="form-check" data-bs-toggle="tooltip" title="Select to remain logged in until you log out.">
                  <input class="form-check-input input-primary" type="checkbox" id="customCheckc1">
                  <label class="form-check-label text-muted" for="customCheckc1">Remember me</label>
                </div>
                {{-- <h6 class="text-secondary f-w-400 mb-0">Forgot Password?</h6> --}}
              </div>
              <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">Login</button>
              </div>
              {{-- <div class="d-flex justify-content-between align-items-end mt-4">
                <h6 class="f-w-500 mb-0">Don't have an Account?</h6>
                <a href="#" class="link-primary">Create Account</a>
              </div> --}}
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- [ Main Content ] end -->
  <!-- Required Js -->
  <script src="{{ asset('admin/js/plugins/popper.min.js') }}"></script>
  <script src="{{ asset('admin/js/plugins/simplebar.min.js') }}"></script>
  <script src="{{ asset('admin/js/plugins/bootstrap.min.js') }}"></script>
  <script src="{{ asset('admin/js/fonts/custom-font.js') }}"></script>
  <script src="{{ asset('admin/js/pcoded.js') }}"></script>
  <script src="{{ asset('admin/js/plugins/feather.min.js') }}"></script>


</body>
<!-- [Body] end -->

</html>
