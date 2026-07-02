<script setup>
import { computed, ref, watch } from "vue"
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

const localCheckoutItems = ref([...props.checkoutItems])
const localSummary = ref({ ...props.summary })

watch(() => props.checkoutItems, (newVal) => {
    localCheckoutItems.value = [...newVal]
}, { deep: true })
watch(() => props.summary, (newVal) => {
    localSummary.value = { ...newVal }
}, { deep: true })

const isUpdating = ref(false)

const changeQty = async (item, amount) => {
    if (isUpdating.value) return
    const newQty = item.qty + amount
    if (newQty < 1) return
    if (newQty > (item.stok ?? 9999)) {
        alert('Jumlah melebihi stok yang tersedia')
        return
    }

    isUpdating.value = true
    try {
        const res = await fetch(route('checkout.update'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                product_id: item.product_id,
                variations: item.variations,
                qty: newQty
            })
        })
        const data = await res.json()
        if (!res.ok) {
            alert(data.error || 'Gagal mengubah jumlah')
            return
        }
        localCheckoutItems.value = data.checkoutItems
        localSummary.value = data.summary
    } catch (err) {
        console.error(err)
        alert('Terjadi kesalahan')
    } finally {
        isUpdating.value = false
    }
}

const updateQtyInput = async (item) => {
    if (item.qty < 1) {
        item.qty = 1
    } else if (item.qty > (item.stok ?? 9999)) {
        item.qty = item.stok ?? 9999
        alert('Jumlah melebihi stok yang tersedia')
    }

    isUpdating.value = true
    try {
        const res = await fetch(route('checkout.update'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                product_id: item.product_id,
                variations: item.variations,
                qty: item.qty
            })
        })
        const data = await res.json()
        if (!res.ok) {
            alert(data.error || 'Gagal mengubah jumlah')
            return
        }
        localCheckoutItems.value = data.checkoutItems
        localSummary.value = data.summary
    } catch (err) {
        console.error(err)
    } finally {
        isUpdating.value = false
    }
}

const allStoresHaveQris = computed(() => {
    if (!props.stores || props.stores.length === 0) return false
    return props.stores.every(store => !!store.qris_image)
})

const form = useForm({
    shipping_method: "ambil_toko",
    location_map: "",
    payment_method: allStoresHaveQris.value ? "qris" : "cash",
})

const isDelivery = computed(() => form.shipping_method === "antar_rumah")

const availablePayments = computed(() => {
    const methods = ["cash"]
    if (allStoresHaveQris.value) {
        methods.push("qris")
    }
    return methods
})

watch(availablePayments, (newPayments) => {
    if (!newPayments.includes(form.payment_method)) {
        form.payment_method = newPayments[0] || "cash"
    }
}, { immediate: true })

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

// Default coordinate (Jebres Surakarta) if store coordinates are unset
const DEFAULT_LAT = -7.561
const DEFAULT_LNG = 110.849

// Parse coordinates from link
const buyerCoordinates = computed(() => {
    if (!form.location_map) return null
    const match = form.location_map.match(/(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/)
    if (match) {
        return {
            lat: parseFloat(match[1]),
            lng: parseFloat(match[2])
        }
    }
    return null
})

watch(() => form.location_map, async (newVal) => {
    if (!newVal) return
    if (newVal.includes('maps.app.goo.gl') || newVal.includes('goo.gl')) {
        try {
            const res = await fetch(route('resolve-maps-link'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ url: newVal })
            })
            const data = await res.json()
            if (data.success) {
                form.location_map = `${data.lat},${data.lng}`
            }
        } catch (err) {
            console.error(err)
        }
    }
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
                        v-for="item in localCheckoutItems"
                        :key="item.product_id"
                        class="flex items-center gap-4 border rounded-xl p-4 shadow-3xs"
                    >
                        <img :src="item.image" alt="produk" class="w-16 h-16 rounded-lg object-cover border" />
                        <div class="flex-1 text-left">
                            <p class="font-bold text-gray-900 text-sm md:text-base">{{ item.name }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Toko: {{ item.seller?.nama_toko || item.seller?.name || '-' }}</p>
                            <p class="text-xs text-gray-400 font-medium">Harga Satuan: Rp {{ formatCurrency(item.price) }}</p>
                            
                            <!-- Variations Display -->
                            <div v-if="item.variations && Object.keys(item.variations).length > 0" class="flex flex-wrap gap-1 mt-1.5">
                                <span 
                                    v-for="(val, key) in item.variations" 
                                    :key="key"
                                    class="inline-block bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-0.5 rounded border border-gray-200"
                                >
                                    {{ key }}: {{ val }}
                                </span>
                            </div>
                        </div>

                        <!-- Quantity Selector -->
                        <div class="flex items-center gap-1">
                            <button 
                                type="button" 
                                @click="changeQty(item, -1)" 
                                :disabled="item.qty <= 1"
                                class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:bg-gray-100 disabled:cursor-not-allowed transition select-none cursor-pointer text-sm font-bold"
                            >
                                &minus;
                            </button>
                            
                            <input
                                type="number"
                                v-model.number="item.qty"
                                @input="updateQtyInput(item)"
                                class="w-12 h-8 border border-gray-200 rounded-lg text-center text-sm font-bold focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                min="1"
                            />

                            <button 
                                type="button" 
                                @click="changeQty(item, 1)" 
                                :disabled="item.qty >= (item.stok ?? 9999)"
                                class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:bg-gray-100 disabled:cursor-not-allowed transition select-none cursor-pointer text-sm font-bold"
                            >
                                &plus;
                            </button>
                        </div>

                        <div class="text-right pl-4">
                            <span class="text-[10px] text-gray-400 block font-semibold uppercase">Subtotal</span>
                            <span class="font-extrabold text-blue-600 text-sm md:text-base">Rp {{ formatCurrency(item.subtotal) }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t flex items-center justify-between text-sm md:text-base">
                    <span class="font-medium text-gray-700">Total {{ localSummary.total_items }} item</span>
                    <span class="font-bold text-xl text-blue-700">Rp {{ formatCurrency(localSummary.total_price) }}</span>
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

                <div v-if="!allStoresHaveQris" class="p-3.5 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800 flex items-start gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <span>Metode pembayaran via QRIS dinonaktifkan karena ada toko yang belum mengunggah kode QRIS. Silakan bayar menggunakan metode <strong>CASH</strong>.</span>
                </div>

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