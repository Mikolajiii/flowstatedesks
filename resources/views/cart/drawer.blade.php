<div
    x-show="open"
    x-cloak
    class="fixed inset-0 z-40 flex justify-end"
>

    <!-- Backdrop (fade in/out) -->
    <div
        x-show="open"
        class="fixed inset-0 bg-black/40 backdrop-blur-sm"
        @click="open = false"
        x-transition.opacity.duration.300ms
    ></div>

    <!-- Drawer Panel (slide in/out) -->
    <div
        x-show="open"
        x-cloak
        class="relative z-50 h-full w-full sm:w-[400px] bg-white shadow-2xl flex flex-col"

        x-init="rehydrate({{ json_encode($cart ?? []) }})"

        x-transition:enter="transform transition ease-[cubic-bezier(.25,.8,.25,1)] duration-500"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"

        x-transition:leave="transform transition ease-in duration-400"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
    >



    <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <form action="{{ route('cart.update') }}" method="POST">
                @csrf

                <div
                    x-show="Object.keys(items).length === 0"
                    class="text-center text-neutral-600 py-10"
                >
                    Your cart is empty.
                </div>

                <div x-show="Object.keys(items).length > 0">
                    <template x-for="(item, key) in items" :key="key">
                        <div class="flex items-center justify-between border-b pb-4">

                            <div class="flex items-center gap-4 w-full">
                                <img :src="item.image" class="w-16 h-16 rounded-lg object-cover">

                                <div class="flex flex-col">
                                    <span class="font-semibold text-neutral-900 text-lg" x-text="item.name"></span>
                                    <span class="text-neutral-600 text-sm">
                                        $<span x-text="(item.price / 100).toFixed(2)"></span>
                                    </span>
                                </div>

                                <div class="flex items-center pr-4">
                                    <form :action="'{{ url('/cart/remove') }}/' + key" method="POST">
                                        @csrf
                                        <button class="text-red-600 hover:text-red-800 mt-1">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 class="w-4 h-4"
                                                 fill="none"
                                                 viewBox="0 0 24 24"
                                                 stroke="currentColor"
                                                 stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">

                                <!-- ⭐ FIXED: decrement now calls decrement() -->
                                <button
                                    type="button"
                                    class="px-3 py-1 bg-neutral-200 rounded hover:bg-neutral-300"
                                    @click="decrement(key)"
                                >−</button>

                                <input
                                    type="number"
                                    :name="'qty[' + key + ']'"
                                    min="0"
                                    class="w-16 border rounded px-3 py-2 text-center"
                                    x-model="item.qty"
                                    @change="updateQty(key, item.qty)"
                                >

                                <!-- Plus button stays increment -->
                                <button
                                    type="button"
                                    class="px-3 py-1 bg-neutral-200 rounded hover:bg-neutral-300"
                                    @click="increment(key)"
                                >+</button>

                            </div>

                        </div>
                    </template>
                </div>

                <div class="mt-4 flex justify-end">
                    <button
                        type="submit"
                        class="px-4 py-2 bg-neutral-900 text-white rounded-lg text-sm font-semibold hover:bg-neutral-700"
                    >
                        Update Cart
                    </button>
                </div>
            </form>
        </div>

        <div class="p-6 border-t space-y-4">
            <div class="flex items-center justify-between text-lg font-semibold">
                <span>Subtotal</span>
                <span x-text="'$' + (subtotal / 100).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></span>

            </div>

            <button
                type="button"
                class="w-full px-6 py-3 bg-green-600 text-white rounded-lg text-xl font-semibold shadow hover:bg-green-700"
                @click="subtotal === 0 ? showEmptyPopup = true : window.location.href = '{{ route('checkout') }}'"

            >
                Checkout with Stripe
            </button>

            <button
                @click="open = false"
                class="w-full px-6 py-3 bg-neutral-200 text-neutral-900 rounded-lg text-lg font-semibold hover:bg-neutral-300"
            >
                Continue Shopping
            </button>
        </div>

        <div
            x-show="showEmptyPopup"
            x-transition
            class="fixed inset-0 bg-black/50 z-[999] flex items-center justify-center"
        >
            <div class="bg-white p-6 rounded-xl shadow-xl text-center w-64">
                <p class="text-lg font-semibold text-neutral-900 mb-4">Cart is Empty</p>
                <button
                    class="px-4 py-2 bg-neutral-900 text-white rounded-lg"
                    @click="showEmptyPopup = false"
                >
                    OK
                </button>
            </div>
        </div>
    </div>
</div>
