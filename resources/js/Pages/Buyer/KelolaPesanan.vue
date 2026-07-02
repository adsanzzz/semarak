<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'
import { computed, ref } from 'vue'

const props = defineProps({
  orders: Array,
})

const notif = ref(null)
const activeView = ref('baru')
const selectedOrder = ref(null)
const rejectingOrder = ref(null)
const rejectionReason = ref('Stok Barang Habis/Kosong')
const customRejectionReason = ref('')
const showProofModal = ref(false)
const selectedProof = ref(null)

const rejectionReasons = [
  'Stok Barang Habis/Kosong',
  'Alamat Luar Jangkauan',
  'Kelebihan Antrean Pesanan',
  'Lainnya',
]

const incomingOrders = computed(() =>
  (props.orders || []).filter((order) => !order.review_status || order.review_status === 'pending')
)

const acceptedOrders = computed(() =>
  (props.orders || [])
    .filter((order) => order.review_status === 'diterima')
    .sort((firstOrder, secondOrder) => {
      const statusPriority = {
        diproses: 1,
        diterima: 2,
        selesai: 3,
      }

      const firstPriority = statusPriority[firstOrder.status] || 99
      const secondPriority = statusPriority[secondOrder.status] || 99

      if (firstPriority !== secondPriority) {
        return firstPriority - secondPriority
      }

      if (firstOrder.status === 'selesai' && secondOrder.status === 'selesai') {
        return getOrderCreatedTimestamp(firstOrder) - getOrderCreatedTimestamp(secondOrder)
      }

      const firstTime = getAcceptedOrderTimestamp(firstOrder)
      const secondTime = getAcceptedOrderTimestamp(secondOrder)

      return secondTime - firstTime
    })
)

function getAcceptedOrderTimestamp(order) {
  const sourceField = 'created_at'
  return new Date(order[sourceField] || 0).getTime()
}

function getOrderCreatedTimestamp(order) {
  return new Date(order.created_at || 0).getTime()
}

const rejectedOrders = computed(() =>
  (props.orders || []).filter((order) => order.review_status === 'ditolak')
)

const visibleOrders = computed(() => {
  if (activeView.value === 'diterima') return acceptedOrders.value
  if (activeView.value === 'ditolak') return rejectedOrders.value
  return incomingOrders.value
})

function showNotif(message, type = 'success') {
  notif.value = { message, type }
  setTimeout(() => (notif.value = null), 3000)
}

function statusLabel(status) {
  if (status === 'diterima') return 'Diterima'
  if (status === 'ditolak') return 'Ditolak'
  if (status === 'pending') return 'Menunggu'
  if (status === 'diproses') return 'Diproses'
  if (status === 'selesai') return 'Selesai'
  if (status === 'dibatalkan') return 'Dibatalkan'
  return status || '-'
}

function paymentLabel(method) {
  if (method === 'qris') return 'QRIS'
  if (method === 'cod') return 'COD'
  return method ? method.toUpperCase() : '-'
}

function shippingLabel(method) {
  if (method === 'ambil_toko') return 'Ambil ke Toko'
  if (method === 'antar_rumah') return 'Antar ke Rumah'
  return '-'
}

function formatDate(value) {
  if (!value) return '-'
  return new Date(value).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

function acceptedOrderDate(order) {
  return formatDate(order.created_at)
}

function openProof(proof) {
  selectedProof.value = `/storage/${proof}`
  showProofModal.value = true
}

function openDetail(order) {
  selectedOrder.value = order
}

function handleRowClick(order) {
  if (activeView.value === 'diterima') return
  openDetail(order)
}

function closeDetail() {
  selectedOrder.value = null
}

function openRejectDialog(order) {
  rejectingOrder.value = order
  rejectionReason.value = rejectionReasons[0]
  customRejectionReason.value = ''
}

function closeRejectDialog() {
  rejectingOrder.value = null
}

function acceptOrder(order) {
  router.post(route('orders.accept', order.id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      showNotif('Pesanan berhasil diterima')
      closeDetail()
      router.reload({ only: ['orders'] })
    },
  })
}

function rejectOrder() {
  if (!rejectingOrder.value) return

  const finalReason = rejectionReason.value === 'Lainnya'
    ? customRejectionReason.value.trim()
    : rejectionReason.value

  if (!finalReason) {
    alert('Harap isi alasan penolakan manual.')
    return
  }

  router.post(route('orders.reject', rejectingOrder.value.id), {
    rejection_reason: finalReason,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      showNotif('Pesanan berhasil ditolak')
      closeRejectDialog()
      closeDetail()
      router.reload({ only: ['orders'] })
    },
  })
}

