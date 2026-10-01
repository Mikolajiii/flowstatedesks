<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSRF Token for Laravel -->
    <title>FlowStateDesks</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <!--<link rel="icon" href="{{'/assets/logos/showrooster_favicon_red_40x40.png'}}" type="image/png">-->


    @vite(['resources/css/app.css', 'resources/js/app.js'])



    @include('partials.css_includes')
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')


</head>

