<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'

// chartjs
import { Pie } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from 'chart.js'
import { computed } from 'vue'

ChartJS.register(Title, Tooltip, Legend, ArcElement)

const props = defineProps({
  categories: { type: Array, default: () => [] },
  products: { type: Array, default: () => [] },
  stats: {
    type: Object,
    default: () => ({
      totalProduk: 0,
      totalKategori: 0,
      totalPenjualan: 0,
      totalPendapatan: 0
    })
  },
  topProducts: { // data produk terlaris
    type: Array,
    default: () => []
  },
  totalSales: { // total penjualan (rupiah)
    type: Number,
    default: 0
  }
})

// chart data untuk produk terlaris
const chartData = computed(() => ({
  labels: props.topProducts.map(p => p.nama),
  datasets: [
    {
      label: 'Jumlah Dibeli',
      data: props.topProducts.map(p => p.jumlah),
      backgroundColor: ['#2563eb', '#f59e0b', '#f97316', '#facc15', '#14b8a6']
    }
  ]
}))
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
                <span class="text-3xl">📂</span>
                <p class="mt-2 text-gray-500 text-sm">Total Kategori</p>
                <h3 class="text-lg font-bold">{{ stats.totalKategori }}</h3>
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

            <!-- 📌 Produk & Produk Terlaris -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
              
              <!-- 📌 Produk di Marketplace (tabel) -->
              <div class="bg-white rounded-xl shadow-md p-6 lg:col-span-2">
                <div class="flex justify-between items-center mb-4">
                  <h3 class="text-xl font-bold">Pesanan Terbaru</h3>
                  <button class="text-sm text-blue-600 hover:underline">Lihat Semua</button>
                </div>
                <div class="overflow-x-auto">
                  <table class="min-w-full text-sm">
                    <thead>
                      <tr class="text-left text-gray-500 border-b">
                        <th class="py-2 px-3">Nama Item</th>
                        <th class="py-2 px-3">Tanggal</th>
                        <th class="py-2 px-3">Total</th>
                        <th class="py-2 px-3">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="p in products" :key="p.id" class="border-b hover:bg-gray-50">
                        <td class="py-2 px-3">{{ p.nama }}</td>
                        <td class="py-2 px-3">{{ p.tanggal }}</td>
                        <td class="py-2 px-3">Rp{{ Number(p.harga).toLocaleString('id-ID') }}</td>
                        <td class="py-2 px-3">
                          <span class="px-2 py-1 rounded text-xs"
                                :class="p.status === 'Sedang Dikirim' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700'">
                            {{ p.status }}
                          </span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- 📌 Produk Terlaris -->
              <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-sm text-gray-500 mb-1">Produk Terlaris</h3>
                <h2 class="text-2xl font-bold text-blue-600 mb-4">
                  Rp {{ Number(totalSales).toLocaleString('id-ID') }}
                  <span class="text-sm text-gray-500 font-normal">— Total Penjualan</span>
                </h2>

                <div class="space-y-2 mb-4">
                  <div v-for="(prod, i) in topProducts" :key="i" class="flex justify-between text-sm">
                    <span class="px-2 py-1 rounded bg-gray-100">{{ prod.nama }}</span>
                    <span class="text-gray-500">{{ prod.jumlah }}x Dibeli</span>
                  </div>
                </div>

                <div style="height:250px">
                  <Pie :data="chartData" :options="{ responsive: true, maintainAspectRatio: false }" />
                </div>
              </div>

            </div>

          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
