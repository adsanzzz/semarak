<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
  products: Array,
  filters: Object,
})

const searchVal = ref(props.filters.search || '')
const suspiciousOnly = ref(props.filters.suspicious || false)

function applyFilters() {
  router.get(route('admin.products'), {
    search: searchVal.value,
    suspicious: suspiciousOnly.value ? '1' : '0'
  }, {
    preserveState: true,
    replace: true
  })
}

watch(suspiciousOnly, () => {
  applyFilters()
})

function handleSearch(e) {
  e.preventDefault()
  applyFilters()
}

/* =========================
   DEACTIVATION & ACTIVATION
========================= */
const showDeactivateModal = ref(false)
const selectedProductId = ref(null)
const deactivateReason = ref('')

function openDeactivateModal(id) {
  selectedProductId.value = id
  deactivateReason.value = ''
  showDeactivateModal.value = true
}

function confirmDeactivate() {
  if (!deactivateReason.value.trim()) {
    alert('Alasan dinonaktifkan wajib diisi.')
    return
  }

  router.post(route('admin.products.deactivate', selectedProductId.value), {
    reason: deactivateReason.value
  }, {
    onSuccess: () => {
      showDeactivateModal.value = false
      router.reload({ only: ['products'] })
    }
  })
}

function activateProduct(id) {
  if (confirm('Apakah Anda yakin ingin mengaktifkan kembali produk ini?')) {
    router.post(route('admin.products.activate', id), {}, {
      onSuccess: () => {
        router.reload({ only: ['products'] })
      }
    })
  }
}
</script>

