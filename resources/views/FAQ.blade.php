@extends('layouts.master')

@section('content')


    <div class="max-w-4xl mx-auto bg-neutral-900/60 backdrop-blur-md p-10 rounded-2xl shadow-xl">



    <h1 class="text-4xl font-bold text-center mb-10">
        Shipping & Returns — FAQ
    </h1>

    <!-- FAQ ITEM -->
    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">Do you offer order cancellations?</h3>
        <p class="text-white leading-relaxed">
            No. All Flow State products are custom‑made per order, and production begins shortly after your purchase.
            Because of this, orders cannot be cancelled once placed.
        </p>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">How long does it take for my order to ship?</h3>
        <p class="text-white leading-relaxed">
            Production time varies depending on workload, materials, and the product you ordered. Estimated build
            times are listed on each product page. Once your item is completed, it ships immediately.
        </p>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">How will my order ship?</h3>
        <p class="text-white leading-relaxed">
            Orders ship using the carrier and service level selected at checkout. You’ll receive a tracking number
            as soon as your order leaves our facility.
        </p>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">Can I track my shipment?</h3>
        <p class="text-white leading-relaxed">
            Yes. A tracking link will be emailed to you once your order ships. Please allow up to 24 hours for
            tracking updates to appear.
        </p>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">What if my package arrives damaged?</h3>
        <p class="text-white leading-relaxed">
            If your order arrives damaged, contact us within 7 days of delivery. We’ll help file a claim with the
            carrier and determine the next steps.
        </p>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">What if my package is lost?</h3>
        <p class="text-white leading-relaxed">
            If tracking shows no movement or the carrier marks your package as lost, reach out to us. We’ll work
            with the carrier to resolve the issue.
        </p>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">Do you accept returns?</h3>
        <p class="text-white leading-relaxed">
            Yes — but only under specific conditions. Because our products are custom‑made, returns require an RMA
            number and must meet all eligibility requirements.
        </p>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">What is an RMA number?</h3>
        <p class="text-white leading-relaxed">
            An RMA (Return Merchandise Authorization) number is required for any return. Returns sent without an
            RMA number will not be accepted.
        </p>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">How do I request an RMA?</h3>
        <p class="text-white leading-relaxed">
            Contact our support team with your order number and reason for return. If eligible, we’ll issue an RMA
            number and return instructions.
        </p>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">What are the return requirements?</h3>
        <ul class="list-disc list-inside text-white leading-relaxed space-y-1">
            <li>Unused</li>
            <li>In original packaging</li>
            <li>In original condition</li>
            <li>Returned within 7 days of delivery</li>
            <li>Accompanied by an RMA number</li>
        </ul>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">Who pays for return shipping?</h3>
        <p class="text-white leading-relaxed">
            The customer is responsible for all return shipping costs. We strongly recommend using a trackable and
            insured shipping method.
        </p>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">Can I exchange my item?</h3>
        <p class="text-white leading-relaxed">
            Exchanges follow the same process as returns. You must request an RMA, return the item in original
            condition, and place a new order.
        </p>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2">When will I receive my refund?</h3>
        <p class="text-white leading-relaxed">
            Once your return is received and inspected, approved refunds are issued to your original payment method.
             Refunds are typically processed within 5–7 business days.
        </p>
    </div>

    <div class="mb-2">
        <h3 class="text-xl font-semibold mb-2">Are any items non‑returnable?</h3>
        <ul class="list-disc list-inside text-white leading-relaxed space-y-1">
            <li>Custom‑made items (all FlowState products)</li>
            <li>Items showing signs of use</li>
            <li>Items not in original packaging</li>
            <li>Items returned without an RMA number</li>
        </ul>
    </div>

</div>
@include('partials.footer_content')
@endsection
