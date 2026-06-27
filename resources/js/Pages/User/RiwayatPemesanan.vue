<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'
import { computed, reactive, ref } from 'vue'

const props = defineProps({
  orders: Array,
})

const page = usePage()
const flashSuccess = computed(() => page.props.flash?.success)
const openedOrderId = ref(null)

const showQrisModal = ref(false)
const selectedOrder = ref(null)

const showUploadModal = ref(false)
const paymentProof = ref(null)

const reviewForms = reactive({})

function toggleDetail(orderId) {
  openedOrderId.value = openedOrderId.value === orderId ? null : orderId

  if (openedOrderId.value) {
    const order = props.orders.find((item) => item.id === openedOrderId.value)

    if (order && !reviewForms[order.id]) {
      reviewForms[order.id] = {
        rating: order.rating || 0,
        review_text: order.review_text || '',
      }
    }
  }
}

function formatDate(value) {
  if (!value) return '-'
  return new Date(value).toLocaleString('id-ID')
}

function shippingLabel(method) {
  if (method === 'ambil_toko') return 'Ambil ke Toko'
  if (method === 'antar_rumah') return 'Antar ke Rumah'
  return '-'
}

function paymentLabel(method) {
  if (method === 'qris') return 'QRIS'
  if (method === 'cod') return 'COD'
  return '-'
}

function setReviewRating(orderId, rating) {
  reviewForms[orderId] = reviewForms[orderId] || { rating: 0, review_text: '' }
  reviewForms[orderId].rating = rating
}

function submitReview(order) {
  const form = reviewForms[order.id] || { rating: 0, review_text: '' }

  router.post(route('orders.review', order.id), {
    rating: form.rating,
    review_text: form.review_text,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      router.reload({ only: ['orders'] })
    },
  })
}

function openQrisModal(order) {
  selectedOrder.value = order
  showQrisModal.value = true
}

function openUploadModal(order) {
  selectedOrder.value = order
  paymentProof.value = null
  showUploadModal.value = true
}

function uploadPaymentProof() {
  if (!paymentProof.value) {
    alert('Silakan pilih bukti pembayaran terlebih dahulu.')
    return
  }

   if (!selectedOrder.value) {
    alert('Order tidak ditemukan.')
    return
  }

    const formData = new FormData()

  formData.append('order_id', selectedOrder.value.id)
formData.append('payment_proof', paymentProof.value)

    router.post(route('checkout.upload-proof'), formData, {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      showUploadModal.value = false
      showQrisModal.value = false
    },
  })
}
</script>

<template>
<Head title="Riwayat Pemesanan" />

<div
  v-if="showQrisModal"
  class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
  @click.self="showQrisModal = false"
>
  <div class="bg-white w-full max-w-lg rounded-xl shadow-xl p-6">

    <div class="flex justify-between items-center mb-4">
      <h3 class="text-lg font-bold">
        Pembayaran QRIS
      </h3>

      <button
        @click="showQrisModal = false"
        class="text-gray-500"
      >
        ✕
      </button>
    </div>

    <div class="text-center">

      <p class="text-sm text-gray-500">
        Jumlah yang Harus Dibayar
      </p>

      <p class="text-2xl font-bold text-green-600 mb-4">
        Rp {{ Number(selectedOrder?.total_harga || 0).toLocaleString('id-ID') }}
      </p>

      <img
        :src="selectedOrder?.product?.user?.qris_image"
        alt="QRIS"
        class="w-64 mx-auto border rounded-lg"
      />

      <div class="mt-4 text-sm text-gray-700">
        <p>
          <b>Bank:</b>
          {{ selectedOrder?.product?.user?.bank_tujuan }}
        </p>

        <p>
          <b>Nama Rekening:</b>
          {{ selectedOrder?.product?.user?.nama_rekening }}
        </p>

        <p>
          <b>No Rekening:</b>
          {{ selectedOrder?.product?.user?.norek }}
        </p>
      </div>

      <div class="mt-6 flex justify-center gap-3">
  <button
    type="button"
    @click="showQrisModal = false"
    class="px-4 py-2 border rounded-lg"
  >
    Tutup
  </button>

  <button
    type="button"
    @click="openUploadModal(selectedOrder)"
    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
  >
    Upload Bukti Pembayaran
  </button>
