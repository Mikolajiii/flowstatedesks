@extends('layouts.master')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Stripe Transactions</h1>
    <a href="{{ route('admin.stripe.dashboard') }}"
       class="inline-block mb-4 px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition">
        ← Back to Dashboard
    </a>
    <table class="table-auto w-full border">
        <thead class="bg-gray-100">
        <tr>
            <th class="px-3 py-2 text-left">ID</th>
            <th class="px-3 py-2 text-left">Event</th>
            <th class="px-3 py-2 text-left">Name</th>
            <th class="px-3 py-2 text-right">Amount</th>
            <th class="px-3 py-2 text-left">Currency</th>
            <th class="px-3 py-2 text-left">Created</th>
            <th class="px-3 py-2 text-left"></th>
        </tr>
        </thead>

        <tbody>
        @foreach ($transactions as $txn)
            @php
                $payload = json_decode($txn->payload, true);

                $eventType = isset($payload['type'])
                    ? trim($payload['type'], '"')
                    : 'unknown';

                $obj = $payload['data']['object'] ?? [];
                $amount = $obj['amount'] ?? null;
                $currency = $obj['currency'] ?? '-';
            @endphp

            <tr>
                <td class="px-3 py-2 text-left">{{ $txn->id }}</td>
                <td class="px-3 py-2 text-left">{{ $eventType }}</td>
                <td class="px-3 py-2 text-left">
                    {{ $txn->buyer_name ?? 'N/A' }}
                </td>


                <td class="px-3 py-2 text-right">
                    @if(!is_null($amount))
                        ${{ number_format($amount / 100, 2) }}
                    @else
                        -
                    @endif
                </td>
                <td class="px-3 py-2 text-left">{{ $currency }}</td>
                <td class="px-3 py-2 text-left">
                    {{ \Carbon\Carbon::parse($txn->created_at)->format('F j, Y g:i A') }}
                </td>

                <td class="px-3 py-2 text-left">
                    <a href="{{ route('admin.stripe.transactions.show', $txn->id) }}" class="text-blue-600">
                        View
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $transactions->links() }}
@endsection
