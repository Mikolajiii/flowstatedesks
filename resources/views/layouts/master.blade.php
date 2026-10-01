

@include('partials.head_content')
<!-- css is included in headcontent-->
<body class="antialiased  w-screen mx-auto  ">
<!--<body class="antialiased w-full min-h-screen overflow-auto">-->

{{--@include('partials.navbarcontent')--}}

<div class=" w-full min-h-screen overflow-auto">
    @yield('content')
</div>

@include('partials.script_includes')
@include('partials.js_includes')