</div>

    </div>
  </div>
</div>

<AuthenticatedLayout>

<template #header>
<h2 class="text-xl font-semibold text-gray-800">
Riwayat Pemesanan
</h2>
</template>

<div class="flex">

<Sidebar class="fixed left-0 top-0 h-screen"/>

<div class="flex-1 bg-gray-100 min-h-screen p-8">

<div
v-if="flashSuccess"
class="mb-4 rounded-lg border border-green-300 bg-green-100 px-4 py-3 text-green-800"
>
{{ flashSuccess }}
</div>

<!-- TABEL -->
<div class="bg-white rounded-lg shadow p-6">

<div class="flex justify-between mb-6">
<h3 class="text-lg font-semibold">
Daftar Pemesanan Saya
</h3>
</div>

<div v-if="orders.length === 0" class="text-center py-8 text-gray-500">
  <p>Belum ada pemesanan</p>
</div>

<div v-else class="space-y-4">
  <div
    v-for="order in orders"
    :key="order.id"
    class="border rounded-lg p-4 bg-white shadow-sm cursor-pointer"
    @click="toggleDetail(order.id)"
  >
    <div class="flex justify-between items-start mb-3">
      <div>
        <h4 class="font-semibold text-gray-800">{{ order.product?.nama || 'Produk tidak ditemukan' }}</h4>
        <p class="text-sm text-gray-600">Toko: {{ order.product?.user?.name || '-' }}</p>
      </div>
      <div class="text-right">
        <p class="text-sm text-gray-500">Order #{{ order.id }}</p>
        <p class="text-xs text-gray-400">{{ formatDate(order.created_at) }}</p>
      </div>
    </div>

    <div class="flex justify-between items-center">
      <div class="flex items-center gap-4">
        <span class="text-sm">Jumlah: {{ order.jumlah }}</span>
        <span class="text-sm font-semibold">Rp {{ Number(order.total_harga || 0).toLocaleString() }}</span>
      </div>

      <div class="flex items-center gap-2">
        <span
          class="px-3 py-1 rounded-full text-xs font-medium"
          :class="{
            'bg-yellow-100 text-yellow-800': order.status === 'pending',
            'bg-blue-100 text-blue-800': order.status === 'diproses',
            'bg-green-100 text-green-800': order.status === 'selesai',
            'bg-red-100 text-red-800': order.status === 'dibatalkan'
          }"
        >
          {{ order.status }}
        </span>

        <Link
          v-if="order.product?.user_id"
          :href="route('chat.start', { seller: order.product.user_id })"
          @click.stop
          class="inline-flex items-center gap-2 px-3 py-2 rounded-md border border-blue-200 bg-blue-50 text-blue-700 text-xs font-semibold hover:bg-blue-100 transition-colors"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            class="h-4 w-4"
            aria-hidden="true"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M8.625 9.75h6.75m-6.75 3h4.5M3 12c0 4.97 4.03 9 9 9a8.96 8.96 0 004.664-1.294L21 21l-1.293-4.336A8.96 8.96 0 0021 12c0-4.97-4.03-9-9-9s-9 4.03-9 9z"
            />
          </svg>
          <span>chat penjual</span>
        </Link>
      </div>
    </div>

    <div v-if="openedOrderId === order.id" class="mt-4 rounded-lg border bg-gray-50 p-4 text-sm text-gray-700">
      <h5 class="font-semibold text-gray-800 mb-3">Detail Pesanan</h5>

      <div class="grid md:grid-cols-2 gap-3">
        <p><span class="font-medium">ID Pesanan:</span> #{{ order.id }}</p>
        <p><span class="font-medium">Tanggal:</span> {{ formatDate(order.created_at) }}</p>
        <p><span class="font-medium">Produk:</span> {{ order.product?.nama || '-' }}</p>
        <p><span class="font-medium">Toko:</span> {{ order.product?.user?.nama_toko || order.product?.user?.name || '-' }}</p>
        <p><span class="font-medium">Jumlah:</span> {{ order.jumlah }}</p>
        <p><span class="font-medium">Total:</span> Rp {{ Number(order.total_harga || 0).toLocaleString('id-ID') }}</p>
        <p><span class="font-medium">Pengiriman:</span> {{ shippingLabel(order.shipping_method) }}</p>
        <p><span class="font-medium">Pembayaran:</span> {{ paymentLabel(order.payment_method) }}</p>
      </div>

      <div
  v-if="
    order.payment_method === 'qris' &&
    order.status === 'diterima'
  "
  class="mt-4"
