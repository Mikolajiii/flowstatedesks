@extends('layouts.master')

@section('content')
    <h1 class="text-3xl font-bold mb-8 text-center">Stripe Dashboard</h1>

    <div class="flex justify-center gap-6">

        <!-- Orders Button -->
        <a href="{{ route('admin.stripe.orders') }}"
           class="px-8 py-4 bg-blue-600 text-white rounded-lg text-xl font-semibold shadow hover:bg-blue-700 transition">
            Orders
        </a>

        <!-- Transactions Button -->
        <a href="{{ route('admin.stripe.transactions') }}"
           class="px-8 py-4 bg-green-600 text-white rounded-lg text-xl font-semibold shadow hover:bg-green-700 transition">
            Transactions
        </a>

    </div>
@endsection
