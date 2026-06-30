<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router, usePage, useForm } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'
import { computed, ref } from 'vue'

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

const activeFilter = ref('semua')
const selectedMonth = ref('')

function resetMonthFilter() {
  selectedMonth.value = ''
}

const dateFilteredOrders = computed(() => {
  let list = props.orders || []

  if (selectedMonth.value) {
    const [year, month] = selectedMonth.value.split('-').map(Number)
    list = list.filter(o => {
      const oDate = new Date(o.created_at)
      return oDate.getFullYear() === year && (oDate.getMonth() + 1) === month
    })
  }

  return list
})

const totalCount = computed(() => dateFilteredOrders.value.length)
const diprosesCount = computed(() => dateFilteredOrders.value.filter(o => o.status === 'diproses' || o.status === 'diterima').length)
const selesaiCount = computed(() => dateFilteredOrders.value.filter(o => o.status === 'selesai').length)
const dibatalkanCount = computed(() => dateFilteredOrders.value.filter(o => o.status === 'dibatalkan').length)

const filteredOrders = computed(() => {
  let list = dateFilteredOrders.value

  if (activeFilter.value === 'diproses') {
    list = list.filter(o => o.status === 'diproses' || o.status === 'diterima')
  } else if (activeFilter.value === 'selesai') {
    list = list.filter(o => o.status === 'selesai')
  } else if (activeFilter.value === 'dibatalkan') {
    list = list.filter(o => o.status === 'dibatalkan')
  }

  return list
})

const showReviewModal = ref(false)
const selectedReviewOrder = ref(null)
const reviewForm = useForm({
  rating: 0,
  review_text: '',
  review_image: null,
})
const reviewImagePreview = ref(null)

