<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Crypto Dashboard</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100">
        <div id="app" class="container mx-auto p-4">
            <h1 class="text-3xl font-bold mb-8">Crypto Dashboard</h1>
            <crypto-ticker></crypto-ticker>
        </div>
    </body>
</html>
