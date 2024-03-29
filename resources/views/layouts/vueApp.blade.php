<?php

header('Access-Control-Allow-Origin: *');
//header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
?>

    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui, minimum-scale=1.0">
    <!-- CSRF Token -->
    <meta id="csrf-token" name="csrf-token" content="{{ csrf_token() }}">
    @guest
        <meta id="loggedUserId" content="0">
    @else
        <meta id="loggedUserId" content="{{ Auth::id() }}">
    @endguest
    <title>{{ config('app.name', 'Auther') }}</title>
    <script>var baseUrl = "/";</script>
    <!-- Scripts -->
    @yield('header-before-js')
    <script src="js/app.js" defer></script>

@yield('header')

<!-- Styles -->
    <link href="css/app.css" rel="stylesheet">
</head>
<body class="">
<div id="app" v-cloak>
    <thesidebar v-bind:alertshown="alertshown" v-bind:mixconfig="mixconfig" v-bind:alerttext="alerttext"
                v-bind:alertcolor="alertcolor"></thesidebar>
    <v-flex xs12 sm12 md8 lg7 class="mx-auto mt-5">
        <main>

            <v-container fluid class="" id="outerContainer">
                @yield('content')
            </v-container>

        </main>
    </v-flex>
</div>
</body>
<footer>

</footer>
</html>