function toggleDetail(orderId) {
  openedOrderId.value = openedOrderId.value === orderId ? null : orderId
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

function paymentStatusLabel(status) {
  if (status === 'waiting_payment' || status === 'waiting_confirmation') return 'Menunggu Pembayaran'
  if (status === 'waiting_verification') return 'Menunggu Verifikasi Penjual'
  if (status === 'paid') return 'Lunas / Sudah Bayar'
  if (status === 'unpaid') return 'Belum Bayar'
  return status || '-'
}

function openReviewModal(order) {
  selectedReviewOrder.value = order
  reviewForm.rating = order.rating || 0
  reviewForm.review_text = order.review_text || ''
  reviewForm.review_image = null
  reviewImagePreview.value = order.review_image ? `/storage/${order.review_image}` : null
  showReviewModal.value = true
}

function setModalReviewRating(rating) {
  if (selectedReviewOrder.value?.rating) return
  reviewForm.rating = rating
}

function onReviewImageChange(event) {
  const file = event.target.files?.[0] || null
  reviewForm.review_image = file
  if (file) {
    reviewImagePreview.value = URL.createObjectURL(file)
  }
}

function submitModalReview() {
  if (reviewForm.rating === 0) {
    alert('Silakan pilih rating bintang terlebih dahulu.')
    return
  }

  reviewForm.post(route('orders.review', selectedReviewOrder.value.id), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      showReviewModal.value = false
      router.reload({ only: ['orders'] })
    }
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

<!-- Filter & Stats Header -->
<div class="mb-6 flex flex-col lg:flex-row gap-6 items-stretch justify-between animate-in fade-in slide-in-from-top-4 duration-300">
  <!-- Left Side: Interactive Stats cards -->
  <div class="flex-1 grid grid-cols-2 md:grid-cols-4 gap-4">
    <!-- Total Pesanan -->
    <div 
      @click="activeFilter = 'semua'"
      class="cursor-pointer p-4 rounded-xl border transition-all duration-300 shadow-sm hover:shadow-md hover:scale-[1.02] flex flex-col justify-between"
      :class="activeFilter === 'semua' ? 'bg-indigo-50 border-indigo-500 text-indigo-900 ring-2 ring-indigo-100 font-bold' : 'bg-white border-gray-200 text-gray-700 hover:border-gray-300 hover:bg-gray-50/50'"
    >
      <div class="flex justify-between items-center gap-2">
        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500" :class="activeFilter === 'semua' ? 'text-indigo-700' : ''">Total</span>
        <span class="text-base">📦</span>
      </div>
      <div class="mt-4">
        <div class="text-2xl font-black">{{ totalCount }}</div>
        <div class="text-[10px] text-gray-400 mt-1" :class="activeFilter === 'semua' ? 'text-indigo-500' : ''">Semua Pesanan</div>
      </div>
    </div>

    <!-- Diproses -->
    <div 
      @click="activeFilter = 'diproses'"
      class="cursor-pointer p-4 rounded-xl border transition-all duration-300 shadow-sm hover:shadow-md hover:scale-[1.02] flex flex-col justify-between"
      :class="activeFilter === 'diproses' ? 'bg-blue-50 border-blue-500 text-blue-900 ring-2 ring-blue-100 font-bold' : 'bg-white border-gray-200 text-gray-700 hover:border-gray-300 hover:bg-gray-50/50'"
    >
      <div class="flex justify-between items-center gap-2">
        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500" :class="activeFilter === 'diproses' ? 'text-blue-700' : ''">Diproses</span>
        <span class="text-base">⏳</span>
      </div>
      <div class="mt-4">
        <div class="text-2xl font-black">{{ diprosesCount }}</div>
        <div class="text-[10px] text-gray-400 mt-1" :class="activeFilter === 'diproses' ? 'text-blue-500' : ''">Sedang Diproses</div>
      </div>
    </div>

    <!-- Selesai -->
    <div 
      @click="activeFilter = 'selesai'"
      class="cursor-pointer p-4 rounded-xl border transition-all duration-300 shadow-sm hover:shadow-md hover:scale-[1.02] flex flex-col justify-between"
      :class="activeFilter === 'selesai' ? 'bg-green-50 border-green-500 text-green-900 ring-2 ring-green-100 font-bold' : 'bg-white border-gray-200 text-gray-700 hover:border-gray-300 hover:bg-gray-50/50'"
    >
      <div class="flex justify-between items-center gap-2">
        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500" :class="activeFilter === 'selesai' ? 'text-green-700' : ''">Selesai</span>
        <span class="text-base">✅</span>
      </div>
      <div class="mt-4">
        <div class="text-2xl font-black">{{ selesaiCount }}</div>
        <div class="text-[10px] text-gray-400 mt-1" :class="activeFilter === 'selesai' ? 'text-green-500' : ''">Pesanan Selesai</div>
      </div>
    </div>

    <!-- Dibatalkan -->
    <div 
      @click="activeFilter = 'dibatalkan'"
      class="cursor-pointer p-4 rounded-xl border transition-all duration-300 shadow-sm hover:shadow-md hover:scale-[1.02] flex flex-col justify-between"
      :class="activeFilter === 'dibatalkan' ? 'bg-red-50 border-red-500 text-red-900 ring-2 ring-red-100 font-bold' : 'bg-white border-gray-200 text-gray-700 hover:border-gray-300 hover:bg-gray-50/50'"
    >
      <div class="flex justify-between items-center gap-2">
        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500" :class="activeFilter === 'dibatalkan' ? 'text-red-700' : ''">Dibatalkan</span>
        <span class="text-base">❌</span>
      </div>
      <div class="mt-4">
        <div class="text-2xl font-black">{{ dibatalkanCount }}</div>
        <div class="text-[10px] text-gray-400 mt-1" :class="activeFilter === 'dibatalkan' ? 'text-red-500' : ''">Pesanan Dibatalkan</div>
      </div>
    </div>
  </div>

  <!-- Right Side: Month Filter Control Card -->
  <div class="w-full lg:w-72 bg-gray-50/50 rounded-xl border border-gray-200 p-4 flex flex-col justify-between gap-3 shadow-sm hover:border-gray-300 transition duration-300">
    <div class="flex items-center justify-between">
      <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Pilih Bulan & Tahun</span>
      <span class="text-sm">📅</span>
    </div>
    <div class="flex flex-col gap-2">
      <input 
        type="month" 
        v-model="selectedMonth" 
        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100 font-semibold text-gray-700 cursor-pointer"
      />
      <button 
        v-if="selectedMonth"
        type="button" 
        @click="resetMonthFilter"
        class="w-full text-center px-4 py-2 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-700 font-bold rounded-lg text-xs transition duration-200"
      >
        Reset Filter Bulan
      </button>
    </div>
  </div>
</div>

<div v-if="filteredOrders.length === 0" class="text-center py-8 text-gray-500">
  <p>Belum ada pemesanan</p>
</div>

<div v-else class="space-y-4">
  <div
    v-for="order in filteredOrders"
    :key="order.id"
    class="border rounded-lg p-4 bg-white shadow-sm cursor-pointer"
    @click="toggleDetail(order.id)"
  >
    <div class="flex justify-between items-start mb-3">
      <div>
        <h4 class="font-semibold text-gray-800">{{ order.product?.nama || 'Produk tidak ditemukan' }}</h4>
        <p class="text-sm text-gray-600">Toko: {{ order.product?.user?.nama_toko || order.product?.user?.name || '-' }}</p>
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
            'bg-blue-100 text-blue-800': order.status === 'diproses' || order.status === 'diterima',
            'bg-green-100 text-green-800': order.status === 'selesai',
            'bg-red-100 text-red-800': order.status === 'dibatalkan'
          }"
        >
          {{ order.status }}
        </span>

        <button
          v-if="order.status === 'selesai' && !order.rating"
          type="button"
          @click.stop="openReviewModal(order)"
          class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded bg-yellow-500 text-white text-xs font-semibold hover:bg-yellow-600 transition shadow-sm"
        >
          ⭐ Nilai
        </button>

        <button
          v-if="order.status === 'selesai' && order.rating"
          type="button"
          @click.stop="openReviewModal(order)"
          class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-semibold hover:bg-indigo-100 transition"
        >
          ⭐ Ulasan
        </button>

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
        <p>
          <span class="font-medium">Status Pembayaran:</span>
          <span
            class="ml-1 px-2 py-0.5 rounded text-xs font-semibold"
            :class="{
              'bg-yellow-100 text-yellow-800': order.payment_status === 'waiting_confirmation' || order.payment_status === 'waiting_payment',
              'bg-blue-100 text-blue-800': order.payment_status === 'waiting_verification',
              'bg-green-100 text-green-800': order.payment_status === 'paid',
              'bg-red-100 text-red-800': order.payment_status === 'unpaid'
            }"
          >
            {{ paymentStatusLabel(order.payment_status) }}
          </span>
        </p>
      </div>

      <div
  v-if="
    order.payment_method === 'qris' &&
    order.status === 'diterima' &&
    !order.payment_proof
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

      <div v-if="order.status === 'selesai'" class="mt-5 rounded-xl border border-indigo-200 bg-indigo-50/50 p-4">
        <div v-if="order.rating" class="space-y-2 text-sm text-indigo-950">
          <p class="font-bold text-gray-800">Ulasan Anda</p>
          <p class="text-yellow-500 text-base">{{ '★'.repeat(order.rating) }}{{ '☆'.repeat(5 - order.rating) }}</p>
          <p v-if="order.review_text" class="text-gray-700 leading-relaxed">{{ order.review_text }}</p>
          <img v-if="order.review_image" :src="order.review_image.startsWith('http') ? order.review_image : `/storage/${order.review_image}`" class="mt-2 h-20 w-20 rounded border object-cover bg-white" />
        </div>

        <div v-else class="flex justify-between items-center text-sm">
          <p class="text-indigo-900 font-semibold">Beri ulasan dan rating Anda untuk pesanan ini.</p>
          <button
            type="button"
            class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 shadow-sm"
            @click.stop="openReviewModal(order)"
          >
            Tulis Ulasan
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


