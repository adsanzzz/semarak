<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, router } from '@inertiajs/vue3'

const props = defineProps({
  products: Array,
})

function deactivateProduct(id) {
  if (confirm('Apakah Anda yakin ingin menonaktifkan produk ini?')) {
    router.post(route('admin.products.deactivate', id), {}, {
      onSuccess: () => {
        router.reload({ only: ['products'] })
      }
    })
  }
}
</script>

<template>
  <Head title="Kelola Produk" />

  <AdminLayout>
    <div class="py-12">
      <div class="p-6">
    <div class="bg-white shadow rounded-lg p-6">
          <div class="p-6">
            <h1 class="text-2xl font-semibold mb-4">Kelola Produk</h1>
            <p class="text-gray-600 mb-6">Produk yang dijual oleh semua toko, dengan aksi hapus.</p>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toko</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="product in products" :key="product.id">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ product.id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.nama }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.user?.name || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ Number(product.harga).toLocaleString('id-ID') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.stok }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <button
                        @click="deactivateProduct(product.id)"
                        class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 transition-colors"
                      >
                        Non-aktifkan
                      </button>
                    </td>
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
