<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'

const props = defineProps({
  orders: Array,
})
</script>

<template>
  <Head title="Kelola Pesanan" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Kelola Pesanan
      </h2>
    </template>
    <div class="flex">
      <Sidebar class="fixed left-0 top-0 h-screen" />
      <div class="flex-1 ml-64 bg-gray-100 min-h-screen">
        <div class="py-12">
          <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
              <h3 class="text-xl font-bold mb-4">Daftar Pesanan</h3>
              <div v-if="orders.length === 0" class="text-gray-500">Belum ada pesanan.</div>
              <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead>
                    <tr>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pembeli</th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="order in orders" :key="order.id" class="bg-white even:bg-gray-50">
                      <td class="px-4 py-2">{{ order.id }}</td>
                      <td class="px-4 py-2">{{ order.product_nama }}</td>
                      <td class="px-4 py-2">{{ order.buyer_name }}</td>
                      <td class="px-4 py-2">{{ order.jumlah }}</td>
                      <td class="px-4 py-2">{{ order.status }}</td>
                      <td class="px-4 py-2">{{ order.created_at }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
