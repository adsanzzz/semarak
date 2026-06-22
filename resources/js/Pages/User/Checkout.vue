<script setup>
import { computed, ref } from "vue"
import { Head, useForm } from "@inertiajs/vue3"
import NavbarAuth from "@/Components/NavbarAuth.vue"

const props = defineProps({
    checkoutItems: {
        type: Array,
        default: () => [],
    },
    stores: {
        type: Array,
        default: () => [],
    },
    summary: {
        type: Object,
        default: () => ({ total_items: 0, total_price: 0 }),
    },
})

const form = useForm({
    shipping_method: "ambil_toko",
    location_map: "",
    payment_method: "cod",
})

const isDelivery = computed(() => form.shipping_method === "antar_rumah")

function formatCurrency(value) {
    return new Intl.NumberFormat("id-ID").format(value || 0)
}

function chooseShipping(method) {
    form.shipping_method = method
    if (method === "ambil_toko") {
        form.location_map = ""
    }
}

function choosePayment(method) {
    form.payment_method = method
}

function useCurrentLocation() {
    if (!navigator.geolocation) return

    navigator.geolocation.getCurrentPosition((position) => {
        const { latitude, longitude } = position.coords
        form.location_map = `https://maps.google.com/?q=${latitude},${longitude}`
    })
}

function submitCheckout() {
    form.post(route("checkout.store"))
}
</script>

<template>
    <div>
        <NavbarAuth />

        <Head title="Checkout" />

        <div class="max-w-5xl mx-auto p-6 space-y-6">
            <h1 class="text-2xl font-bold text-gray-800">Checkout</h1>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Detail Produk Checkout</h2>

                <div class="space-y-4">
                    <div
                        v-for="item in checkoutItems"
                        :key="item.product_id"
                        class="flex items-center gap-4 border rounded-lg p-3"
                    >
                        <img :src="item.image" alt="produk" class="w-16 h-16 rounded object-cover border" />
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ item.name }}</p>
                            <p class="text-sm text-gray-500">Toko: {{ item.seller?.nama_toko || item.seller?.name || '-' }}</p>
                            <p class="text-sm text-gray-500">Jumlah: {{ item.qty }}</p>
                        </div>
                        <p class="font-bold text-blue-600">Rp {{ formatCurrency(item.subtotal) }}</p>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t flex items-center justify-between text-sm md:text-base">
                    <span class="font-medium text-gray-700">Total {{ summary.total_items }} item</span>
                    <span class="font-bold text-xl text-blue-700">Rp {{ formatCurrency(summary.total_price) }}</span>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-semibold text-gray-800">Opsi Pengiriman</h2>

                <label class="flex items-start gap-3 border rounded-lg p-3 cursor-pointer">
                    <input
                        type="checkbox"
                        :checked="form.shipping_method === 'ambil_toko'"
                        @change="chooseShipping('ambil_toko')"
                        class="mt-1"
                    />
                    <div>
                        <p class="font-semibold">Ambil ke Toko</p>
                        <p class="text-sm text-gray-500">Anda mengambil pesanan langsung ke toko.</p>
                    </div>
                </label>

                <label class="flex items-start gap-3 border rounded-lg p-3 cursor-pointer">
                    <input
                        type="checkbox"
                        :checked="form.shipping_method === 'antar_rumah'"
                        @change="chooseShipping('antar_rumah')"
                        class="mt-1"
                    />
                    <div>
                        <p class="font-semibold">Antar ke Rumah</p>
                        <p class="text-sm text-gray-500">Kurir akan mengantar pesanan ke lokasi Anda.</p>
                    </div>
                </label>

                <div v-if="isDelivery" class="bg-gray-50 rounded-lg p-4 border space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Link Maps Lokasi Terkini</label>
                    <input
                        v-model="form.location_map"
                        type="text"
                        placeholder="Tempel link Google Maps lokasi pengiriman"
                        class="w-full border rounded-lg px-3 py-2"
                    />
                    <button
                        type="button"
                        @click="useCurrentLocation"
                        class="text-sm px-3 py-2 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200"
                    >
                        Gunakan lokasi saat ini
                    </button>
                    <p v-if="form.errors.location_map" class="text-red-500 text-sm">{{ form.errors.location_map }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-semibold text-gray-800">Opsi Pembayaran</h2>

                <label class="flex items-start gap-3 border rounded-lg p-3 cursor-pointer">
                    <input
                        type="checkbox"
                        :checked="form.payment_method === 'qris'"
                        @change="choosePayment('qris')"
                        class="mt-1"
                    />
                    <div>
                        <p class="font-semibold">QRIS</p>
                        <p class="text-sm text-gray-500">Scan QRIS sesuai toko terkait.</p>
                    </div>
                </label>

                <label class="flex items-start gap-3 border rounded-lg p-3 cursor-pointer">
                    <input
                        type="checkbox"
                        :checked="form.payment_method === 'cod'"
                        @change="choosePayment('cod')"
                        class="mt-1"
                    />
                    <div>
                        <p class="font-semibold">COD</p>
                        <p class="text-sm text-gray-500">Bayar saat barang diterima.</p>
                    </div>
                </label>

                <p v-if="form.errors.payment_method" class="text-red-500 text-sm">{{ form.errors.payment_method }}</p>
            </div>

            <button
                @click="submitCheckout"
                :disabled="form.processing"
                class="w-full py-3 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700 disabled:opacity-70"
            >
                {{ form.processing ? 'Memproses...' : 'Buat Pesanan' }}
            </button>

            <p class="text-sm text-stone-500 text-center">
                Jika memilih QRIS, kode QR akan muncul setelah pesanan berhasil dibuat.
            </p>
        </div>
    </div>
</template>