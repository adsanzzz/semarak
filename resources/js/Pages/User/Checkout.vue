<script setup>
import { ref } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

const form = useForm({
    payment_method: 'qris'
})

function submit() {
    form.post(route('checkout.store'))
}
</script>

<template>
    <Head title="Checkout" />

    <div class="max-w-xl mx-auto p-6">

        <h2 class="text-2xl font-bold mb-6">
            Checkout
        </h2>

        <!-- METODE PEMBAYARAN -->
        <div class="mb-6">
            <label class="font-semibold block mb-2">
                Pilih Metode Pembayaran
            </label>

            <div class="space-y-3">

                <label class="flex items-center gap-3 border p-3 rounded cursor-pointer">
                    <input
                        type="radio"
                        value="qris"
                        v-model="form.payment_method"
                    >
                    <div>
                        <div class="font-semibold">QRIS</div>
                        <div class="text-sm text-gray-500">
                            Bayar sekarang via scan QR
                        </div>
                    </div>
                </label>

                <label class="flex items-center gap-3 border p-3 rounded cursor-pointer">
                    <input
                        type="radio"
                        value="cod"
                        v-model="form.payment_method"
                    >
                    <div>
                        <div class="font-semibold">COD</div>
                        <div class="text-sm text-gray-500">
                            Bayar saat barang diterima
                        </div>
                    </div>
                </label>

            </div>

            <div v-if="form.errors.payment_method" class="text-red-500 text-sm mt-2">
                {{ form.errors.payment_method }}
            </div>
        </div>

        <!-- BUTTON -->
        <button
            @click="submit"
            :disabled="form.processing"
            class="w-full bg-green-600 text-white py-3 rounded hover:bg-green-700"
        >
            Buat Pesanan
        </button>

    </div>
</template>