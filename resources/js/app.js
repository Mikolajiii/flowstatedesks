import Alpine from 'alpinejs'

window.Alpine = Alpine

function cartDrawer(initialItems = {}) {
    return {
        open: false,
        items: initialItems,
        subtotal: 0,
        showEmptyPopup: false,
        badgeCount: 0,

        // ⭐ Alpine lifecycle
        init() {
            // ⭐ REQUIRED: expose Alpine instance globally
            // This prevents hydration timing issues and flicker
            window.cartDrawerInstance = this;

            // ⭐ Hydrate AFTER Alpine is fully mounted
            // This avoids the “instant pop” before transitions
            this.$nextTick(() => {
                this.rehydrate(initialItems);
            });
        },

        // ⭐ Hydrate cart items
        rehydrate(newItems) {
            this.items = newItems || {};
            this.recalculate();
        },

        // ⭐ Recalculate totals
        recalculate() {
            this.subtotal = Object.values(this.items)
                .reduce((t, i) => t + (i.price * i.qty), 0);

            this.badgeCount = Object.values(this.items)
                .reduce((t, i) => t + i.qty, 0);
        },

        // ⭐ Quantity controls
        increment(key) {
            if (!this.items[key]) return;
            this.items[key].qty++;
            this.recalculate();
        },

        decrement(key) {
            if (!this.items[key]) return;
            if (this.items[key].qty > 1) {
                this.items[key].qty--;
            } else {
                this.items[key].qty = 0;
            }
            this.recalculate();
        },

        updateQty(key, value) {
            const v = parseInt(value, 10);
            if (!this.items[key]) return;

            this.items[key].qty = (isNaN(v) || v <= 0) ? 0 : v;
            this.recalculate();
        }
    };
}

// ⭐ Register Alpine component factory
window.cartDrawer = cartDrawer;

// ⭐ Start Alpine
Alpine.start();
