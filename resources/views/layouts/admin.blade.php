<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Kemenag Lampung</title>

    @vite(['resources/css/app.css','resources/js/app.js'])

</head>

<body class="bg-gray-100">

<div class="flex min-h-screen">

    @include('layouts.sidebar')

    <div class="flex-1">

        @include('layouts.navbar')

        <main class="p-8">

            @yield('content')

        </main>

    </div>

</div>

@stack('scripts')

</body>
</html>