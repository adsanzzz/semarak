<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import NavbarAuth from '@/Components/NavbarAuth.vue'

const page = usePage()

const flashSuccess = computed(() => page.props.flash?.success)
const flashError = computed(() => page.props.flash?.error)
</script>

<template>
  <div>
    <NavbarAuth />

    <Head title="Checkout Berhasil" />

    <div class="max-w-2xl mx-auto p-6 space-y-4">
      <div
        v-if="flashSuccess"
        class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-700"
      >
        {{ flashSuccess }}
      </div>

      <div
        v-if="flashError"
        class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-700"
      >
        {{ flashError }}
      </div>

      <div class="bg-white border border-green-200 rounded-xl shadow-sm p-6 text-center">
        <h1 class="text-2xl font-bold text-green-700 mb-2">
          Pembelian Berhasil
        </h1>

        <p class="text-gray-600 mb-4">
          Pesanan Anda sudah masuk dan sedang menunggu konfirmasi dari penjual.
        </p>

        <div class="flex items-center justify-center gap-3">
          <Link
            :href="route('user.riwayat-pemesanan')"
            class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700"
          >
            Lihat Pesanan Saya
          </Link>

          <Link
            :href="route('produk.lihat')"
            class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50"
          >
            Kembali Belanja
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>