>
  <button
    type="button"
    @click.stop="openQrisModal(order)"
    class="rounded-lg bg-green-600 px-4 py-2 text-white hover:bg-green-700"
  >
    Bayar Sekarang
  </button>
</div>

      <div v-if="order.shipping_method === 'antar_rumah'" class="mt-3">
        <p class="font-medium">Lokasi Antar:</p>
        <a
          v-if="order.delivery_location"
          :href="order.delivery_location"
          target="_blank"
          rel="noopener noreferrer"
          class="text-blue-600 hover:underline"
          @click.stop
        >
          Lihat Lokasi Maps
        </a>
        <p v-else>-</p>
      </div>

      <div v-if="order.status === 'selesai'" class="mt-5 rounded-xl border border-blue-200 bg-blue-50 p-4">
        <div v-if="order.rating" class="space-y-2 text-sm text-blue-900">
          <p class="font-semibold">Ulasan Anda</p>
          <p class="text-yellow-500 text-base">{{ '★'.repeat(order.rating) }}{{ '☆'.repeat(5 - order.rating) }}</p>
          <p v-if="order.review_text">{{ order.review_text }}</p>
        </div>

        <div v-else class="space-y-3">
          <p class="font-semibold text-blue-900">Pesanan ini selesai. Beri rating dan ulasan untuk produk.</p>

          <div class="flex items-center gap-2 text-2xl">
            <button
              v-for="star in 5"
              :key="star"
              type="button"
              class="transition hover:scale-110"
              :class="(reviewForms[order.id]?.rating || 0) >= star ? 'text-yellow-400' : 'text-gray-300'"
              @click="setReviewRating(order.id, star)"
            >
              ★
            </button>
          </div>

          <textarea
            v-model="reviewForms[order.id].review_text"
            rows="3"
            class="w-full rounded-xl border border-blue-200 bg-white px-4 py-3 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
            placeholder="Tulis ulasan singkat untuk produk ini"
          ></textarea>

          <button
            type="button"
            class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
            @click="submitReview(order)"
          >
            Kirim Ulasan
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

</div>
</div>
</div>


<div
  v-if="showUploadModal"
  class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
>
  <div class="bg-white w-full max-w-md rounded-xl shadow-xl p-5">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-bold text-gray-800">
        Upload Bukti Pembayaran
      </h3>

      <button
        @click="showUploadModal = false"
        class="text-gray-500"
      >
        ✕
      </button>
    </div>

    <input
      type="file"
      accept="image/*"
      @change="paymentProof = $event.target.files[0]"
      class="w-full border rounded-lg p-2"
    />

    <div class="mt-5 flex justify-end gap-3">
      <button
        type="button"
        @click="showUploadModal = false"
        class="px-4 py-2 rounded-lg border border-gray-300"
      >
        Batal
      </button>

      <button
        type="button"
        @click="uploadPaymentProof"
        class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700"
      >
        Upload
      </button>
    </div>
  </div>
</div>


  <div class="bg-white w-full max-w-md rounded-xl shadow-xl p-5">

    <div class="flex justify-between items-center mb-4">
      <h3 class="text-lg font-bold">
        Upload Bukti Pembayaran
      </h3>

      <button
        type="button"
        @click="showUploadModal = false"
        class="text-gray-500"
      >
        ✕
      </button>
    </div>

    <input
      type="file"
      accept="image/*"
      @change="paymentProof = $event.target.files[0]"
      class="w-full border rounded-lg p-2"
    />

    <div class="mt-5 flex justify-end gap-3">

  

      <button
        type="button"
        @click="uploadPaymentProof"
        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
      >
        Upload
      </button>

    </div>

  </div>


</AuthenticatedLayout>



</template>