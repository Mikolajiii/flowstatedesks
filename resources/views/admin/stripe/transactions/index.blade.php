@extends('layouts.master')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Stripe Transactions</h1>
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
            {{ session('error') }}
        </div>
    @endif

    <a href="{{ route('admin.stripe.dashboard') }}"
       class="inline-block mb-4 px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition">
        ← Back to Dashboard
    </a>

    <a href="{{ route('admin.stripe.transactions.sync') }}"
       class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
        🔄 Manual Sync with Stripe
    </a>

    <table class="table-auto w-full border">
        <thead class="bg-gray-100">
        <tr>
            <th class="px-3 py-2 text-left">ID</th>
            <th class="px-3 py-2 text-left">Event</th>
            <th class="px-3 py-2 text-left">Name</th>
            <th class="px-3 py-2 text-left">Card</th>
            <th class="px-3 py-2 text-left">Status</th>
            <th class="px-3 py-2 text-right">Gross</th>
            <th class="px-3 py-2 text-right">Fee</th>
            <th class="px-3 py-2 text-right">Net</th>
            <th class="px-3 py-2 text-left">Currency</th>
            <th class="px-3 py-2 text-left">Created</th>
            <th></th>
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
                <td class="px-3 py-2">
                    {{ $txn->id }}
                </td>

                <td class="px-3 py-2">
                    {{ $eventType }}
                </td>

                <td class="px-3 py-2">
                    {{ $txn->buyer_name ?? 'N/A' }}
                </td>

                <td class="px-3 py-2">
                    @if($txn->card_brand && $txn->last4)
                        {{ strtoupper($txn->card_brand) }}
                        ****{{ $txn->last4 }}
                    @else
                        -
                    @endif
                </td>

                <td class="px-3 py-2">
                    @if($txn->display_status === 'paid')
                        <span class="badge bg-success">
            Paid
        </span>

                    @elseif($txn->display_status === 'succeeded')
                        <span class="badge bg-success">
            Succeeded
        </span>

                    @elseif($txn->display_status === 'pending')
                        <span class="badge bg-warning">
            Pending
        </span>

                    @elseif($txn->display_status)
                        <span class="badge bg-secondary">
            {{ ucfirst($txn->display_status) }}
        </span>

                    @else
                        <span class="badge bg-secondary">
            N/A
        </span>
                    @endif
                </td>

                <td class="px-3 py-2 text-right">
                    @if($txn->gross)
                        ${{ number_format($txn->gross / 100, 2) }}
                    @elseif(!is_null($amount))
                        ${{ number_format($amount / 100, 2) }}
                    @else
                        -
                    @endif
                </td>

                <td class="px-3 py-2 text-right text-red-600">
                    @if($txn->fee)
                        ${{ number_format($txn->fee / 100, 2) }}
                    @else
                        -
                    @endif
                </td>

                <td class="px-3 py-2 text-right text-green-600 font-semibold">
                    @if($txn->net)
                        ${{ number_format($txn->net / 100, 2) }}
                    @else
                        -
                    @endif
                </td>

                <td class="px-3 py-2">
                    {{ strtoupper($currency) }}
                </td>

                <td class="px-3 py-2">
                    {{ \Carbon\Carbon::parse($txn->created_at)->format('F j, Y g:i A') }}
                </td>

                <td class="px-3 py-2">
                    <div class="flex gap-3">
                        <a
                            href="{{ route('admin.stripe.transactions.show', $txn->id) }}"
                            class="text-blue-600 hover:underline"
                        >
                            View
                        </a>

                        @if($txn->receipt_url)
                            <a
                                href="{{ $txn->receipt_url }}"
                                target="_blank"
                                class="text-green-600 hover:underline"
                            >
                                Receipt
                            </a>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $transactions->links() }}
@endsection
