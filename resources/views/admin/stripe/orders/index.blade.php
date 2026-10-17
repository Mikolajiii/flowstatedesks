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

            {{-- ⭐ NEW: Items column --}}
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

                $eventType = isset($payload['type'])
                    ? trim($payload['type'], '"')
                    : 'unknown';

                $session = $payload['data']['object'] ?? [];

                $amount = $session['amount_total'] ?? null;
                $currency = $session['currency'] ?? '-';

                $created = \Carbon\Carbon::parse($order->created_at)
                    ->format('F j, Y g:i A');

                // ⭐ Items decoded in controller
                $items = $order->items ?? [];
            @endphp

            <tr>
                <td class="px-3 py-2 text-left">{{ $order->id }}</td>

                <td class="px-3 py-2 text-left">{{ $eventType }}</td>

                <td class="px-3 py-2 text-left">
                    @php
                        $email = null;
                        $name  = null;

                        if (isset($session['customer_details'])) {
                            $email = $session['customer_details']['email'] ?? null;
                            $name  = $session['customer_details']['name'] ?? null;
                        }

                        if (!$email && isset($session['charges']['data'][0]['billing_details'])) {
                            $email = $session['charges']['data'][0]['billing_details']['email'] ?? null;
                            $name  = $session['charges']['data'][0]['billing_details']['name'] ?? null;
                        }

                        if (!$email && isset($session['billing_details'])) {
                            $email = $session['billing_details']['email'] ?? null;
                            $name  = $session['billing_details']['name'] ?? null;
                        }

                        if (!$email && isset($session['customer'])) {
                            $email = $session['customer'];
                        }
                    @endphp

                    {{ $name ?? $email ?? 'N/A' }}
                </td>

                <td class="px-3 py-2 text-left">
                    {{ $email ?? 'N/A' }}
                </td>

                {{-- ⭐ NEW: Items column --}}
                <td class="px-3 py-2 text-left">
                    @if (!empty($items))
                        @foreach ($items as $item)
                            <div>
                                {{ $item['name'] }}
                                x {{ $item['qty'] }}
                                — ${{ number_format($item['price'] / 100, 2) }}
                            </div>
                        @endforeach
                    @else
                        <span class="text-gray-500">N/A</span>
                    @endif
                </td>

                <td class="px-3 py-2 text-right">
                    @if(!is_null($amount))
                        ${{ number_format($amount / 100, 2) }}
                    @else
                        -
                    @endif
                </td>

                <td class="px-3 py-2 text-left">{{ $currency }}</td>

                <td class="px-3 py-2 text-left">{{ $created }}</td>

                <td class="px-3 py-2 text-left">
                    <a href="{{ route('admin.stripe.orders.show', $order->id) }}" class="text-blue-600">
                        View
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $orders->links() }}
@endsection
