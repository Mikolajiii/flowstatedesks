@extends('layouts.master')

@section('content')

    <a href="{{ route('admin.stripe.dashboard') }}"
       class="inline-block mb-4 px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition">
        ← Back to Dashboard
    </a>

    <h1 class="text-xl font-bold mb-4">Order #{{ $hook->id }}</h1>

    @php
        $payload = $hook->payload;
        $session = $payload['data']['object'] ?? [];

        // Full cart items
        $itemsJson = $session['metadata']['items'] ?? null;
        $items = $itemsJson ? json_decode($itemsJson, true) : [];

        // Totals
        $amountTotal = $session['amount_total'] ?? 0;
        $currency = strtoupper($session['currency'] ?? 'CAD');

        // Stripe tax breakdown (if enabled)
        $taxAmount = $session['total_details']['amount_tax'] ?? 0;
        $subtotal = $amountTotal - $taxAmount;

        // Customer details
        $customer = $session['customer_details'] ?? [];
        $customerName  = $customer['name'] ?? 'N/A';
        $customerEmail = $customer['email'] ?? 'N/A';

        // Billing address
        $billing = $session['customer_details']['address'] ?? [];

        // Shipping address
        $shipping = $session['shipping_details']['address'] ?? [];
    @endphp

    {{-- ⭐ NEW: Order Summary --}}
    <div class="mb-6 p-4 bg-white shadow rounded border">
        <h2 class="font-semibold text-lg mb-3">Order Summary</h2>

        {{-- Customer Info --}}
        <div class="mb-4">
            <strong>Customer:</strong> {{ $customerName }}<br>
            <strong>Email:</strong> {{ $customerEmail }}
        </div>

        {{-- Items --}}
        <h3 class="font-semibold mb-2">Items Ordered</h3>

        @if (!empty($items))
            <div class="space-y-3">
                @foreach ($items as $item)
                    <div class="flex items-center space-x-4 border-b pb-3">

                        {{-- ⭐ Product Image --}}
                        @if(isset($item['image']))
                            <img src="{{ $item['image'] }}"
                                 alt="{{ $item['name'] }}"
                                 class="w-20 h-20 object-cover rounded border">
                        @endif

                        <div>
                            <div class="font-semibold">{{ $item['name'] }}</div>
                            <div>Qty: {{ $item['qty'] }}</div>
                            <div>${{ number_format($item['price'] / 100, 2) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600">No item data found.</p>
        @endif

        {{-- Totals --}}
        <div class="mt-4">
            <strong>Subtotal:</strong> ${{ number_format($subtotal / 100, 2) }} {{ $currency }}<br>
            <strong>Tax:</strong> ${{ number_format($taxAmount / 100, 2) }} {{ $currency }}<br>
            <strong>Total:</strong> ${{ number_format($amountTotal / 100, 2) }} {{ $currency }}
        </div>

        {{-- Billing Address --}}
        <div class="mt-6">
            <h3 class="font-semibold mb-2">Billing Address</h3>
            @if(!empty($billing))
                <div class="text-gray-700">
                    {{ $billing['line1'] ?? '' }}<br>
                    {{ $billing['line2'] ?? '' }}<br>
                    {{ $billing['city'] ?? '' }},
                    {{ $billing['state'] ?? '' }}<br>
                    {{ $billing['postal_code'] ?? '' }}<br>
                    {{ $billing['country'] ?? '' }}
                </div>
            @else
                <p class="text-gray-600">No billing address available.</p>
            @endif
        </div>

        {{-- Shipping Address --}}
        <div class="mt-6">
            <h3 class="font-semibold mb-2">Shipping Address</h3>
            @if(!empty($shipping))
                <div class="text-gray-700">
                    {{ $shipping['line1'] ?? '' }}<br>
                    {{ $shipping['line2'] ?? '' }}<br>
                    {{ $shipping['city'] ?? '' }},
                    {{ $shipping['state'] ?? '' }}<br>
                    {{ $shipping['postal_code'] ?? '' }}<br>
                    {{ $shipping['country'] ?? '' }}
                </div>
            @else
                <p class="text-gray-600">No shipping address available.</p>
            @endif
        </div>

        {{-- ⭐ Buttons --}}
        <div class="mt-6 flex space-x-4">

            {{-- Resend Receipt
            <form action="{{ route('admin.stripe.resendReceipt', $hook->id) }}" method="POST">
                @csrf
                <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Resend Receipt Email
                </button>
            </form>

            {{-- Refund
            <form action="{{ route('admin.stripe.refund', $hook->id) }}" method="POST">
                @csrf
                <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Refund in Stripe
                </button>
            </form>
            --}}
        </div>
    </div>

    {{-- Existing debug blocks --}}
    <div class="mb-6">
        <h2 class="font-semibold">Event Type</h2>
        <p>{{ $hook->name }}</p>
    </div>

    <div class="mb-6">
        <h2 class="font-semibold">Processed Payload</h2>
        <pre class="bg-gray-100 p-4 rounded">
{{ json_encode($hook->processed_payload, JSON_PRETTY_PRINT) }}
        </pre>
    </div>

    <div class="mb-6">
        <h2 class="font-semibold">Raw Payload</h2>
        <pre class="bg-gray-100 p-4 rounded">
{{ json_encode($hook->payload, JSON_PRETTY_PRINT) }}
        </pre>
    </div>

    <div class="mb-6">
        <h2 class="font-semibold">Headers</h2>
        <pre class="bg-gray-100 p-4 rounded">
{{ json_encode($hook->headers, JSON_PRETTY_PRINT) }}
        </pre>
    </div>

    <div class="mb-6">
        <h2 class="font-semibold">Exception</h2>
        <pre class="bg-gray-100 p-4 rounded">
{{ $hook->exception ?? 'None' }}
        </pre>
    </div>

@endsection