<template>
  <Head title="Kelola Produk - Admin" />

  <AdminLayout>
    <div class="space-y-6">
      
      <!-- Header -->
      <div>
        <h2 class="text-2xl font-extrabold text-[#0A3551] tracking-tight">
          Kelola Produk Katalog
        </h2>
        <p class="text-sm text-gray-500 mt-1">
          Pantau produk yang dipasarkan oleh seluruh toko. Gunakan filter peninjauan untuk mendeteksi barang mencurigakan.
        </p>
      </div>

      <!-- Search & Filters Panel -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <form @submit="handleSearch" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <!-- Search Input -->
          <div class="relative flex-1 max-w-md">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.637 10.637Z" />
              </svg>
            </span>
            <input 
              v-model="searchVal"
              type="text" 
              placeholder="Cari nama atau deskripsi produk..."
              class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
            />
          </div>

          <!-- Suspicious Toggle & Actions -->
          <div class="flex items-center gap-4 flex-wrap">
            <label class="inline-flex items-center gap-2 cursor-pointer bg-rose-50 hover:bg-rose-100/75 border border-rose-100 rounded-xl px-4 py-2.5 transition">
              <input 
                v-model="suspiciousOnly"
                type="checkbox"
                class="rounded border-rose-300 text-rose-600 focus:ring-rose-500 h-4 w-4 cursor-pointer"
              />
              <span class="text-xs font-bold text-rose-700 flex items-center gap-1.5 select-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-rose-600 animate-pulse">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
                Hanya Produk Mencurigakan
              </span>
            </label>

            <button 
              type="submit" 
              class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition shadow-sm hover:shadow cursor-pointer"
            >
              Cari
            </button>
          </div>
        </form>
      </div>

      <!-- Products Table Card -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50/75">
              <tr class="text-left text-gray-500 font-bold text-xs uppercase tracking-wider">
                <th class="px-6 py-4 w-16 text-center">ID</th>
                <th class="px-6 py-4 w-48">Nama Produk</th>
                <th class="px-6 py-4">Deskripsi</th>
                <th class="px-6 py-4">Kategori</th>
                <th class="px-6 py-4">Toko Penjual</th>
                <th class="px-6 py-4 w-32">Harga</th>
                <th class="px-6 py-4 w-20 text-center">Stok</th>
                <th class="px-6 py-4 w-40 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
              <tr 
                v-for="product in products" 
                :key="product.id"
                class="hover:bg-gray-50/50 transition"
                :class="product.is_suspicious ? 'bg-rose-50/20' : ''"
              >
                <!-- ID -->
                <td class="px-6 py-4 whitespace-nowrap text-center text-gray-400 font-mono text-xs">
                  #{{ product.id }}
                </td>
                
                <!-- Nama & Matched Keywords -->
                <td class="px-6 py-4">
                  <div class="font-bold text-gray-800">{{ product.nama }}</div>
                  
                  <!-- Warning Badge for Suspicious -->
                  <div v-if="product.is_suspicious" class="flex flex-wrap gap-1 mt-1.5">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                      ⚠ Terdeteksi: {{ product.matched_keywords.join(', ') }}
                    </span>
                  </div>
                </td>

                <!-- Deskripsi -->
                <td class="px-6 py-4 max-w-xs">
                  <div class="text-xs text-gray-500 line-clamp-2" :title="product.deskripsi">
                    {{ product.deskripsi || '-' }}
                  </div>
                </td>

                <!-- Kategori -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                    {{ product.category?.nama_kategori || '-' }}
                  </span>
                  <div v-if="product.sub_category?.nama_subkategori" class="text-[10px] text-gray-400 mt-1 pl-1">
                    ↳ {{ product.sub_category.nama_subkategori }}
                  </div>
                </td>

                <!-- Toko -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="font-medium text-gray-700">{{ product.user?.nama_toko || '-' }}</div>
                  <div class="text-xs text-gray-400 mt-0.5">{{ product.user?.name || '-' }}</div>
                </td>

                <!-- Harga -->
                <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900">
                  Rp{{ Number(product.harga).toLocaleString('id-ID') }}
                </td>

                <!-- Stok -->
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span 
                    class="px-2.5 py-0.5 rounded text-xs font-bold border"
                    :class="product.stok > 0 ? 'bg-gray-50 text-gray-700' : 'bg-red-50 text-red-700 border-red-100'"
                  >
                    {{ product.stok }}
                  </span>
                </td>

                <!-- Aksi -->
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex justify-center gap-2">
                    <button
                      v-if="product.is_active"
                      @click="openDeactivateModal(product.id)"
                      class="inline-flex items-center gap-1 bg-amber-500 hover:bg-amber-600 text-white font-bold px-3 py-1.5 rounded-lg text-xs transition cursor-pointer"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                      </svg>
                      Non-aktifkan
                    </button>
                    <button
                      v-else
                      @click="activateProduct(product.id)"
                      class="inline-flex items-center gap-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3 py-1.5 rounded-lg text-xs transition cursor-pointer"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                      </svg>
                      Aktifkan
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="products.length === 0">
                <td colspan="8" class="py-16 text-center text-gray-400">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto text-gray-300 mb-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.637 10.637Z" />
                  </svg>
                  Tidak ada produk yang cocok dengan pencarian atau filter Anda.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- 🔍 MODAL NONAKTIFKAN PRODUK (DENGAN ALASAN) -->
    <transition name="fade">
      <div v-if="showDeactivateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-gray-100 transition-all duration-300 space-y-4">
          <div class="flex items-center gap-3 text-amber-500 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            <h3 class="text-lg font-bold text-gray-800">Nonaktifkan Produk</h3>
          </div>
          <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">Alasan Menonaktifkan Produk <span class="text-red-500">*</span></label>
            <textarea 
              v-model="deactivateReason" 
              required
              placeholder="Tulis alasan penonaktifan produk ini agar penjual dapat mengetahuinya..." 
              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm h-28 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition"
            ></textarea>
          </div>
          <div class="flex justify-end gap-3 pt-2">
            <button @click="showDeactivateModal = false" class="rounded-xl bg-gray-100 hover:bg-gray-200 px-5 py-2.5 text-sm font-bold text-gray-700 transition cursor-pointer">
              Batal
            </button>
            <button @click="confirmDeactivate" class="rounded-xl bg-amber-500 hover:bg-amber-600 px-5 py-2.5 text-sm font-bold text-white transition cursor-pointer">
              Nonaktifkan
            </button>
          </div>
        </div>
      </div>
    </transition>

  </AdminLayout>
</template>
