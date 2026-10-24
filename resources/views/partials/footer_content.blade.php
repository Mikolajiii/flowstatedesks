<footer
    class="
        w-full
        bg-neutral-900
        text-neutral-300
        py-6
        px-4

        flex
        flex-col
        items-center
        justify-center
    "
>

    <p
        class="
            text-xs
            sm:text-sm
            md:text-base
        "
    >
        © 2026 Flowdesk — Crafted with purpose.
    </p>

    <div>
        <div
            class="
                flex
                flex-wrap
                justify-center
                items-center

                gap-3
                sm:gap-4
                md:gap-6

                mt-2

                text-xs
                sm:text-sm
                md:text-base
            "
        >
            <a
                href="mailto:michaelkondakow@gmail.com?subject=Flowdesk Inquiry&body=Hi, I have a question about..."
                class="text-purple-500 font-semibold hover:opacity-80 transition"
            >
                Contact Us
            </a>

            <a
                href="{{ route('terms') }}"
                class="text-purple-500 font-semibold hover:opacity-80 transition"
            >
                Terms
            </a>

            <a
                href="{{ route('shipping') }}"
                class="text-purple-500 font-semibold hover:opacity-80 transition"
            >
                Shipping & Returns
            </a>

            <a
                href="{{ route('faq') }}"
                class="text-purple-500 font-semibold hover:opacity-80 transition"
            >
                FAQ
            </a>

            <a
                href="/"
                class="text-purple-500 font-semibold hover:opacity-80 transition"
            >
                Main Page
            </a>

        </div>

</div>
</footer>
