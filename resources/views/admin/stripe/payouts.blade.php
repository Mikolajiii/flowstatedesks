
<x-admin-layout>
    <h1 class="text-2xl font-bold mb-4">Payouts</h1>

    <table class="w-full text-left border">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2">Amount</th>
                <th class="p-2">Status</th>
                <th class="p-2">Arrival</th>
                <th class="p-2">ID</th>
            </tr>
        </thead>

        <tbody>
@foreach ($payouts->data as $payout)
    <tr class="border-t">
        <td class="p-2">${{ number_format($payout->amount / 100, 2) }}</td>
        <td class="p-2">{{ $payout->status }}</td>
        <td class="p-2">{{ $payout->arrival_date ? date('Y-m-d', $payout->arrival_date) : '-' }}</td>
        <td class="p-2">{{ $payout->id }}</td>
    </tr>
    @endforeach
    </tbody>
    </table>
    </x-admin-layout>
