<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- FAVICONS -->
    <link rel="icon" href="{{ asset('favicos/favicon.ico') }}" type="image/x-icon" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title -->
    <title>{{ config('app.name', 'Scribblr') }}</title>

    <!-- csrf token for js -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
    <!-- application root -->
    <scribblr-ng></scribblr-ng>

    <!-- Scripts -->
    <script type="text/javascript" src="{{ asset('client-app/polyfills.bundle.js') }}"></script>
    <script type="text/javascript" src="{{ asset('client-app/main.bundle.js') }}"></script>
    <script type="text/javascript" src="{{ asset('client-app/styles.bundle.js') }}"></script>
    <script type="text/javascript" src="{{ asset('client-app/vendor.bundle.js') }}"></script>
    <script type="text/javascript" src="{{ asset('client-app/inline.bundle.js') }}"></script>
</body>
</html>