function updateAcceptedStatus(order, statusBaru) {
  router.post(route('orders.updateStatus', order.id), {
    status: statusBaru,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      showNotif('Status pesanan berhasil diperbarui')
      router.reload({ only: ['orders'] })
    },
  })
}

function confirmPayment(order) {
  if (!confirm('Apakah Anda yakin ingin mengonfirmasi pembayaran untuk pesanan ini?')) return

  router.post(route('orders.confirm-payment', order.id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      showNotif('Pembayaran berhasil dikonfirmasi')
      if (selectedOrder.value && selectedOrder.value.id === order.id) {
        selectedOrder.value.payment_status = 'paid'
      }
      router.reload({ only: ['orders'] })
    },
  })
}

function hapusPesanan(id) {
  if (!confirm('Yakin ingin menghapus pesanan ini?')) return

  router.delete(route('orders.destroy', id), {
    onSuccess: () => {
      showNotif('Pesanan berhasil dihapus')
      router.reload({ only: ['orders'] })
    }
  })
}
</script>

<template>

<Head title="Kelola Pesanan" />

<AuthenticatedLayout>

<template #header>
<h2 class="text-xl font-semibold text-gray-800">
Kelola Pesanan
</h2>
</template>

<div class="flex">

<Sidebar class="fixed left-0 top-0 h-screen"/>

<div class="flex-1 bg-gray-100 min-h-screen p-8">

<div
v-if="notif"
class="fixed top-5 right-5 z-50 bg-green-100 border border-green-300 text-green-800 px-4 py-2 rounded shadow"
>
{{ notif.message }}
</div>

<div class="bg-white rounded-2xl shadow p-6">

<div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between mb-6">
<div>
<h3 class="text-lg font-semibold text-gray-800">
Kelola Pesanan
</h3>
<p class="text-sm text-gray-500">
Klik baris pesanan untuk melihat detail lengkap.
</p>
</div>

<label class="flex flex-col gap-2 text-sm font-medium text-gray-700">
Tampilan Pesanan
<select
v-model="activeView"
class="min-w-[220px] rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
>
  <option value="baru">Pesanan Baru</option>
  <option value="diterima">Pesanan Diterima</option>
  <option value="ditolak">Pesanan Ditolak</option>
</select>
</label>
</div>

<div class="overflow-x-auto">
<table v-if="activeView === 'ditolak'" class="min-w-[900px] w-full text-sm">

<thead>
<tr class="bg-gray-100">
<th class="p-3 text-left">ID</th>
<th class="p-3 text-left">Produk</th>
<th class="p-3 text-left">Pembeli</th>
<th class="p-3 text-left">Status</th>
<th class="p-3 text-left">Alasan</th>
</tr>
</thead>

<tbody>

<tr
v-for="order in visibleOrders"
:key="order.id"
class="border-b hover:bg-gray-50"
@click="handleRowClick(order)"
>

<td class="p-3 whitespace-nowrap">{{ order.id }}</td>
<td class="p-3">{{ order.product?.nama || '-' }}</td>
<td class="p-3">{{ order.buyer?.name || '-' }}</td>

<td class="p-3">
  <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
    {{ statusLabel(order.review_status) }}
  </span>
</td>

<td class="p-3">{{ order.rejection_reason || '-' }}</td>

</tr>

<tr v-if="visibleOrders.length === 0">
  <td colspan="5" class="px-6 py-10 text-center text-gray-500">
    Belum ada pesanan pada tampilan ini.
  </td>
</tr>

</tbody>

</table>

<table v-else class="min-w-[1200px] w-full text-sm">

<thead>
<tr class="bg-gray-100">
<th class="p-3 text-left">ID</th>
<th class="p-3 text-left">Produk</th>
<th class="p-3 text-left">Pembeli</th>
<th class="p-3 text-left">Jumlah</th>
<th class="p-3 text-left">Pembayaran</th>
<th class="p-3 text-left">Metode</th>
<th class="p-3 text-left">Status</th>
<th class="p-3 text-left">Tanggal</th>
<th class="p-3 text-left">Bukti Pembayaran</th>
<th class="p-3 text-right">Aksi</th>
</tr>
</thead>

<tbody>

<tr
v-for="order in visibleOrders"
:key="order.id"
:class="[
  'border-b hover:bg-gray-50',
  activeView === 'diterima' ? '' : 'cursor-pointer'
]"
@click="handleRowClick(order)"
>

<td class="p-3 whitespace-nowrap">{{ order.id }}</td>
<td class="p-3">{{ order.product?.nama || '-' }}</td>
<td class="p-3">{{ order.buyer?.name || '-' }}</td>
<td class="p-3 whitespace-nowrap">{{ order.jumlah }}</td>

