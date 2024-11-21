<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Loopsacademy">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="theme-color" content="#0134d4">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">

  <!-- Title -->
  <title>@yield('title') | Loopsacademy</title>

  <!-- Favicon -->
  <link rel="icon" href="{{ asset('img/core-img/favicon.ico') }}">
  <link rel="apple-touch-icon" href="{{ asset('img/icons/icon-96x96.png') }}">
  <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('img/icons/icon-152x152.png') }}">
  <link rel="apple-touch-icon" sizes="167x167" href="{{ asset('img/icons/icon-167x167.png') }}">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/icons/icon-180x180.png') }}">
 <!-- Style CSS -->
 <link rel="stylesheet" href="{{ asset('css/style.css') }}">

 <!-- Web App Manifest -->
 <link rel="manifest" href="{{ asset('manifest.json') }}">

 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

 <link rel="stylesheet" href="{{ asset('css/my-style.css') }}">
</head>


<body>

@include('includes.header')

  <div class="page-content-wrapper">
    @yield('content')
  </div>

@include('includes.footer')

@yield('scripts')

  <!-- All JavaScript Files -->
  <script src="{{ asset('js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('js/slideToggle.min.js') }}"></script>
  <script src="{{ asset('js/internet-status.js') }}"></script>
  <script src="{{ asset('js/tiny-slider.js') }}"></script>
  <script src="{{ asset('js/venobox.min.js') }}"></script>
  <script src="{{ asset('js/countdown.js') }}"></script>
  <script src="{{ asset('js/rangeslider.min.js') }}"></script>
  <script src="{{ asset('js/vanilla-dataTables.min.js') }}"></script>
  <script src="{{ asset('js/index.js') }}"></script>
  <script src="{{ asset('js/imagesloaded.pkgd.min.js') }}"></script>
  <script src="{{ asset('js/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('js/dark-rtl.js') }}"></script>
  <script src="{{ asset('js/active.js') }}"></script>
  <script src="{{ asset('js/pwa.js') }}"></script>
</body>

</html>
