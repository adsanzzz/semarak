<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'

const props = defineProps({
  orders: Array,
})
</script>

<template>
<Head title="Riwayat Pemesanan" />

<AuthenticatedLayout>

<template #header>
<h2 class="text-xl font-semibold text-gray-800">
Riwayat Pemesanan
</h2>
</template>

<div class="flex">

<Sidebar class="fixed left-0 top-0 h-screen"/>

<div class="flex-1 bg-gray-100 min-h-screen p-8">

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
    class="border rounded-lg p-4 bg-white shadow-sm"
  >
    <div class="flex justify-between items-start mb-3">
      <div>
        <h4 class="font-semibold text-gray-800">{{ order.product?.nama || 'Produk tidak ditemukan' }}</h4>
        <p class="text-sm text-gray-600">Toko: {{ order.product?.user?.name || '-' }}</p>
      </div>
      <div class="text-right">
        <p class="text-sm text-gray-500">Order #{{ order.id }}</p>
        <p class="text-xs text-gray-400">{{ order.created_at }}</p>
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
      </div>
    </div>
  </div>
</div>

</div>
</div>
</div>

</AuthenticatedLayout>
</template>