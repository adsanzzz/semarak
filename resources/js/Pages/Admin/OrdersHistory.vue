<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
  orders: Array,
})
</script>

<template>
  <Head title="Histori Pesanan" />

  <AdminLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <h1 class="text-2xl font-semibold mb-4">Histori Pesanan</h1>
            <p class="text-gray-600 mb-6">Semua histori pemesanan dari pembeli.</p>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembeli</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Harga</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="order in orders" :key="order.id">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ order.id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ order.product?.nama || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ order.buyer?.name || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ order.jumlah }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ Number(order.total_harga).toLocaleString('id-ID') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                            :class="order.status === 'selesai' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                        {{ order.status }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ new Date(order.created_at).toLocaleDateString('id-ID') }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
