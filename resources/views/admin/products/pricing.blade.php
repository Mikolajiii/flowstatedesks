@extends('layouts.master')


@section('content')


    <div class="container py-5">

        <div class="mx-auto" style="max-width: 900px;">

            <div class="text-center mb-5">
                <h1 class="fw-bold">
                    Product Pricing
                </h1>

                <p class="text-muted">
                    Update product pricing shown throughout the website.
                </p>
            </div>




            <form
                method="POST"
                action="{{ route('admin.products.update') }}"
            >
                @csrf

                @foreach($products as $key => $product)

                    <div
                        class="card border-0 shadow-sm mb-4"
                        style="
                        border-radius:20px;
                        border:2px solid #dbeafe !important;
                        background:#ffffff;
                    "
                    >

                        <div class="card-body p-4">

                            <div class="text-center">

                                <img
                                    src="{{ asset($product['image']) }}"
                                    alt="{{ $product['name'] }}"
                                    class="img-fluid mb-3"
                                    style="
                                    max-height:180px;
                                    object-fit:contain;
                                "
                                >

                                <h4 class="fw-bold mb-3">
                                    {{ $product['name'] }}
                                </h4>

                                <div
                                    class="mx-auto"
                                    style="max-width:250px;"
                                >

                                    <label
                                        class="form-label fw-semibold text-primary"
                                    >
                                        Price (CAD)
                                    </label>

                                    <input
                                        type="number"
                                        step="0.01"
                                        class="form-control text-center fw-bold"
                                        name="price[{{ $key }}]"
                                        value="{{ number_format($product['price'] / 100, 2, '.', '') }}"
                                        style="
                                        border:2px solid #3b82f6;
                                        border-radius:12px;
                                        height:55px;
                                        font-size:1.2rem;
                                    "
                                    >

                                </div>

                            </div>

                        </div>

                    </div>

                @endforeach

                <div class="text-center mt-5">

                    <button
                        type="submit"
                        class="btn btn-lg px-5 py-3 fw-bold"
                        style="
                        background:#2563eb;
                        border:2px solid #1d4ed8;
                        color:white;
                        border-radius:14px;
                        min-width:220px;
                    "
                    >
                        Save Prices
                    </button>

                </div>

            </form>

        </div>

    </div>
@endsection
