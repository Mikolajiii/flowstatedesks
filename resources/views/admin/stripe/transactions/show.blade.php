@extends('layouts.master')

@section('content')

    <a href="{{ route('admin.stripe.dashboard') }}"
       class="inline-block mb-4 px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition">
        ← Back to Dashboard
    </a>
    <h1 class="text-xl font-bold mb-4">Transaction #{{ $hook->id }}</h1>

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

@endsection

