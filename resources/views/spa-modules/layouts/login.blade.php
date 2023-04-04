<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name'))</title>

    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600' rel='stylesheet' type='text/css'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css' rel='stylesheet'
          type='text/css'>

    <!-- CSS -->
    <link href="/css/sweetalert.css" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/notifications.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/settings.css') }}">
    @yield('style', '')
    <!-- Scripts -->
    @yield('scripts', '')

<!-- Global Spark Object -->
    <script>
        localStorage.clear();
        window.Spark = <?php echo json_encode(array_merge(
            App\Traits\ProvidesScriptVariables::scriptVariables(), []
        )); ?>;
    </script>
</head>
<body class="with-navbar">
{{--<div id="spark-app" v-cloak></div>--}}

@yield('content')
<!-- Scripts -->
@yield('scripts', '')
<!-- JavaScript -->
<script src="/js/sweetalert2.min.js"></script>
<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
