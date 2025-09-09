<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'

const props = defineProps({
  products: Array,
})

function renderStars(rating) {
  const full = Math.floor(rating)
  const half = rating % 1 >= 0.5
  let stars = ''
  for (let i = 0; i < full; i++) stars += '★'
  if (half) stars += '☆'
  while (stars.length < 5) stars += '☆'
  return stars
}
</script>

<template>
  <Head title="Dashboard Buyer" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Dashboard Buyer
      </h2>
    </template>
    <div class="flex">
      <Sidebar class="fixed left-0 top-0 h-screen" />
      <div class="flex-1 ml-64 bg-gray-100 min-h-screen">
        <div class="py-12">
          <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
              <h3 class="text-xl font-bold mb-4">Produk Marketplace</h3>
              <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <div v-for="produk in products" :key="produk.id" class="bg-white rounded-2xl shadow-md p-4 flex flex-col items-center hover:scale-105 hover:shadow-xl transition transform duration-300 border border-orange-100">
                  <img :src="produk.image ? (produk.image.startsWith('produk/') ? '/storage/' + produk.image : produk.image) : ''" :alt="produk.nama" class="w-32 h-32 object-cover rounded mb-3" />
                  <div class="text-base font-semibold text-gray-800 mb-1 text-center">{{ produk.nama }}</div>
                  <div class="text-orange-600 font-bold mb-1">Rp{{ produk.harga.toLocaleString('id-ID') }}</div>
                  <div class="text-xs text-gray-500 mb-1">Kategori: {{ produk.kategori }}</div>
                  <div class="flex items-center mb-1">
                    <span class="text-yellow-400 text-lg">{{ renderStars(4.5) }}</span>
                    <span class="ml-1 text-xs text-gray-500">4.5</span>
                  </div>
                  <div class="text-xs text-gray-400 text-center line-clamp-2 mb-1">{{ produk.deskripsi }}</div>
                  <div class="text-xs text-gray-400">Terjual: {{ produk.terjual }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
