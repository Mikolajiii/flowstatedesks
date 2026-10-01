
@extends('layouts.master')

@section('content')


    <div class="max-w-4xl mx-auto bg-neutral-900/60 backdrop-blur-md p-10 rounded-2xl shadow-xl">




    <h1 class="text-4xl font-bold text-center mb-10">
        Shipping & Return Policy
    </h1>

    <!-- SHIPPING POLICY -->
    <h2 class="text-2xl font-semibold mb-4">Shipping Policy</h2>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">Order Processing</h3>
        <p class="text-white leading-relaxed">
            All Flow State products are custom‑built to order. Production begins immediately after your purchase is
            completed. Because each item is made specifically for you, processing times may vary depending on
            current workload and material availability. Typical production time is listed on each product page.
        </p>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">Shipping Times</h3>
        <p class="text-white leading-relaxed">
            Once your order is completed, it will ship using the carrier and service level selected at checkout.
            Delivery estimates are provided by the carrier and may vary due to weather, regional delays, or other
            factors outside our control.
        </p>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">Tracking Information</h3>
        <p class="text-white leading-relaxed">
            You will receive a shipping confirmation email with a tracking number as soon as your order leaves our
            facility. Please allow up to 24 hours for tracking data to update.
        </p>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">Lost or Damaged Shipments</h3>
        <p class="text-white leading-relaxed">
            If your shipment arrives damaged or appears lost in transit, please contact us immediately. We will
            work directly with the carrier to resolve the issue. Claims must be submitted within 7 days of delivery.
        </p>
    </div>

    <!-- RETURN POLICY -->
    <h2 class="text-2xl font-semibold mt-12 mb-4">Return Policy</h2>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">Custom‑Made Products</h3>
        <p class="text-white leading-relaxed">
            All FlowState products are custom‑made per order. Because of this, orders cannot be cancelled once
            production has begun, and returns are only accepted under specific conditions.
        </p>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">RMA Required</h3>
        <p class="text-white leading-relaxed">
            To initiate a return, you must request a Return Merchandise Authorization (RMA) number. Returns sent
            without an RMA number will not be accepted.
        </p>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">Return Eligibility</h3>
        <ul class="list-disc list-inside text-white leading-relaxed space-y-1">
            <li>The product is unused</li>
            <li>In original packaging</li>
            <li>In original condition</li>
            <li>Returned within 7 days of delivery</li>
            <li>An RMA number has been issued</li>
        </ul>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">Return Shipping Costs</h3>
        <p class="text-white leading-relaxed">
            All return shipping costs are the responsibility of the customer. We recommend using a trackable and
            insured shipping method, as we cannot be responsible for lost or damaged return shipments.
        </p>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">Condition Requirements</h3>
        <p class="text-white leading-relaxed">
            Returned items must arrive in original packaging, original condition, and free of damage, wear, or
            modification. Items that arrive damaged, used, or missing components may be rejected or subject to a
            restocking fee.
        </p>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">Non‑Returnable Items</h3>
        <ul class="list-disc list-inside text-white leading-relaxed space-y-1">
            <li>Custom orders</li>
            <li>Modified or personalized items</li>
            <li>Items showing signs of use or installation</li>
        </ul>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">Refunds</h3>
        <p class="text-white leading-relaxed">
            Once your return is received and inspected, we will notify you of approval or rejection. Approved
            returns will be refunded to the original payment method, minus any applicable fees (such as shipping or
            restocking fees). Refunds are typically processed within 5–7 business days.
        </p>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">Order Cancellations</h3>
        <p class="text-white leading-relaxed">
            Because all Flow State products are custom‑made, orders cannot be cancelled once production has started.
            If you need to request a cancellation, contact us immediately. If production has not yet begun, we may
            be able to cancel the order at our discretion.
        </p>
    </div>

    <div class="mb-2">
        <h3 class="text-xl font-semibold mb-2">Exchanges</h3>
        <p class="text-white leading-relaxed">
            Exchanges are handled as a return and new purchase. An RMA is required, and the item must meet all return
            eligibility requirements.
        </p>
    </div>

</div>
@include('partials.footer_content')

@endsection
