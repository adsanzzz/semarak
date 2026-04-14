<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'
import { ref } from 'vue'

const props = defineProps({
  orders: Array,
})

/* 🔔 Notifikasi */
const notif = ref(null)

function showNotif(message, type = 'success') {
  notif.value = { message, type }
  setTimeout(() => (notif.value = null), 3000)
}

/* =========================
   UPDATE STATUS PESANAN
========================= */

function updateStatus(order, statusBaru) {

  router.post(route('orders.updateStatus', order.id), {
    status: statusBaru
  }, {
    onSuccess: () => {
      showNotif('Status pesanan berhasil diperbarui')
      router.reload({ only: ['orders'] })
    }
  })
}

/* =========================
   HAPUS PESANAN (OPSIONAL)
========================= */

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

<!-- NOTIF -->
<div
v-if="notif"
class="fixed top-5 right-5 bg-green-100 border border-green-300 text-green-800 px-4 py-2 rounded shadow"
>
{{ notif.message }}
</div>

<!-- TABEL -->
<div class="bg-white rounded-lg shadow p-6">

<div class="flex justify-between mb-6">
<h3 class="text-lg font-semibold">
Daftar Pesanan
</h3>
</div>

<table class="w-full text-sm">

<thead>
<tr class="bg-gray-100">
<th class="p-3">ID</th>
<th class="p-3">Produk</th>
<th class="p-3">Pembeli</th>
<th class="p-3">Jumlah</th>
<th class="p-3">Status</th>
<th class="p-3">Tanggal</th>
<th class="p-3">Aksi</th>
</tr>
</thead>

<tbody>

<tr v-for="order in orders" :key="order.id">

<td class="p-3">{{ order.id }}</td>
<td class="p-3">{{ order.product_nama }}</td>
<td class="p-3">{{ order.buyer_name }}</td>
<td class="p-3">{{ order.jumlah }}</td>

<td class="p-3">
  <span class="px-2 py-1 rounded bg-gray-200">
    {{ order.status }}
  </span>
</td>

<td class="p-3">{{ order.created_at }}</td>

<td class="p-3 flex gap-2">

<!-- Ubah Status -->
<button
@click="updateStatus(order, 'diproses')"
class="text-blue-600"
>
Proses
</button>

<button
@click="updateStatus(order, 'selesai')"
class="text-green-600"
>
Selesai
</button>

<!-- Hapus -->
<button
@click="hapusPesanan(order.id)"
class="text-red-600"
>
Hapus
</button>

</td>

</tr>

</tbody>

</table>

</div>

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
