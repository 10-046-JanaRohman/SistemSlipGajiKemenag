<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Kemenag Lampung</title>

    @vite(['resources/css/app.css','resources/js/app.js'])

</head>

<body class="bg-gray-100 h-screen overflow-hidden">

<div class="flex h-full">

    @include('layouts.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">

        @include('layouts.navbar')

        <main class="flex-1 p-8 overflow-y-auto">

            @yield('content')

        </main>

    </div>

</div>

@stack('scripts')

</body>
</html>