import Alpine from 'alpinejs'

window.Alpine = Alpine

function cartDrawer(initialItems = {}) {
    return {
        open: false,
        items: initialItems,
        subtotal: 0,
        showEmptyPopup: false,
        badgeCount: 0,

        init() {
            this.rehydrate(initialItems);
            // ⭐ THIS is the correct place

        },

        // ⭐ NEW: full rehydration support
        rehydrate(newItems) {
            this.items = newItems || {};
            this.recalculate();
        },

        recalculate() {
            this.subtotal = Object.values(this.items)
                .reduce((t, i) => t + (i.price * i.qty), 0);

            this.badgeCount = Object.values(this.items)
                .reduce((t, i) => t + i.qty, 0);
        },

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

window.cartDrawer = cartDrawer;
Alpine.start();
