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
            <th class="px-3 py-2 text-left">Product</th>
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

                // Real Stripe event type (remove extra quotes)
                $eventType = isset($payload['type'])
                    ? trim($payload['type'], '"')
                    : 'unknown';

                $session = $payload['data']['object'] ?? [];

                $amount = $session['amount_total'] ?? null;
                $currency = $session['currency'] ?? '-';

                // Format created_at (string → Carbon)
                $created = \Carbon\Carbon::parse($order->created_at)
                    ->format('F j, Y g:i A');
            @endphp

            <tr>
                <td class="px-3 py-2 text-left">{{ $order->id }}</td>

                <td class="px-3 py-2 text-left">{{ $eventType }}</td>

                <td class="px-3 py-2 text-left">
                    @php
                        $email = null;
                        $name  = null;

                        // 1. Checkout Session (customer_details)
                        if (isset($session['customer_details'])) {
                            $email = $session['customer_details']['email'] ?? null;
                            $name  = $session['customer_details']['name'] ?? null;
                        }

                        // 2. Payment Intent → charges array
                        if (!$email && isset($session['charges']['data'][0]['billing_details'])) {
                            $email = $session['charges']['data'][0]['billing_details']['email'] ?? null;
                            $name  = $session['charges']['data'][0]['billing_details']['name'] ?? null;
                        }

                        // 3. Charge Succeeded → billing_details directly
                        if (!$email && isset($session['billing_details'])) {
                            $email = $session['billing_details']['email'] ?? null;
                            $name  = $session['billing_details']['name'] ?? null;
                        }

                        // 4. FINAL FALLBACK → show customer ID instead of N/A
                        if (!$email && isset($session['customer'])) {
                            $email = $session['customer']; // e.g., cus_XXXX
                        }
                    @endphp

                    {{ $name ?? $email ?? 'N/A' }}
                </td>

                <td class="px-3 py-2 text-left">
                    {{ $email ?? 'N/A' }}
                </td>


                <td class="px-3 py-2 text-left">
                    {{ $order->description ?? 'N/A' }}

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
