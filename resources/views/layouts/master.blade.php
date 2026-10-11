@include('partials.head_content')

<body class="antialiased overflow-x-hidden"
    x-data="cartDrawer({{ json_encode(session('cart', [])) }})"
    @open-cart.window="open = true"
    x-init="
        rehydrate({{ json_encode(session('cart', [])) }});
        {{ session('cart_open') ? '$nextTick(() => open = true);' : '' }}
    "
    x-cloak
>





<div class="w-full min-h-screen overflow-auto">
    @yield('content')
</div>

@include('cart.drawer', ['cart' => session('cart', [])])

</body>

@include('partials.script_includes')
@include('partials.js_includes')
