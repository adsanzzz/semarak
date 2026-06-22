<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import NavbarAuth from '@/Components/NavbarAuth.vue'

const props = defineProps({
  checkoutResult: {
    type: Object,
    default: () => null,
  },
})

const page = usePage()
const flashSuccess = computed(() => page.props.flash?.success)
const flashError = computed(() => page.props.flash?.error)
const showQrisModal = ref((props.checkoutResult?.payment_method || '') === 'qris')

function formatCurrency(value) {
  return new Intl.NumberFormat('id-ID').format(value || 0)
}

function buildQrisPayload(store) {
  const shopName = store?.nama_toko || store?.name || 'Toko'
  const bank = store?.bank_tujuan || ''
  const accountName = store?.nama_rekening || ''
  const accountNumber = store?.norek || ''

  return [
    'PEMBAYARAN QRIS',
    `TOKO: ${shopName}`,
    `BANK: ${bank}`,
    `NAMA REKENING: ${accountName}`,
    `NOMOR REKENING: ${accountNumber}`,
  ].join('\n')
}

function getQrisSource(store) {
  if (store?.qris_image) {
    return store.qris_image
  }

  const payload = encodeURIComponent(buildQrisPayload(store))
  return `https://api.qrserver.com/v1/create-qr-code/?size=360x360&data=${payload}`
}

function confirmPayment() {
  router.post(route('checkout.qris.confirm'))
}
</script>

<template>
  <div>
    <NavbarAuth />

    <Head title="Checkout Berhasil" />

    <div class="max-w-2xl mx-auto p-6 space-y-4">
      <div v-if="flashSuccess" class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-700">
        {{ flashSuccess }}
      </div>
      <div v-if="flashError" class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
        {{ flashError }}
      </div>

      <div class="bg-white border border-green-200 rounded-xl shadow-sm p-6 text-center">
        <h1 class="text-2xl font-bold text-green-700 mb-2">Pembelian Berhasil</h1>
        <p class="text-gray-600 mb-4">Pesanan Anda sudah dibuat. Jika memilih QRIS, silakan selesaikan pembayaran melalui kode QR di bawah.</p>

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

    <div
      v-if="showQrisModal"
      class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
      @click.self="showQrisModal = false"
    >
      <div class="bg-white w-full max-w-3xl rounded-xl shadow-xl p-5 max-h-[85vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-bold text-gray-800">QRIS Pembayaran</h3>
          <button type="button" @click="showQrisModal = false" class="text-gray-500 hover:text-gray-700">Tutup</button>
        </div>

        <div class="space-y-4">
          <div v-for="store in checkoutResult?.stores || []" :key="store.id" class="border rounded-lg p-4">
            <p class="font-semibold text-gray-900 mb-2">{{ store.nama_toko || store.name || 'Toko' }}</p>

            <img
              :src="getQrisSource(store)"
              alt="QRIS Toko"
              class="w-56 h-56 object-contain border rounded-lg mb-3 bg-white"
            />

            <div class="text-sm text-gray-600 space-y-1">
              <p><span class="font-medium">Bank:</span> {{ store.bank_tujuan || '-' }}</p>
              <p><span class="font-medium">Nama Rekening:</span> {{ store.nama_rekening || '-' }}</p>
              <p><span class="font-medium">No Rekening:</span> {{ store.norek || '-' }}</p>
            </div>
          </div>
        </div>

        <div class="mt-5 flex justify-end gap-3">
          <button
            type="button"
            @click="showQrisModal = false"
            class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50"
          >
            Nanti
          </button>
          <button
            type="button"
            @click="confirmPayment"
            class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700"
          >
            Saya Sudah Bayar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
