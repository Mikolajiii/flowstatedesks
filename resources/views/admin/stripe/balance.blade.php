
<x-admin-layout>
    <h1 class="text-2xl font-bold mb-4">Stripe Balance</h1>

    <div class="grid grid-cols-3 gap-4">
        <div class="p-4 bg-white shadow rounded">
            <h2 class="font-semibold">Available</h2>
            <p class="text-xl">${{ number_format($balance->available[0]->amount / 100, 2) }}</p>
</div>

<div class="p-4 bg-white shadow rounded">
    <h2 class="font-semibold">Pending</h2>
    <p class="text-xl">${{ number_format($balance->pending[0]->amount / 100, 2) }}</p>
</div>

<div class="p-4 bg-white shadow rounded">
    <h2 class="font-semibold">Reserved</h2>
    <p class="text-xl">${{ number_format($balance->instant_available[0]->amount / 100, 2) }}</p>
</div>
</div>
</x-admin-layout>
