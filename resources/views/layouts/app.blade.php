<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @vite([])
    <meta charset="utf-8">
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/classic/ckeditor.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="https://i.ibb.co/42k0sxg/47764316.png" />
    <meta name="description" content="Hi everyone ! Visit my website to see profile and my interesting projects.">
    <meta property="og:image" content="https://i.ibb.co/42k0sxg/47764316.png">
    <!-- change the tab title -->
    <title>{{ config('Wisnu', 'Wisnu Adi Wardhana') }}</title>
    <!-- Fonts -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <!-- Styles -->
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    @if (!Auth::guest())
        @include('inc.navbar')
    @endif
    <div class="container pt-3">
        @include('inc.messages')
        @yield('content')
    </div>
</body>

</html>