<td class="p-3 whitespace-nowrap">
  <span
    class="px-2 py-1 rounded text-xs font-medium"
    :class="order.payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
  >
    {{ order.payment_status === 'paid' ? 'Sudah Bayar' : 'Belum Bayar' }}
  </span>
</td>

<td class="p-3 whitespace-nowrap">
  <span class="px-2 py-1 rounded bg-gray-100 text-xs font-medium text-gray-700">
    {{ paymentLabel(order.payment_method) }}
  </span>
</td>

<td class="p-3 min-w-[180px] whitespace-nowrap">
  <select
    v-if="activeView === 'diterima'"
    :value="order.status"
    @change.stop="updateAcceptedStatus(order, $event.target.value)"
    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
  >
    <option v-if="order.status === 'diterima'" value="diterima" disabled hidden>Pilih Status</option>
    <option value="diproses">Diproses</option>
    <option value="selesai">Selesai</option>
  </select>

  <span
    v-else
    class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
    :class="{
      'bg-yellow-100 text-yellow-700': order.status === 'pending',
      'bg-blue-100 text-blue-700': order.status === 'diproses' || order.status === 'diterima',
      'bg-green-100 text-green-700': order.status === 'selesai',
      'bg-red-100 text-red-700': order.status === 'dibatalkan',
    }"
  >
    {{ statusLabel(order.status) }}
  </span>
</td>

<td class="p-3 whitespace-nowrap">{{ acceptedOrderDate(order) }}</td>
<td class="p-3 text-center">
  <button
    v-if="order.payment_proof"
    @click.stop="openProof(order.payment_proof)"
    class="rounded-md bg-blue-600 px-3 py-1 text-white hover:bg-blue-700"
  >
    Lihat Bukti
  </button>

  <span
    v-else
    class="text-gray-400 text-sm"
  >
    Belum Ada
  </span>
</td>
<td class="p-3">
<div class="flex items-center justify-end gap-2" @click.stop>

<button
  v-if="activeView === 'diterima' && order.payment_proof && order.payment_status !== 'paid'"
  @click="confirmPayment(order)"
  class="rounded-md bg-purple-600 px-3 py-1 text-white hover:bg-purple-700 text-xs font-semibold"
>
  Konfirmasi Bayar
</button>

<button
v-if="activeView === 'baru'"
@click="acceptOrder(order)"
class="rounded-md bg-green-600 px-3 py-1 text-white hover:bg-green-700"
>
Terima Pesanan
</button>

<button
v-if="activeView === 'baru'"
@click="openRejectDialog(order)"
class="rounded-md bg-red-600 px-3 py-1 text-white hover:bg-red-700"
>
Tolak Pesanan
</button>

<button
v-else
@click="openDetail(order)"
class="rounded-md bg-slate-700 px-3 py-1 text-white hover:bg-slate-800"
>
Detail
</button>

<!-- Tombol chat untuk pesanan baru & diterima -->
<a
  v-if="(activeView === 'diterima' || activeView === 'baru') && order.buyer"
  :href="route('chat.start', { seller: order.buyer.id, order_id: order.id })"
  class="rounded-md bg-blue-600 px-3 py-1 text-white hover:bg-blue-700"
>
  Chat Pembeli
</a>

</div>
</td>

</tr>

<tr v-if="visibleOrders.length === 0">
  <td :colspan="10" class="px-6 py-10 text-center text-gray-500">
    Belum ada pesanan pada tampilan ini.
  </td>
</tr>

</tbody>

</table>
</div>

</div>

