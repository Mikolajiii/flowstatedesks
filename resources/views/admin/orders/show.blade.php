<x-admin-layout>
    <h1 class="text-2xl font-bold mb-4">Order #{{ $order->id }}</h1>

    <div class="mb-6 p-4 border rounded bg-gray-50">
        <p><strong>Stripe Session ID:</strong> {{ $order->stripe_session_id }}</p>
        <p><strong>Customer Email:</strong> {{ $order->customer_email }}</p>
        <p><strong>Product:</strong> {{ $order->product_name }}</p>
        <p><strong>Amount:</strong> ${{ number_format($order->amount_total / 100, 2) }}</p>
        <p><strong>Payment Status:</strong> {{ $order->payment_status }}</p>
        <p><strong>Payout Status:</strong> {{ $order->payout_status }}</p>
        <p><strong>Created:</strong> {{ $order->created_at }}</p>
    </div>

    <h2 class="text-xl font-semibold mb-2">Raw Stripe Payload</h2>

    <pre class="text-xs bg-black text-green-400 p-4 rounded overflow-x-auto">
{{ json_encode($order->raw_payload, JSON_PRETTY_PRINT) }}
    </pre>

    <a href="{{ route('admin.orders') }}" class="inline-block mt-4 text-blue-600 hover:underline">
        ← Back to Orders
    </a>
</x-admin-layout>

