@extends('layouts.master')

@section('content')
    <div x-data></div>

    <!-- ⭐ MUST BE HERE -->

    <!-- HERO SECTION -->
    <section
        class=" relative w-full h-[80vh] bg-cover bg-center bg-no-repeat"
        style="background-image: url('{{ Vite::asset('resources/images/other_photos/hero_section.webp') }}');"
    >

        <!-- Dark overlay for readability -->
        <div class="absolute inset-0 bg-black/25"></div>

        <section >
            <div class="max-w-6xl mx-auto">

                <div class=" -mt-10 flex justify-center items-center text-xl font-semibold tracking-wide pt-6 relative z-20">

                    <!-- LEFT SIDE LINKS -->
                    <div class="flex items-center gap-12">
                        <a href="#FlowState_Pro"
                           class="text-white transition-colors duration-200 hover:text-purple-500">
                            Flow Pro
                        </a>
                        <a href="#FlowState_Mini"
                           class="text-white transition-colors duration-200 hover:text-purple-500">
                            Flow Mini
                        </a>


                    </div>

                    <!-- CENTER LOGO -->
                    <div class="px-10">

                        <img
                            src="{{ Vite::asset('resources/images/other_photos/flowstate_logo.png') }}"
                            alt="FlowState Logo"
                            class="w-52 md:w-72 drop-shadow-xl"
                        />
                    </div>

                    <!-- RIGHT SIDE LINKS -->
                    <div class="flex items-center gap-12">
                        <a href="#FlowState_SideCar"
                           class="text-white transition-colors duration-200 hover:text-purple-500">
                            Flow Side Car
                        </a>


                        <a href="#About"
                           class="text-white transition-colors duration-200 hover:text-purple-500">
                            About
                        </a>
                    </div>

                </div>

            </div>
        </section>
        <!-- LOGO ABOVE TEXT -->



        <!-- HERO CONTENT -->
        <div class="relative z-10 max-w-4xl px-8 flex flex-row justify-center items-center translate-x-32">


            <!-- LEFT COLUMN (your content) -->
            <div class="mt-4 w-1/2">
                <h1 class="text-5xl md:text-6xl font-semibold leading-tight text-white drop-shadow-lg">
                    A studio desk built<br>
                    for modern creators.
                </h1>

                <p class="text-neutral-200 max-w-xl mt-6 text-lg drop-shadow">
                    A clean, ergonomic workspace designed for producers, designers, and artists who demand clarity,
                    comfort, and room for inspiration.
                </p>
            </div>

            <!-- RIGHT COLUMN (empty on purpose) -->
            <div class="w-1/2">
                <!-- intentionally left empty -->
            </div>

        </div>

    </section>



    <!-- FlowState Pro -->
    <section id="FlowState_Pro" class="bg-white">
        <div class="w-full px-6 py-16">

            <div class="flex flex-col lg:flex-row gap-12 items-start">

                <!-- Product image + gallery -->
                <div class=" mt-20 flex-1 space-y-4">

                    <!-- Main Image -->
                    <div class="rounded-3xl overflow-hidden h-[500px] relative">

                        <img
                            alt="FlowState Pro"
                            src="{{ Vite::asset('resources/images/Product_photos/flowstate_pro/1_FLOWPRO_EDITED.webp') }}"
                            class="w-full h-full object-cover"
                            id="pro-main-image"
                        />

                        <button id="pro-zoom-btn"
                                class="absolute top-4 right-4 bg-neutral-900/70 text-white p-2 rounded-full hover:bg-neutral-900 transition shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="w-6 h-6"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="white"
                                 stroke-width="2">
                                <circle cx="11" cy="11" r="7"/>
                                <line x1="16" y1="16" x2="21" y2="21"/>
                            </svg>
                        </button>




                    </div>

                    <!-- Thumbnails -->
                    <div class="grid grid-cols-8 gap-3">
                        <img alt="FlowState Pro" src="{{ Vite::asset('resources/images/Product_photos/flowstate_pro/1_FLOWPRO_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                        <img alt="FlowState Pro" src="{{ Vite::asset('resources/images/Product_photos/flowstate_pro/2_FLOWPRO_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                        <img alt="FlowState Pro" src="{{ Vite::asset('resources/images/Product_photos/flowstate_pro/3_FLOWPRO_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                        <img alt="FlowState Pro" src="{{ Vite::asset('resources/images/Product_photos/flowstate_pro/4_FLOWPRO_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                        <img alt="FlowState Pro" src="{{ Vite::asset('resources/images/Product_photos/flowstate_pro/5_FLOWPRO_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                        <img alt="FlowState Pro" src="{{ Vite::asset('resources/images/Product_photos/flowstate_pro/6_FLOWPRO_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                        <img alt="FlowState Pro" src="{{ Vite::asset('resources/images/Product_photos/flowstate_pro/7_FLOWPRO_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                        <img alt="FlowState Pro" src="{{ Vite::asset('resources/images/Product_photos/flowstate_pro/8_FLOWPRO_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                    </div>


                </div>




                <!-- Product details FlowState Pro-->
                <div class="flex-1 space-y-6">

                    <div class="space-y-2">


                        <h2 class="text-3xl md:text-4xl font-semibold text-neutral-900">
                            Flow Pro
                        </h2>


                        <p class="my-4">Whether you're building your first home studio or upgrading a professional production room, this desk is designed to put the tools of your craft exactly where you need them, in front of you. From recording bands, to mixing, mastering, or creating music, this desk gives your studio a dedicated command center.</p>
                        <h2 class="text-3xl md:text-3xl font-semibold text-neutral-900">Built for Creativity</h2>

                        <p class="mt-4">With 18U of rack space, the Flow Pro can hold your essential outboard gear at your fingertips, giving you quick access to the controls you want without having to disrupt your creative flow.</p>

                        <p class="mt-4">The open workspace provides room for your controllers, keyboard, and mouse. With three evenly positioned cutouts you can pass your cables down to your computer or audio interface, making cable management easier and more effective.
                            Equipped with a sliding keyboard tray that can hold 75lbs of weight, it can handle several MIDI controllers or synths that you wish to store.</p>



                    </div>

                    <div class="flex items-center gap-4">
          <span class="text-xl font-semibold text-neutral-900">
            $1,499
          </span>

                    </div>

                    <div class="space-y-3">
                        <h3 class="font-medium text-lg text-neutral-900">
                            Specifications
                        </h3>
                        <p>Wood – Pre-lacquered Baltic birch plywood</p>
                        <p>Hardware – German Engineered Rampa Inserts and Bolts</p>
                        <p> Keyboard Tray Sliders – Lee Valley (75 lbs.) Fully extendable Sliders</p>
                        <P>Rails – SPCC Steel with black powder coating</p>

                        <div class="grid grid-cols-2 gap-4 text-md text-neutral-900">
                            <div>
                                <p class="font-medium text-neutral-900">Dimensions</p>
                                <P>Length - 60 1/4"</P>
                                <P> Width - 33"</P>
                                <P>Height - 32 3/4" (top tier)</P>
                                <P>Height - 29" (lower tier)</P>

                                <div class="mt-10">
                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product" value="pro">

                                        <label class="block mb-2 text-sm text-neutral-700">Quantity</label>
                                        <input type="number" name="qty" value="1" min="1"
                                               class="border rounded px-3 py-2 mb-3 w-24">

                                        <button
                                            type="submit"
                                            class="px-6 py-3 rounded-full bg-neutral-900 text-white text-sm font-semibold hover:bg-neutral-700 transition shadow-md"
                                        >
                                            Add to Cart
                                        </button>




                                    </form>
                                    <button
                                        @click="$dispatch('open-cart')"
                                        class="flex items-center gap-2 text-neutral-900 hover:text-neutral-700"
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
                                        View Cart
                                    </button>
                                </div>
                                    <span class="mt-3 inline-flex items-center justify-center  text-purple-500
             text-md font-medium px-2 py-0.5 rounded-full">
    In stock — Free Shipping
</span>
                                </div>
                            </div>
                        </div>





                    </div> <!-- closes .flex flex-col lg:flex-row -->

                </div> <!-- closes .w-full px-6 py-16 -->
            </div>



    </section>

    <!-- FlowState Mini -->
    <section id="FlowState_Mini" class="bg-white">
        <div class="w-full px-6 py-16">

            <div class="flex flex-col lg:flex-row gap-12 items-start">

                <!-- Product image + gallery -->
                <div class="flex-1 space-y-4">

                    <!-- Main Image -->
                    <div class="rounded-3xl overflow-hidden h-[500px] relative">


                        <img
                            alt="FlowState Mini"
                            src="{{ Vite::asset('resources/images/Product_photos/flowstate_mini/1_FLOWMINI_EDITED.webp') }}"
                            class="w-full h-full object-cover"
                            id="mini-main-image"
                        />


                        <button id="mini-zoom-btn"
                                class="absolute top-4 right-4 bg-neutral-900/70 text-white p-2 rounded-full hover:bg-neutral-900 transition shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="w-6 h-6"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="white"
                                 stroke-width="2">
                                <circle cx="11" cy="11" r="7"/>
                                <line x1="16" y1="16" x2="21" y2="21"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Thumbnails -->
                    <div class="grid grid-cols-8 gap-3">
                        <img alt="FlowState Mini" src="{{ Vite::asset('resources/images/Product_photos/flowstate_mini/1_FLOWMINI_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                        <img alt="FlowState Mini" src="{{ Vite::asset('resources/images/Product_photos/flowstate_mini/2_FLOWMINI_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                        <img alt="FlowState Mini" src="{{ Vite::asset('resources/images/Product_photos/flowstate_mini/3_FLOWMINI_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                        <img alt="FlowState Mini" src="{{ Vite::asset('resources/images/Product_photos/flowstate_mini/4_FLOWMINI_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                        <img alt="FlowState Mini" src="{{ Vite::asset('resources/images/Product_photos/flowstate_mini/5_FLOWMINI_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                        <img alt="FlowState Mini" src="{{ Vite::asset('resources/images/Product_photos/flowstate_mini/6_FLOWMINI_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                        <img alt="FlowState Mini" src="{{ Vite::asset('resources/images/Product_photos/flowstate_mini/7_FLOWMINI_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                        <img alt="FlowState Mini" src="{{ Vite::asset('resources/images/Product_photos/flowstate_mini/8_FLOWMINI_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                    </div>


                </div>

                <!-- Product details FlowState Mini -->
                <div class="flex-1 space-y-6">

                    <div class="space-y-2">


                        <h2 class="text-3xl md:text-4xl font-semibold text-neutral-900">
                            Flow Mini
                        </h2>

                        <p class="text-neutral-900">
                            When space is at a premium, the Flow Mini has got you covered. Ideal for the creator who may be working in a condo or apartment, sharing a creative space, or simply wants to maximize the room they have available.
                        <h2 class="text-3xl md:text-3xl font-semibold text-neutral-900">Small Footprint</h2>
                        Perfect for when you are working out of a smaller studio, the Flow Mini has 12U of rack space for your essential outboard gear to be at your fingertips.
                        <p></p>
                        At just 40 ½” in length, the Flow Mini can fit into smaller studios with ease while still being a hub for creativity.
                        <p></p>
                        The Flow Mini has a workspace that is big enough for your keyboard and mouse and a cutout for your cable management. The keyboard tray is rated at 75lbs and can easily hold a synth or MIDI controller.


                    </div>

                    <div class="flex items-center gap-4">
                    <span class="text-xl font-semibold text-neutral-900">
                        $1,099
                    </span>

                    </div>

                    <div class="space-y-3">
                        <h3 class="font-medium text-lg text-neutral-900">
                            Specifications
                        </h3>
                        <p>Wood – Pre-lacquered Baltic birch plywood</p>
                        <p>Hardware – German Engineered Rampa Inserts and Bolts</p>
                        <p> Keyboard Tray Sliders – Lee Valley (75 lbs.) Fully extendable Sliders</p>
                        <P>Rails – SPCC Steel with black powder coating</p>

                        <div class="grid grid-cols-2 gap-4 text-lg text-neutral-900">
                            <div>
                                <p class="font-medium text-neutral-900">Dimensions</p>
                                <p>Length - 40 1/2"</p>
                                <p>Width - 33"
                                <p>Height - 32 3/4" (top tier)</p>
                                <p>Height - 29" (lower tier)</p>
                                <div class="mt-10">
                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product" value="mini">

                                        <label class="block mb-2 text-sm text-neutral-700">Quantity</label>
                                        <input type="number" name="qty" value="1" min="1"
                                               class="border rounded px-3 py-2 mb-3 w-24">

                                        <button
                                            type="submit"
                                            class="px-6 py-3 rounded-full bg-neutral-900 text-white text-sm font-semibold hover:bg-neutral-700 transition shadow-md"
                                        >
                                            Add to Cart
                                        </button>



                                    </form>

                                    <button
                                        @click="$dispatch('open-cart')"
                                        class="flex items-center gap-2 text-neutral-900 hover:text-neutral-700"
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
                                        View Cart
                                    </button>
                                </div>
                                    <span class="mt-3 inline-flex items-center justify-center  text-purple-500
             text-md font-medium px-2 py-0.5 rounded-full">
    In stock — Free Shipping
</span>
                                </div>
                            </div>

                            <!--<div>
                                <p class="font-medium text-neutral-900">Materials</p>
                                <p>Solid birch or oak top</p>
                                <p>Powder‑coated steel frame</p>
                                <p>Integrated cable channel</p>
                            </div>-->


                        </div>
                    </div>

                </div> <!-- closes .flex-1 space-y-6 -->

            </div> <!-- closes .flex flex-col lg:flex-row -->

        </div> <!-- closes .w-full px-6 py-16 -->
    </section>
    <!-- Product: FlowState Sidecar -->
    <section id="FlowState_SideCar" class="bg-white">
        <div class="w-full px-6 py-16">

            <div class="flex flex-col lg:flex-row gap-12 items-start">


                <!-- Product image + gallery -->
                <div class="flex-1 space-y-4">

                    <!-- Main Image -->
                    <div class="rounded-3xl overflow-hidden h-[500px] relative">
                        <img
                            alt="side car"
                            src="{{ Vite::asset('resources/images/Product_photos/flowstate_sidecar/1_RACK_EDITED.webp') }}"
                            class="w-full h-full object-cover"
                            id="sidecar-main-image"
                        />


                        <button id="sidecar-zoom-btn"
                                class="absolute top-4 right-4 bg-neutral-900/70 text-white p-2 rounded-full hover:bg-neutral-900 transition shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="w-6 h-6"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="white"
                                 stroke-width="2">
                                <circle cx="11" cy="11" r="7"/>
                                <line x1="16" y1="16" x2="21" y2="21"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Thumbnails -->
                    <div class="grid grid-cols-8 gap-3">
                        <img alt="sidecar" src="{{ Vite::asset('resources/images/Product_photos/flowstate_sidecar/1_RACK_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                        <img alt="sidecar" src="{{ Vite::asset('resources/images/Product_photos/flowstate_sidecar/2_RACK_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                        <img alt="sidecar" src="{{ Vite::asset('resources/images/Product_photos/flowstate_sidecar/3_RACK_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                        <img alt="sidecar" src="{{ Vite::asset('resources/images/Product_photos/flowstate_sidecar/4_RACK_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                        <img alt="sidecar" src="{{ Vite::asset('resources/images/Product_photos/flowstate_sidecar/5_RACK_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                        <img alt="sidecar" src="{{ Vite::asset('resources/images/Product_photos/flowstate_sidecar/6_RACK_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                        <img alt="sidecar" src="{{ Vite::asset('resources/images/Product_photos/flowstate_sidecar/7_RACK_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                        <img alt="sidecar" src="{{ Vite::asset('resources/images/Product_photos/flowstate_sidecar/8_RACK_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                        <img alt="sidecar" src="{{ Vite::asset('resources/images/Product_photos/flowstate_sidecar/9_RACK_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                        <img alt="sidecar" src="{{ Vite::asset('resources/images/Product_photos/flowstate_sidecar/10_RACK_EDITED.webp') }}" class="thumb rounded-xl object-cover h-20">
                    </div>




                </div>




                <!-- Product details FlowState SideCar-->

                <div class="flex-1 space-y-6">

                    <div class="space-y-2">


                        <h2 class="text-3xl md:text-4xl font-semibold text-neutral-900">
                            Flow SideCar
                        </h2>

                        <p class="text-neutral-900">
                            Giving you additional space for rack equipment, controllers, keyboards, or other hardware without requiring you to replace your existing desk with something substantially larger, the Flow Side Car is a great addition to any studio. The top of the Flow Side car is also the exact height as the Flow Pro and Flow Mini’s workspace so you can easily align them for extra desk space.
                        <h2 class="text-3xl md:text-3xl font-semibold text-neutral-900"> Studio Function Meets Furniture Design</h2>

                        <p> A lot of professional studio furniture is highly functional but visually industrial—black racks, and metal frames. We wanted to create something that could hold its own in a home, condo, apartment, or creative space while still feeling like it belongs in a serious studio.
                        <p></p>
                        Each Flow Side Car as a natural rattan installed by hand to give it a warmer, more open look.


                    </div>

                    <div class="flex items-center gap-4">
          <span class="text-xl font-semibold text-neutral-900">
            $449.00
          </span>

                    </div>

                    <div class="space-y-3">
                        <h3 class="font-medium text-lg text-neutral-900">
                            Specifications
                        </h3>
                        <p>Wood – Pre-lacquered Baltic birch plywood</p>
                        <p>Hardware – German Engineered Rampa Inserts and Bolts</p>
                        <p> Keyboard Tray Sliders – Lee Valley (75 lbs.) Fully extendable Sliders</p>
                        <P>Rails – SPCC Steel with black powder coating</p>
                        <div class="grid grid-cols-2 gap-4 text-lg text-neutral-700">
                            <div>
                                <p class="font-medium text-neutral-900">Dimensions</p>

                                <p> Length - 20 1/2"</p>
                                <p>Width – 12 1/2" (top)</p>
                                <p>Width – 16 ¾” (bottom)</p>
                                <p>Height - 29"</p>

                                <div class="mt-10">
                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product" value="sidecar">

                                        <label class="block mb-2 text-sm text-neutral-700">Quantity</label>
                                        <input type="number" name="qty" value="1" min="1"
                                               class="border rounded px-3 py-2 mb-3 w-24">

                                        <button
                                            type="submit"
                                            class="px-6 py-3 rounded-full bg-neutral-900 text-white text-sm font-semibold hover:bg-neutral-700 transition shadow-md"
                                        >
                                            Add to Cart
                                        </button>



                                    </form>
                                    <button
                                        @click="$dispatch('open-cart')"
                                        class="flex items-center gap-2 text-neutral-900 hover:text-neutral-700"
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
                                        View Cart
                                    </button>
                                </div>
                                    <span class="mt-3 inline-flex items-center justify-center  text-purple-500
             text-md font-medium px-2 py-0.5 rounded-full">
    In stock — Free Shipping
</span>
                                </div>
                            </div>


                    </div> <!-- closes .flex-1 space-y-6 -->

                    </div> <!-- closes .flex flex-col lg:flex-row -->

                </div> <!-- closes .max-w-7xl mx-auto -->
            </div>

    </section>
    <!-- ABOUT SECTION -->
    <section id="About" class="w-full bg-white py-20  border-neutral-200">
        <div class="max-w-4xl mx-auto px-6 text-center">

            <h2 class="text-4xl font-semibold text-neutral-900 mb-6">
                About FlowState
            </h2>

            <p class="mt-3 text-neutral-900 leading-relaxed text-lg font-semibold">
                Furniture that Moves with Your Career</p>

            <h2 class="text-neutral-900 mt-3 leading-relaxed text-md font-semibold">Your studio will evolve</h2>
            <p class="mt-3"></p>

            <p class="text-neutral-900 leading-relaxed text-md font-semibold ">Your desk should be built to evolve with it.</p>
        </div>
        <div class="max-w-4xl mx-auto px-6 mt-6 text-left ">
            <p class="mt-3 text-left"> When we designed our products at Flow State, we wanted to design furniture that would be functional, durable, be able to be disassembled and reassembled as many times as possible and look well… cool.</p>
            <p class="mt-3">Our products are constructed from pre-lacquered Baltic birch plywood, chosen for its strength, stability, and durability. But the material itself is only part of the solution. At the connection points, we use German-engineered Rampa threaded inserts, creating a durable mechanical connection that allows the desk to be assembled and disassembled repeatedly without compromising the integrity of the wood.</p>
            <p class="mt-3">That means your studio can change without your furniture becoming disposable.</p>
            <p class="mt-3">The goal wasn't simply to make furniture that looks good when it's assembled. The goal was to create studio furniture that could remain part of your setup for years, through multiple moves and countless reconfigurations.</p>

        </div>


    </section>
    <!-- testimonials section-->
    <section>
        <section id="testonials" class="w-full bg-white py-20  border-neutral-200">
            <div class="max-w-4xl mx-auto px-6 text-center">

                <h2 class="text-4xl font-semibold text-neutral-900 mb-6">
                    Testimonials
                </h2>

                <p class="text-neutral-600 leading-relaxed text-lg">
                    Gavin Brown is a Juno Award-winning Canadian record producer, songwriter, and musician known for shaping the alternative rock and pop sounds of major international and Canadian acts, including Metric, Three Days Grace, and Billy Talent.
                </p>

            </div>
        </section>
    </section>

    <!-- Modal: SideCar -->

    <div id="sidecar-modal"
         class="fixed inset-0 bg-black/90 hidden z-50 flex items-center justify-center">

        <!-- Close Button -->
        <button id="sidecar-close"
                class="absolute top-6 right-6 text-white text-3xl font-light">
            ✕
        </button>

        <!-- Left Arrow -->
        <button id="sidecar-prev"
                class="absolute left-6 text-white text-4xl font-light">
            ‹
        </button>

        <!-- Centered Image Wrapper -->
        <div class="flex items-center justify-center w-full h-full pointer-events-none">
            <img alt="side car" id="sidecar-modal-img"
                 class="max-w-[90%] max-h-[90%] object-contain rounded-xl shadow-2xl pointer-events-auto">
        </div>

        <!-- Right Arrow -->
        <button id="sidecar-next"
                class="absolute right-6 text-white text-4xl font-light">
            ›
        </button>
    </div>



    <!-- Modal: FlowState Pro -->
    <div id="pro-modal"
         class="fixed inset-0 bg-black/90 hidden z-50 flex items-center justify-center">

        <button id="pro-close"
                class="absolute top-6 right-6 text-white text-3xl font-light">
            ✕
        </button>

        <button id="pro-prev"
                class="absolute left-6 text-white text-4xl font-light">
            ‹
        </button>

        <img alt="Flow Pro"   id="pro-modal-img"
             class="max-w-[90%] max-h-[90%] object-contain rounded-xl shadow-2xl">

        <button id="pro-next"
                class="absolute right-6 text-white text-4xl font-light">
            ›
        </button>
    </div>


    <!-- Modal: FlowState Mini -->

    <div id="mini-modal"
         class="fixed inset-0 bg-black/90 hidden z-50 flex items-center justify-center">

        <!-- Close Button -->
        <button id="mini-close"
                class="absolute top-6 right-6 text-white text-3xl font-light">
            ✕
        </button>

        <!-- Left Arrow -->
        <button id="mini-prev"
                class="absolute left-6 text-white text-4xl font-light">
            ‹
        </button>

        <!-- Image -->
        <img id="mini-modal-img"
             alt="FlowState Mini"
             class="max-w-[90%] max-h-[90%] object-contain rounded-xl shadow-2xl">

        <!-- Right Arrow -->
        <button id="mini-next"
                class="absolute right-6 text-white text-4xl font-light">
            ›
        </button>
    </div>



    {{--@include('cart.drawer')--}}



    @include('partials.footer_content')









    <script>
        document.addEventListener("DOMContentLoaded", () => {

            // List all gallery section IDs
            const galleries = ["FlowState_Pro", "FlowState_SideCar", "FlowState_Mini"];

            galleries.forEach(id => {
                const section = document.querySelector(`#${id}`);
                if (!section) return; // skip if section doesn't exist

                const mainImage = section.querySelector(".rounded-3xl img");
                const thumbs = Array.from(section.querySelectorAll(".grid img"));

                let index = 0;

                // Click main image → go to next thumbnail
                mainImage.addEventListener("click", () => {
                    index = (index + 1) % thumbs.length;
                    mainImage.src = thumbs[index].src;
                });

                // Click thumbnail → update main image
                thumbs.forEach((thumb, i) => {
                    thumb.addEventListener("click", () => {
                        index = i;
                        mainImage.src = thumb.src;
                    });
                });
            });

        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {

            function initGallery(sectionId, mainId, zoomId, modalId, modalImgId, prevId, nextId, closeId) {

                const section = document.querySelector(`#${sectionId}`);
                const mainImage = section.querySelector(`#${mainId}`);
                const thumbs = Array.from(section.querySelectorAll(".thumb"));

                let index = 0;

                thumbs.forEach((thumb, i) => {
                    thumb.addEventListener("click", () => {
                        index = i;
                        mainImage.src = thumb.src;
                    });
                });

                const modal = document.getElementById(modalId);
                const modalImg = document.getElementById(modalImgId);
                const zoomBtn = document.getElementById(zoomId);
                const closeBtn = document.getElementById(closeId);
                const prevBtn = document.getElementById(prevId);
                const nextBtn = document.getElementById(nextId);

                zoomBtn.addEventListener("click", () => {
                    modal.classList.remove("hidden");
                    modalImg.src = mainImage.src;
                });

                closeBtn.addEventListener("click", () => modal.classList.add("hidden"));

                modal.addEventListener("click", (e) => {
                    if (e.target === modal) modal.classList.add("hidden");
                });

                nextBtn.addEventListener("click", () => {
                    index = (index + 1) % thumbs.length;
                    modalImg.src = thumbs[index].src;
                });

                prevBtn.addEventListener("click", () => {
                    index = (index - 1 + thumbs.length) % thumbs.length;
                    modalImg.src = thumbs[index].src;
                });
            }

            initGallery("FlowState_SideCar", "sidecar-main-image", "sidecar-zoom-btn",
                "sidecar-modal", "sidecar-modal-img", "sidecar-prev", "sidecar-next", "sidecar-close");

            initGallery("FlowState_Pro", "pro-main-image", "pro-zoom-btn",
                "pro-modal", "pro-modal-img", "pro-prev", "pro-next", "pro-close");

            initGallery("FlowState_Mini", "mini-main-image", "mini-zoom-btn",
                "mini-modal", "mini-modal-img", "mini-prev", "mini-next", "mini-close");

        });
    </script>



@endsection
