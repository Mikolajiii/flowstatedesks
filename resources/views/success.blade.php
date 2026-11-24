<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed | FlowState</title>

    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-neutral-50">

<div class="min-h-screen flex items-center justify-center px-6 py-12">

    <div class="w-full max-w-xl">

        <div class="bg-white rounded-3xl shadow-xl border border-neutral-200 overflow-hidden">

            <!-- Success Header -->
            <div class="bg-green-50 border-b border-green-100 px-8 py-8 text-center">

                <div class="mx-auto flex items-center justify-center w-20 h-20 rounded-full bg-green-500 shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-10 h-10 text-white"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h1 class="mt-6 text-3xl font-bold text-neutral-900">
                    Thank You for Your Order
                </h1>

                <p class="mt-3 text-neutral-600 leading-relaxed">
                    Your payment has been successfully received and your order is now being processed.
                </p>

            </div>

            <!-- Content -->
            <div class="px-8 py-8">

                <div class="space-y-4">

                    <p class="text-neutral-700 leading-relaxed">
                        We sincerely appreciate your support and are excited to get your order on its way.
                    </p>

                    <p class="text-neutral-700 leading-relaxed">
                        Our team will begin preparing your items immediately and will ship them as soon as possible.
                    </p>

                    <p class="text-neutral-700 leading-relaxed">
                        Once your order ships, you'll receive a confirmation email with tracking information.
                    </p>

                </div>

                <div class="mt-10 flex flex-col sm:flex-row gap-4">

                    <a href="/"
                       class="flex-1 text-center px-6 py-3 rounded-full bg-neutral-900 text-white font-medium hover:bg-neutral-800 transition">
                        Return Home
                    </a>



                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>