<transition name="fade">
  <div
    v-if="selectedOrder"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
    @click.self="closeDetail"
  >
    <div class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-2xl">
      <div class="mb-4 flex items-start justify-between gap-4">
        <div>
          <h4 class="text-lg font-semibold text-gray-800">Detail Pesanan #{{ selectedOrder.id }}</h4>
          <p class="text-sm text-gray-500">{{ formatDate(selectedOrder.created_at) }}</p>
        </div>
        <button class="text-gray-500 hover:text-gray-800" @click="closeDetail">Tutup</button>
      </div>

      <div class="grid gap-3 md:grid-cols-2 text-sm text-gray-700">
        <p><span class="font-medium">Produk:</span> {{ selectedOrder.product?.nama || '-' }}</p>
        <p><span class="font-medium">Pembeli:</span> {{ selectedOrder.buyer?.name || '-' }}</p>
        <p><span class="font-medium">Jumlah:</span> {{ selectedOrder.jumlah }}</p>
        <p><span class="font-medium">Total:</span> Rp {{ Number(selectedOrder.total_harga || 0).toLocaleString('id-ID') }}</p>
        <p><span class="font-medium">Pembayaran:</span> {{ paymentLabel(selectedOrder.payment_method) }}</p>
        <p><span class="font-medium">Metode:</span> {{ shippingLabel(selectedOrder.shipping_method) }}</p>
        <p><span class="font-medium">Status Order:</span> {{ statusLabel(selectedOrder.status) }}</p>
        <p><span class="font-medium">Status Review:</span> {{ statusLabel(selectedOrder.review_status) }}</p>
      </div>

      <div v-if="selectedOrder.payment_proof" class="mt-5 p-4 border border-purple-100 rounded-xl bg-purple-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <p class="font-semibold text-purple-900">Bukti Pembayaran Tersedia</p>
          <button
            type="button"
            @click="openProof(selectedOrder.payment_proof)"
            class="text-xs font-semibold text-blue-600 hover:text-blue-800 underline mt-0.5"
          >
            Lihat Bukti Transfer
          </button>
        </div>
        <div v-if="selectedOrder.review_status === 'diterima' && selectedOrder.payment_status !== 'paid'">
          <button
            type="button"
            @click="confirmPayment(selectedOrder)"
            class="w-full sm:w-auto px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-sm transition"
          >
            Konfirmasi Pembayaran
          </button>
        </div>
        <div v-else>
          <span class="inline-flex items-center gap-1 text-green-700 font-bold text-xs bg-green-100 px-3 py-1.5 rounded-full border border-green-200">
            ✓ Pembayaran Lunas
          </span>
        </div>
      </div>

      <div v-if="selectedOrder.rejection_reason" class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <span class="font-semibold">Alasan Penolakan:</span> {{ selectedOrder.rejection_reason }}
      </div>
    </div>
  </div>
</transition>

<transition name="fade">
  <div
    v-if="rejectingOrder"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
    @click.self="closeRejectDialog"
  >
    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl animate-in fade-in zoom-in-95 duration-200">
      <div class="mb-4">
        <h4 class="text-lg font-semibold text-gray-800">Tolak Pesanan #{{ rejectingOrder.id }}</h4>
        <p class="text-sm text-gray-500">Pilih salah satu alasan atau tulis secara manual di bawah.</p>
      </div>

      <div class="space-y-3">
        <label
          v-for="reason in rejectionReasons"
          :key="reason"
          class="flex cursor-pointer items-center gap-3 rounded-xl border px-4 py-3 text-sm hover:bg-gray-50 transition duration-200"
          :class="rejectionReason === reason ? 'border-red-200 bg-red-50/20' : 'border-gray-200'"
        >
          <input v-model="rejectionReason" type="radio" :value="reason" class="text-red-600 focus:ring-red-500" />
          <span>{{ reason }}</span>
        </label>

        <!-- Textarea for manual reason -->
        <transition name="fade">
          <div v-if="rejectionReason === 'Lainnya'" class="mt-3 space-y-1.5 animate-in fade-in slide-in-from-top-2 duration-200">
            <label class="block text-xs font-semibold text-gray-500">Alasan Penolakan Manual:</label>
            <textarea
              v-model="customRejectionReason"
              rows="3"
              required
              class="w-full rounded-xl border border-gray-300 p-3 text-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100 font-medium"
              placeholder="Tulis alasan penolakan secara spesifik di sini..."
            ></textarea>
          </div>
        </transition>
      </div>

      <div class="mt-6 flex justify-end gap-3">
        <button class="rounded-xl border px-4 py-2 text-sm text-gray-700 hover:bg-gray-50" @click="closeRejectDialog">
          Batal
        </button>
        <button 
          class="rounded-xl bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed font-semibold shadow-sm"
          :disabled="rejectionReason === 'Lainnya' && !customRejectionReason.trim()"
          @click="rejectOrder"
        >
          Konfirmasi Tolak
        </button>
      </div>
    </div>
  </div>
</transition>

<transition name="fade">
  <div
    v-if="showProofModal"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
    @click.self="showProofModal = false"
  >
    <div class="w-full max-w-3xl rounded-2xl bg-white p-6 shadow-2xl">

      <div class="mb-4 flex justify-between items-center">
        <h4 class="text-lg font-semibold text-gray-800">
          Bukti Pembayaran
        </h4>

        <button
          @click="showProofModal = false"
          class="text-gray-500 hover:text-gray-800"
        >
          Tutup
        </button>
      </div>

      <div class="flex justify-center">
        <img
          :src="selectedProof"
          alt="Bukti Pembayaran"
          class="max-h-[600px] rounded-xl border"
        />
      </div>

    </div>
  </div>
</transition>
</div>

</div>

</AuthenticatedLayout>

</template>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: none; }
}
.animate-fadeIn { animation: fadeIn 0.3s; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.5s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
