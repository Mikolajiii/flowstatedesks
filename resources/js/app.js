import Alpine from 'alpinejs'

window.Alpine = Alpine

function cartDrawer(initialItems = {}) {
    return {
        open: false,
        items: initialItems,
        subtotal: 0,
        showEmptyPopup: false,
        badgeCount: 0,

        // ⭐ NEW: per-item loading + success indicators
        loadingItem: null,
        successItem: null,

        init() {
            // Expose Alpine instance globally
            window.cartDrawerInstance = this;

            // Hydrate AFTER Alpine mounts
            this.$nextTick(() => {
                this.rehydrate(initialItems);
            });
        },

        // ⭐ Hydrate cart items
        rehydrate(newItems) {
            this.items = newItems || {};
            this.recalculate();
        },

        // ⭐ Recalculate totals + badge
        recalculate() {
            this.subtotal = Object.values(this.items).reduce((sum, item) => {
                return sum + item.price * item.qty;
            }, 0);

            this.badgeCount = Object.values(this.items)
                .reduce((t, i) => t + i.qty, 0);
        },

        // ⭐ Auto-save quantity change (with spinner + checkmark)
        updateQty(key, qty) {
            if (!this.items[key]) return;

            // Normalize qty
            qty = Math.max(0, parseInt(qty, 10) || 0);

            // Set local state immediately
            this.items[key].qty = qty;
            this.recalculate();

            // Show spinner
            this.loadingItem = key;
            this.successItem = null;

            // Send AJAX update
            fetch('/cart/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ qty: { [key]: qty } })
            })
                .then(res => res.json())
                .then(data => {
                    // Rehydrate cart from server
                    this.rehydrate(data.cart);

                    // Stop spinner
                    this.loadingItem = null;

                    // Show checkmark
                    this.successItem = key;

                    // Auto-hide checkmark
                    setTimeout(() => {
                        this.successItem = null;
                    }, 1200);
                });
        },

        // ⭐ Increment auto-saves
        increment(key) {
            if (!this.items[key]) return;
            this.updateQty(key, this.items[key].qty + 1);
        },

        // ⭐ Decrement auto-saves
        decrement(key) {
            if (!this.items[key]) return;
            this.updateQty(key, this.items[key].qty - 1);
        }
    };
}

// Register Alpine component
window.cartDrawer = cartDrawer;

// Start Alpine
Alpine.start();
