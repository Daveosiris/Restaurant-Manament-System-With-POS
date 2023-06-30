<!doctype html>
<html lang="en" >

<head>

    <!--====== Required meta tags ======-->
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="@yield('meta-description')">
    <meta name="keywords" content="@yield('meta-keywords')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--====== Title ======-->
    <title>{{ $bs->website_title }}</title>

    <!--====== Favicon Icon ======-->
    <link rel="shortcut icon" href="{{ asset('assets/front/img/' . $bs->favicon) }}" type="image/png">


    <link rel="stylesheet" href="{{ asset('assets/front/css/plugin.min.css') }}">

    <!--====== Default css ======-->

    <link rel="stylesheet" href="{{ asset('assets/front/css/default.css') }}">

    <!--====== Style css ======-->
    <link rel="stylesheet" href="{{ asset('assets/front/css/style.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/front/css/styles.php?color=' . str_replace('#', '', $bs->base_color)) }}">
    @if ($rtl == 1)
        <link rel="stylesheet" href="{{ asset('assets/front/css/rtl.css') }}">
    @endif

    <!--====== jquery js ======-->
    <script src="{{ asset('assets/front/js/vendor/modernizr-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/front/js/vendor/jquery.3.2.1.min.js') }}"></script>

    <link rel="manifest" href="{{('manifest.json')}}">
</head>

<body>


      <!--    Error section start   -->
      <div class="error-container">
         <div>
            <div class="offline text-center">
               <img src="{{ asset('assets/front/img/static/offline.png') }}" alt="">
            </div>
            <div class="error-txt">
               <h2>{{__("Sorry, you're offline.")}}...</h2>
            </div>
         </div>
      </div>
      <!--    Error section end   -->
    
</body>

</html>