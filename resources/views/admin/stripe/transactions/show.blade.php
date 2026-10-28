@extends('layouts.master')

@section('content')

    <a href="{{ route('admin.stripe.transactions') }}"
       class="inline-block mb-4 px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition">
        ← Back to Transactions
    </a>

    <h1 class="text-2xl font-bold mb-6">
        Transaction #{{ $hook->id }}
    </h1>
    <div class="bg-white border rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">
            Transaction Summary
        </h2>

        <div class="grid grid-cols-2 gap-6">

            <div>
                <strong>Event Type:</strong><br>
                {{ $hook->name }}
            </div>

            <div>
                <strong>Stripe ID:</strong><br>
                {{ $hook->stripe_id ?? 'N/A' }}
            </div>

            <div>
                <strong>Gross Amount:</strong><br>
                @if($hook->gross)
                    ${{ number_format($hook->gross / 100, 2) }}
                @else
                    -
                @endif
            </div>

            <div>
                <strong>Stripe Fee:</strong><br>
                @if($hook->fee)
                    <span class="text-red-600">
                    ${{ number_format($hook->fee / 100, 2) }}
                </span>
                @else
                    -
                @endif
            </div>

            <div>
                <strong>Net Deposit:</strong><br>
                @if($hook->net)
                    <span class="text-green-600 font-semibold">
                    ${{ number_format($hook->net / 100, 2) }}
                </span>
                @else
                    -
                @endif
            </div>

            <div>
                <strong>Payout Status:</strong><br>

                @if($hook->payout_status === 'paid')
                    <span class="text-green-600 font-semibold">
                    Paid to Bank
                </span>
                @elseif($hook->payout_status === 'pending')
                    <span class="text-amber-600 font-semibold">
                    Pending
                </span>
                @else
                    N/A
                @endif
            </div>

            <div>
                <strong>Card:</strong><br>

                @if($hook->card_brand && $hook->last4)
                    {{ strtoupper($hook->card_brand) }}
                    ****{{ $hook->last4 }}
                @else
                    N/A
                @endif
            </div>

            <div>
                <strong>Processed At:</strong><br>
                {{ $hook->processed_at ?? 'Not Processed' }}
            </div>

            <div>
                <strong>Created:</strong><br>
                {{ $hook->created_at }}
            </div>

            <div>
                <strong>Updated:</strong><br>
                {{ $hook->updated_at }}
            </div>

        </div>

        @if($hook->receipt_url)
            <div class="mt-6 border-t pt-4">

                <a href="{{ $hook->receipt_url }}"
                   target="_blank"
                   class="inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    View Stripe Receipt
                </a>

            </div>
        @endif
    </div>


    @if($hook->processed_payload)
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">
                Processed Payload
            </h2>

            <pre class="bg-gray-100 p-4 rounded overflow-auto text-sm">{{ json_encode($hook->processed_payload, JSON_PRETTY_PRINT) }}</pre>
        </div>
    @endif

    <div class="mb-6">
        <h2 class="text-lg font-semibold mb-2">
            Raw Stripe Payload
        </h2>

        <pre class="bg-gray-100 p-4 rounded overflow-auto text-sm">{{ json_encode($hook->payload, JSON_PRETTY_PRINT) }}</pre>
    </div>

@endsection
