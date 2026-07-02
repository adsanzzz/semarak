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

const activeCarouselIndices = ref({})
const getProductImageIndex = (product) => {
  return activeCarouselIndices.value[product.id] || 0
}
const prevCarouselImg = (product) => {
  const current = activeCarouselIndices.value[product.id] || 0
  const len = product.images_url.length
  activeCarouselIndices.value[product.id] = (current - 1 + len) % len
}
const nextCarouselImg = (product) => {
  const current = activeCarouselIndices.value[product.id] || 0
  const len = product.images_url.length
  activeCarouselIndices.value[product.id] = (current + 1) % len
}

const showDetailModal = ref(false)
const selectedProduct = ref(null)
const activeDetailImageIndex = ref(0)
const openDetailModal = (product) => {
  selectedProduct.value = product
  activeDetailImageIndex.value = 0
  showDetailModal.value = true
}
const prevDetailImg = () => {
  if (selectedProduct.value && selectedProduct.value.images_url) {
    const len = selectedProduct.value.images_url.length
    activeDetailImageIndex.value = (activeDetailImageIndex.value - 1 + len) % len
  }
}
const nextDetailImg = () => {
  if (selectedProduct.value && selectedProduct.value.images_url) {
    const len = selectedProduct.value.images_url.length
    activeDetailImageIndex.value = (activeDetailImageIndex.value + 1) % len
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
                <th class="px-6 py-4 w-20 text-center">Gambar</th>
                <th class="px-6 py-4 w-48">Nama Produk</th>
                <th class="px-6 py-4">Deskripsi</th>
                <th class="px-6 py-4">Kategori</th>
                <th class="px-6 py-4">Variasi</th>
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

                <!-- Gambar -->
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div v-if="product.images_url && product.images_url.length > 1" class="relative w-12 h-12 mx-auto group">
                    <div class="w-full h-full rounded-lg overflow-hidden border border-gray-200 shadow-xs relative">
                      <img 
                        :src="product.images_url[getProductImageIndex(product)]" 
                        class="w-full h-full object-cover transition-all duration-300"
                        alt="Gambar Produk"
                      />
                    </div>
                    
                    <button 
                      type="button" 
                      @click.stop="prevCarouselImg(product)"
                      class="absolute left-0 top-1/2 -translate-y-1/2 bg-black/50 text-white rounded-r p-1 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-black/75 cursor-pointer z-10 text-[10px] select-none font-bold"
                    >
                      &lsaquo;
                    </button>
                    <button 
                      type="button" 
                      @click.stop="nextCarouselImg(product)"
                      class="absolute right-0 top-1/2 -translate-y-1/2 bg-black/50 text-white rounded-l p-1 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-black/75 cursor-pointer z-10 text-[10px] select-none font-bold"
                    >
                      &rsaquo;
                    </button>
                    
                    <div class="absolute -bottom-1.5 left-0 right-0 flex justify-center gap-0.5 z-10">
                      <span 
                        v-for="(img, imgIdx) in product.images_url" 
                        :key="imgIdx"
                        class="w-1 h-1 rounded-full transition-all"
                        :class="getProductImageIndex(product) === imgIdx ? 'bg-indigo-600 w-1.5' : 'bg-gray-300'"
                      ></span>
                    </div>
                  </div>
                  <div v-else class="relative w-12 h-12 mx-auto">
                    <img 
                      :src="product.image_url || 'https://via.placeholder.com/150'" 
                      class="w-full h-full object-cover rounded-lg border border-gray-200 shadow-xs mx-auto transition"
                      alt="Gambar"
                    />
                  </div>
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

                <!-- Variasi -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div v-if="product.variations && product.variations.length > 0" class="space-y-2 text-xs">
                    <div v-for="(v, idx) in product.variations" :key="idx" class="text-left">
                      <span class="font-bold text-gray-400 uppercase tracking-wider block mb-0.5 text-[10px]">{{ v.name }}</span>
                      <ul class="list-disc list-inside text-gray-500 pl-1 text-[11px] space-y-0.5">
                        <li v-for="opt in v.options" :key="opt" class="list-none flex items-center gap-1 font-semibold text-gray-600">
                          <span class="text-gray-400 text-[10px] font-bold">&bull;</span> {{ opt }}
                        </li>
                      </ul>
                    </div>
                  </div>
                  <span v-else class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-400 border border-gray-200">
                    Tidak ada variasi
                  </span>
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
                      @click="openDetailModal(product)"
                      class="inline-flex items-center gap-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold px-3 py-1.5 rounded-lg text-xs transition cursor-pointer border border-indigo-100"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                      </svg>
                      Detail
                    </button>
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
                <td colspan="10" class="py-16 text-center text-gray-400">
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

  <!-- 🔍 MODAL DETAIL PRODUK -->
  <transition name="fade">
    <div v-if="showDetailModal && selectedProduct" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-xl border border-gray-100 transition-all duration-300 flex flex-col md:flex-row gap-6 max-h-[90vh] overflow-y-auto relative">
        <!-- Close Button (top-right) -->
        <button @click="showDetailModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition cursor-pointer">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
          </svg>
        </button>

        <!-- Left: Image Slider -->
        <div class="w-full md:w-1/2 space-y-3">
          <div class="relative w-full h-64 rounded-xl overflow-hidden border border-gray-200 bg-gray-50 flex items-center justify-center group shadow-xs">
            <img 
              :src="selectedProduct.images_url[activeDetailImageIndex] || 'https://via.placeholder.com/300'" 
              class="w-full h-full object-cover transition-all"
            />
            <button 
              v-if="selectedProduct.images_url.length > 1" 
              type="button" 
              @click="prevDetailImg"
              class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 hover:bg-white text-gray-800 flex items-center justify-center shadow select-none cursor-pointer font-bold"
            >
              &lsaquo;
            </button>
            <button 
              v-if="selectedProduct.images_url.length > 1" 
              type="button" 
              @click="nextDetailImg"
              class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 hover:bg-white text-gray-800 flex items-center justify-center shadow select-none cursor-pointer font-bold"
            >
              &rsaquo;
            </button>
          </div>
          <!-- Thumbnails -->
          <div v-if="selectedProduct.images_url.length > 1" class="flex gap-2 overflow-x-auto pb-1">
            <button 
              v-for="(img, idx) in selectedProduct.images_url" 
              :key="idx"
              @click="activeDetailImageIndex = idx"
              class="w-14 h-14 rounded-lg overflow-hidden border-2 flex-shrink-0"
              :class="activeDetailImageIndex === idx ? 'border-indigo-600' : 'border-gray-200'"
            >
              <img :src="img" class="w-full h-full object-cover" />
            </button>
          </div>
        </div>
        <!-- Right: Details Info -->
        <div class="flex-1 space-y-4 text-left">
          <div>
            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
              {{ selectedProduct.category?.nama_kategori || '-' }}
            </span>
            <h3 class="text-xl font-bold text-gray-800 mt-1">{{ selectedProduct.nama }}</h3>
            <p class="text-2xl font-extrabold text-blue-600 mt-1">Rp{{ Number(selectedProduct.harga).toLocaleString('id-ID') }}</p>
          </div>
          
          <div class="grid grid-cols-2 gap-3 text-xs bg-gray-50 p-3 rounded-xl border border-gray-100">
            <div>
              <span class="text-gray-400 block uppercase font-bold text-[10px]">Stok</span>
              <span class="text-gray-700 font-bold">{{ selectedProduct.stok }} {{ selectedProduct.satuan || '' }}</span>
            </div>
            <div>
              <span class="text-gray-400 block uppercase font-bold text-[10px]">Toko Penjual</span>
              <span class="text-gray-700 font-bold block leading-tight">{{ selectedProduct.user?.nama_toko || '-' }}</span>
              <span class="text-gray-400 text-[10px] block mt-0.5">({{ selectedProduct.user?.name || '-' }})</span>
            </div>
          </div>

          <!-- Variasi -->
          <div>
            <span class="text-gray-400 block uppercase font-bold text-[10px] mb-1">Variasi Produk</span>
            <div v-if="selectedProduct.variations && selectedProduct.variations.length > 0" class="flex flex-wrap gap-2">
              <span v-for="(v, vIdx) in selectedProduct.variations" :key="vIdx" class="inline-flex items-center gap-1.5 bg-gray-50 border border-gray-200 text-gray-600 px-3 py-1 rounded-xl text-xs font-semibold">
                <span class="font-bold text-gray-400 uppercase text-[10px]">{{ v.name }}:</span>
                <span class="text-gray-700 font-bold">{{ v.options.join(', ') }}</span>
              </span>
            </div>
            <span v-else class="text-xs text-gray-400 bg-gray-50 border border-gray-100 px-3 py-1 rounded-xl inline-block font-semibold">
              Tidak ada variasi
            </span>
          </div>

          <!-- Deskripsi -->
          <div>
            <span class="text-gray-400 block uppercase font-bold text-[10px] mb-1">Deskripsi</span>
            <p class="text-xs text-gray-600 leading-relaxed whitespace-pre-line max-h-32 overflow-y-auto bg-gray-50/50 p-2.5 rounded-lg border border-gray-100">
              {{ selectedProduct.deskripsi || 'Tidak ada deskripsi' }}
            </p>
          </div>

          <div class="flex justify-end pt-2">
            <button @click="showDetailModal = false" class="w-full md:w-auto rounded-xl bg-indigo-600 hover:bg-indigo-700 px-6 py-2.5 text-sm font-bold text-white shadow transition cursor-pointer">
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>