<!-- 🔔 REVIEW / NILAI MODAL -->
<div
  v-if="showReviewModal"
  class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
  @click.self="showReviewModal = false"
>
  <div class="bg-white w-full max-w-lg rounded-xl shadow-xl p-6 space-y-4 animate-in fade-in zoom-in-95 duration-200">
    <div class="flex justify-between items-center border-b pb-3">
      <h3 class="text-lg font-bold text-gray-800">
        {{ selectedReviewOrder?.rating ? 'Detail Ulasan Anda' : 'Beri Ulasan Produk' }}
      </h3>
      <button @click="showReviewModal = false" class="text-gray-500 hover:text-gray-700 text-lg">✕</button>
    </div>

    <div class="space-y-4">
      <div class="flex gap-3 items-center bg-gray-50 p-3 rounded-lg">
        <img :src="selectedReviewOrder?.product?.image ? `/storage/${selectedReviewOrder.product.image}` : 'https://via.placeholder.com/150'" class="w-12 h-12 object-cover rounded border" />
        <div>
          <h4 class="font-semibold text-sm text-gray-900">{{ selectedReviewOrder?.product?.nama }}</h4>
          <p class="text-xs text-gray-500">Toko: {{ selectedReviewOrder?.product?.user?.nama_toko || selectedReviewOrder?.product?.user?.name }}</p>
        </div>
      </div>

      <!-- Rating selection -->
      <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700">Rating Produk</label>
        <div class="flex items-center gap-2 text-3xl">
          <button
            v-for="star in 5"
            :key="star"
            type="button"
            :disabled="!!selectedReviewOrder?.rating"
            class="transition hover:scale-110"
            :class="(reviewForm.rating >= star) ? 'text-yellow-400' : 'text-gray-300'"
            @click="setModalReviewRating(star)"
          >
            ★
          </button>
        </div>
      </div>

      <!-- Description text area -->
      <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700">Deskripsi Ulasan</label>
        <textarea
          v-model="reviewForm.review_text"
          rows="3"
          :disabled="!!selectedReviewOrder?.rating"
          class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100 disabled:bg-gray-50 disabled:text-gray-500"
          placeholder="Bagikan pengalaman Anda menggunakan produk ini..."
        ></textarea>
      </div>

      <!-- Photo upload (optional) -->
      <div v-if="!selectedReviewOrder?.rating" class="space-y-1">
        <label class="block text-sm font-medium text-gray-700">Foto Ulasan (Opsional)</label>
        <input
          type="file"
          accept="image/*"
          @change="onReviewImageChange"
          class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer"
        />
      </div>

      <!-- Image preview / Display -->
      <div v-if="reviewImagePreview" class="space-y-1">
        <p class="text-xs text-gray-500">{{ selectedReviewOrder?.rating ? 'Foto Ulasan:' : 'Preview Foto:' }}</p>
        <img :src="reviewImagePreview" alt="Foto Ulasan" class="h-32 w-32 rounded border object-cover bg-white" />
      </div>
    </div>

    <div class="flex justify-end gap-3 pt-3 border-t">
      <button
        type="button"
        @click="showReviewModal = false"
        class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50"
      >
        Tutup
      </button>
      <button
        v-if="!selectedReviewOrder?.rating"
        type="button"
        @click="submitModalReview"
        :disabled="reviewForm.processing"
        class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 disabled:opacity-50"
      >
        {{ reviewForm.processing ? 'Mengirim...' : 'Kirim Ulasan' }}
      </button>
    </div>
  </div>
</div>


</AuthenticatedLayout>



</template>