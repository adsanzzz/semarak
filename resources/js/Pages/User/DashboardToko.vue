<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({
      totalProduk: 0,
      totalPesanan: 0,
      totalPenjualan: 0,
      totalPendapatan: 0
    })
  },
  topProducts: {
    type: Array,
    default: () => []
  }
})
</script>

<template>
  <Head title="Dashboard Toko" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Dashboard Toko
      </h2>
    </template>

    <div class="flex">
      <!-- Sidebar -->
      <Sidebar class="h-screen fixed left-0 top-0 bg-white shadow-md" />

      <!-- Konten utama -->
  <div class="flex-1 ml-0 bg-gray-100 min-h-screen">
        <div class="py-10 px-6">
          <div class="max-w-7xl mx-auto space-y-8">
            
            <!-- 📊 Statistik Toko -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
              <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center">
                <span class="text-3xl">📦</span>
                <p class="mt-2 text-gray-500 text-sm">Total Produk</p>
                <h3 class="text-lg font-bold">{{ stats.totalProduk }}</h3>
              </div>
              <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center">
                <span class="text-3xl">📑</span>
                <p class="mt-2 text-gray-500 text-sm">Total Pesanan</p>
                <h3 class="text-lg font-bold">{{ stats.totalPesanan }}</h3>
              </div>
              <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center">
                <span class="text-3xl">🛒</span>
                <p class="mt-2 text-gray-500 text-sm">Total Penjualan</p>
                <h3 class="text-lg font-bold">{{ stats.totalPenjualan }}</h3>
              </div>
              <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center">
                <span class="text-3xl">💰</span>
                <p class="mt-2 text-gray-500 text-sm">Total Pendapatan</p>
                <h3 class="text-lg font-bold">Rp{{ Number(stats.totalPendapatan).toLocaleString('id-ID') }}</h3>
              </div>
            </div>

            <!-- 📌 List Produk Terlaris -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                  <h3 class="text-xl font-bold">Produk Terlaris</h3>
                </div>
                <div class="overflow-x-auto">
                  <table class="min-w-full text-sm">
                    <thead>
                      <tr class="text-left text-gray-500 border-b">
                        <th class="py-2 px-3">No</th>
                        <th class="py-2 px-3">Nama Produk</th>
                        <th class="py-2 px-3">Total Terjual</th>
                        <th class="py-2 px-3">Total Pendapatan</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="product in topProducts" :key="product.product_id" class="border-b hover:bg-gray-50">
                        <td class="py-2 px-3">{{ product.rank }}</td>
                        <td class="py-2 px-3">{{ product.nama }}</td>
                        <td class="py-2 px-3">{{ product.total_terjual }}</td>
                        <td class="py-2 px-3">Rp{{ Number(product.total_pendapatan).toLocaleString('id-ID') }}</td>
                      </tr>
                      <tr v-if="topProducts.length === 0">
                        <td colspan="4" class="py-6 px-3 text-center text-gray-500">Belum ada produk terjual.</td>
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
