export default function cartDrawer(initialItems = {}) {
    return {
        open: false,
        items: initialItems,
        subtotal: 0,
        showEmptyPopup: false,
        badgeCount: 0,

        loadingItem: null,
        successItem: null,

        init() {
            window.cartDrawerInstance = this
            this.$nextTick(() => this.rehydrate(initialItems))
        },

        rehydrate(newItems) {
            this.items = newItems || {}
            this.recalculate()
        },

        recalculate() {
            this.subtotal = Object.values(this.items)
                .reduce((sum, item) => sum + item.price * item.qty, 0)

            this.badgeCount = Object.values(this.items)
                .reduce((t, i) => t + i.qty, 0)
        },

        updateQty(key, qty) {
            if (!this.items[key]) return

            qty = Math.max(0, parseInt(qty, 10) || 0)

            this.items[key].qty = qty
            this.recalculate()

            this.loadingItem = key
            this.successItem = null

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

                    // ⭐ DO NOT rehydrate entire cart (causes shutter)
                    if (data.cart[key]) {
                        this.items[key].qty = data.cart[key].qty
                    }

                    this.recalculate()

                    this.loadingItem = null
                    this.successItem = key

                    setTimeout(() => {
                        this.successItem = null
                    }, 2500)
                })
        },

        increment(key) {
            if (!this.items[key]) return
            this.updateQty(key, this.items[key].qty + 1)
        },

        decrement(key) {
            if (!this.items[key]) return
            this.updateQty(key, this.items[key].qty - 1)
        }
    }
}
