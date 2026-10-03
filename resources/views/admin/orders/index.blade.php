
<x-admin-layout>
    <h1 class="text-2xl font-bold mb-4">Orders</h1>

    <table class="w-full text-left border">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2">Customer</th>
                <th class="p-2">Product</th>
                <th class="p-2">Amount</th>
                <th class="p-2">Payment</th>
                <th class="p-2">Payout</th>
                <th class="p-2">Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($orders as $order)
                <tr class="border-t">
                    <td class="p-2">{{ $order->customer_email }}</td>
                    <td class="p-2">{{ $order->product_name }}</td>
                    <td class="p-2">${{ number_format($order->amount_total / 100, 2) }}</td>
                    <td class="p-2">{{ $order->payment_status }}</td>
                    <td class="p-2">{{ $order->payout_status }}</td>
                    <td class="p-2">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $orders->links() }}
</x-admin-layout>
