
@extends('layouts.master')

@section('content')

    <h1 class="text-2xl font-bold mb-6">Stripe Orders</h1>

    <a href="{{ route('admin.stripe.dashboard') }}"
       class="inline-block mb-4 px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition">
        ← Back to Dashboard
    </a>

    <div class="overflow-x-auto">

        <table class="table-auto w-full border">

            <thead class="bg-gray-100">
            <tr>
                <th class="px-3 py-2 text-left">ID</th>
                <th class="px-3 py-2 text-left">Event</th>
                <th class="px-3 py-2 text-left">Customer</th>
                <th class="px-3 py-2 text-left">Email</th>
                <th class="px-3 py-2 text-left">Items</th>
                <th class="px-3 py-2 text-right">Amount</th>
                <th class="px-3 py-2 text-left">Currency</th>
                <th class="px-3 py-2 text-left">Created</th>
                <th class="px-3 py-2 text-left"></th>
            </tr>
            </thead>

            <tbody>

            @foreach ($orders as $order)

                @php

                    // Original webhook payload
                    $payload = is_array($order->payload)
                        ? $order->payload
                        : json_decode($order->payload ?? '{}', true);

                    $payload = is_array($payload) ? $payload : [];

                    $eventType = $payload['type']
                        ?? $order->name
                        ?? 'unknown';

                    // Created date
                    $created = \Carbon\Carbon::parse($order->created_at)
                        ->format('F j, Y g:i A');

                    // Cart items
                    $items = $order->items ?? [];

                    $items = is_array($items) ? $items : [];

                @endphp

                <tr class="border-t hover:bg-gray-50">

                    {{-- ID --}}
                    <td class="px-3 py-2">
                        {{ $order->id }}
                    </td>

                    {{-- Event --}}
                    <td class="px-3 py-2">
                        {{ $eventType }}
                    </td>

                    {{-- Customer --}}
                    <td class="px-3 py-2">
                        {{ $order->buyer_name ?? 'N/A' }}
                    </td>

                    {{-- Email --}}
                    <td class="px-3 py-2">
                        {{ $order->buyer_email ?? 'N/A' }}
                    </td>

                    {{-- Items --}}
                    <td class="px-3 py-2">

                        @forelse ($items as $item)

                            @php

                                $description = $item['description']
                                    ?? $item['name']
                                    ?? 'Item';

                                $quantity = $item['quantity']
                                    ?? $item['qty']
                                    ?? 1;

                                $itemTotal = $item['amount_total']
                                    ?? $item['price']
                                    ?? null;

                                $itemCurrency = strtoupper(
                                    $item['currency']
                                        ?? $order->currency
                                        ?? ''
                                );

                            @endphp

                            <div class="mb-2">

                                <div class="font-medium">
                                    {{ $description }}
                                </div>

                                <div class="text-sm text-gray-600">

                                    Quantity: {{ $quantity }}

                                    @if (!is_null($itemTotal))
                                        <span class="ml-2">
                                                {{ $itemCurrency }}
                                            {{ number_format($itemTotal / 100, 2) }}
                                            </span>
                                    @endif

                                </div>

                            </div>

                        @empty

                            <span class="text-gray-500">
                                    N/A
                                </span>

                        @endforelse

                    </td>

                    {{-- Total Amount --}}
                    <td class="px-3 py-2 text-right font-medium">

                        @if (!is_null($order->amount))

                            ${{ number_format($order->amount / 100, 2) }}

                        @else

                            -

                        @endif

                    </td>

                    {{-- Currency --}}
                    <td class="px-3 py-2">
                        {{ $order->currency ?: '-' }}
                    </td>

                    {{-- Created --}}
                    <td class="px-3 py-2 whitespace-nowrap">
                        {{ $created }}
                    </td>

                    {{-- View --}}
                    <td class="px-3 py-2">

                        <a href="{{ route('admin.stripe.orders.show', $order->id) }}"
                           class="text-blue-600 hover:underline">
                            View
                        </a>

                    </td>

                </tr>

            @endforeach

            </tbody>

        </table>

    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $orders->links() }}
    </div>

@endsection
