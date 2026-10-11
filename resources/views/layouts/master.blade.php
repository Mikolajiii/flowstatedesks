@include('partials.head_content')

<body
    class="antialiased w-screen mx-auto"
    x-data="cartDrawer({{ json_encode(session('cart', [])) }})"
    @open-cart.window="open = true"
    x-init="
        rehydrate({{ json_encode(session('cart', [])) }});
       @if(session('cart_open'))
    $nextTick(() => open = true);
@endif

    "
    x-cloak
>


<header class="flex items-center justify-center px-6 py-4 border-b">

    <div class="text-xl font-bold">FlowState</div>

    <button
        @click="open = true"
        class="relative flex items-center gap-2 text-neutral-900 hover:text-neutral-700"
    >
        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-6 h-6"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor"
             stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 3h2l3 12h10l3-8H6" />
        </svg>

        <span>Cart</span>

        <span
            x-show="badgeCount > 0"
            x-text="badgeCount"
            class="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5"
        ></span>
    </button>
</header>

<div class="w-full min-h-screen overflow-auto">
    @yield('content')
</div>

@include('cart.drawer', ['cart' => session('cart', [])])

</body>

@include('partials.script_includes')
@include('partials.js_includes')
