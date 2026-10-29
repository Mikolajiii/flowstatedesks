@extends('layouts.master')

@section('content')

    <h1 class="text-2xl font-bold mb-6">Stripe Orders</h1>

    <a href="{{ route('admin.stripe.dashboard') }}"
       class="inline-block mb-4 px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition">
        ← Back to Dashboard
    </a>

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
                $payload = json_decode($order->payload, true);

                $eventType = $payload['type'] ?? 'unknown';

                $created = \Carbon\Carbon::parse($order->created_at)
                    ->format('F j, Y g:i A');

                $items = $order->items ?? [];
            @endphp

            <tr class="border-t">

                <td class="px-3 py-2">
                    {{ $order->id }}
                </td>

                <td class="px-3 py-2">
                    {{ $eventType }}
                </td>

                <td class="px-3 py-2">
                    {{ $order->buyer_name ?? 'N/A' }}
                </td>

                <td class="px-3 py-2">
                    {{ $order->buyer_email ?? 'N/A' }}
                </td>

                <td class="px-3 py-2">
                    @if(count($items))

                        @foreach($items as $item)

                            <div>
                                {{ $item['name'] ?? 'Item' }}

                                @if(isset($item['qty']))
                                    × {{ $item['qty'] }}
                                @endif

                                @if(isset($item['price']))
                                    — ${{ number_format($item['price'] / 100, 2) }}
                                @endif
                            </div>

                        @endforeach

                    @else

                        <span class="text-gray-500">
                        N/A
                    </span>

                    @endif
                </td>

                <td class="px-3 py-2 text-right">

                    @if(!is_null($order->amount))

                        ${{ number_format($order->amount / 100, 2) }}

                    @else

                        -

                    @endif

                </td>

                <td class="px-3 py-2">
                    {{ $order->currency ?: '-' }}
                </td>

                <td class="px-3 py-2">
                    {{ $created }}
                </td>

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

    <div class="mt-6">
        {{ $orders->links() }}
    </div>

@endsection
