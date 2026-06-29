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
    payment_method: "qris",
})

const isDelivery = computed(() => form.shipping_method === "antar_rumah")

const availablePayments = computed(() => {
    if (form.shipping_method === "ambil_toko") {
        return ["qris"]
    }
    return ["qris", "cash"]
})

function formatCurrency(value) {
    return new Intl.NumberFormat("id-ID").format(value || 0)
}

function chooseShipping(method) {
    form.shipping_method = method
    if (method === "ambil_toko") {
        form.location_map = ""
        form.payment_method = "qris"
    }
}

function choosePayment(method) {
    form.payment_method = method
}

// Default coordinate (Jebres Surakarta) if store coordinates are unset
const DEFAULT_LAT = -7.561
const DEFAULT_LNG = 110.849

// Parse coordinates from link
const buyerCoordinates = computed(() => {
    if (!form.location_map) return null
    const regex = /(?:q=|place\/|query=)(-?\d+\.\d+),(-?\d+\.\d+)/
    const match = form.location_map.match(regex)
    if (match) {
        return {
            lat: parseFloat(match[1]),
            lng: parseFloat(match[2])
        }
    }
    const simpleRegex = /^(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)$/
    const simpleMatch = form.location_map.trim().match(simpleRegex)
    if (simpleMatch) {
        return {
            lat: parseFloat(simpleMatch[1]),
            lng: parseFloat(simpleMatch[2])
        }
    }
    return null
})

// Haversine distance formula
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371
    const dLat = (lat2 - lat1) * (Math.PI / 180)
    const dLon = (lon2 - lon1) * (Math.PI / 180)
    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2)
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))
    return R * c
}

// Calculate distance to each store
const storeDistances = computed(() => {
    if (!isDelivery.value || !buyerCoordinates.value) return []

    return props.stores.map(store => {
        const storeLat = parseFloat(store.latitude) || DEFAULT_LAT
        const storeLng = parseFloat(store.longitude) || DEFAULT_LNG
        const distance = calculateDistance(
            buyerCoordinates.value.lat,
            buyerCoordinates.value.lng,
            storeLat,
            storeLng
        )
        return {
            store_id: store.id,
            name: store.nama_toko || store.name,
            distance: distance,
            isValid: distance <= 5
        }
    })
})

const isDistanceExceeded = computed(() => {
    if (!isDelivery.value) return false
    if (!buyerCoordinates.value) return false
    return storeDistances.value.some(d => d.distance > 5)
})

function useCurrentLocation() {
    if (!navigator.geolocation) return

    navigator.geolocation.getCurrentPosition((position) => {
        const { latitude, longitude } = position.coords
        form.location_map = `https://maps.google.com/?q=${latitude},${longitude}`
    })
}

function submitCheckout() {
    if (isDelivery.value) {
        if (!form.location_map) {
            alert('Silakan masukkan lokasi pengiriman terlebih dahulu.')
            return
        }
        if (!buyerCoordinates.value) {
            alert('Format link lokasi Maps tidak valid. Silakan gunakan tombol "Gunakan lokasi saat ini" atau masukkan format "latitude,longitude".')
            return
        }
        if (isDistanceExceeded.value) {
            alert('Pengiriman gagal: Jarak ke salah satu toko melebihi batas 5 km. Silakan gunakan opsi Ambil ke Toko.')
            return
        }
    }
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
                    <label class="block text-sm font-medium text-gray-700">Link Maps Lokasi Terkini / Koordinat (Lat, Lng)</label>
                    <input
                        v-model="form.location_map"
                        type="text"
                        placeholder="Tempel link Google Maps atau masukkan format: -7.5612,110.8491"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500"
                    />
                    <button
                        type="button"
                        @click="useCurrentLocation"
                        class="text-sm px-3 py-2 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200"
                    >
                        Gunakan lokasi saat ini
                    </button>
                    <p v-if="form.errors.location_map" class="text-red-500 text-sm">{{ form.errors.location_map }}</p>

                    <!-- DISTANCE CALCULATOR DISPLAY -->
                    <div v-if="form.location_map && buyerCoordinates" class="mt-3 space-y-2 p-3 bg-white rounded-lg border text-sm">
                        <p class="font-semibold text-gray-700">Informasi Jarak Pengiriman:</p>
                        <div v-for="d in storeDistances" :key="d.store_id" class="flex justify-between items-center">
                            <span class="text-gray-600">Toko {{ d.name }}:</span>
                            <span :class="['font-bold', d.isValid ? 'text-green-600' : 'text-red-600']">
                                {{ d.distance.toFixed(2) }} km {{ d.isValid ? '(Dalam Jangkauan)' : '(Melebihi Batas 5km)' }}
                            </span>
                        </div>
                    </div>

                    <div v-if="form.location_map && buyerCoordinates && isDistanceExceeded" class="p-3 bg-red-50 border border-red-200 rounded-lg text-xs text-red-700 font-medium">
                        ⚠️ Jarak ke salah satu toko melebihi batas 5 km. Pengiriman ke rumah tidak didukung untuk pesanan ini. Silakan ubah pengiriman ke "Ambil ke Toko".
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 space-y-4">
                <h2 class="text-lg font-semibold text-gray-800">Opsi Pembayaran</h2>

                <label v-if="availablePayments.includes('qris')" class="flex items-start gap-3 border rounded-lg p-3 cursor-pointer">
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

<label v-if="availablePayments.includes('cash')" class="flex items-start gap-3 border rounded-lg p-3 cursor-pointer">
    <input
        type="checkbox"
        :checked="form.payment_method === 'cash'"
        @change="choosePayment('cash')"
        class="mt-1"
    />
    <div>
        <p class="font-semibold">CASH</p>
        <p class="text-sm text-gray-500">Bayar saat barang diterima.</p>
    </div>
</label>

                <p v-if="form.errors.payment_method" class="text-red-500 text-sm">{{ form.errors.payment_method }}</p>
            </div>

            <button
                @click="submitCheckout"
                :disabled="form.processing || (isDelivery && (!buyerCoordinates || isDistanceExceeded))"
                class="w-full py-3 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700 disabled:opacity-70 disabled:cursor-not-allowed"
            >
                {{ form.processing ? 'Memproses...' : 'Buat Pesanan' }}
            </button>

            <p class="text-sm text-stone-500 text-center">
                Jika memilih QRIS, kode QR akan muncul setelah pesanan berhasil dibuat.
            </p>
        </div>
    </div>
</template>