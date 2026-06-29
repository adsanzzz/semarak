<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  reportedProducts: Array
})

const selectedProduct = ref(null)
const selectedReport = ref(null)
const modalOpen = ref(false)

function openDetailModal(product) {
  selectedProduct.value = product
  selectedReport.value = null
  modalOpen.value = true
}

function closeDetailModal() {
  selectedProduct.value = null
  selectedReport.value = null
  modalOpen.value = false
}

function deactivateProduct(id) {
  if (confirm('Apakah Anda yakin ingin menonaktifkan produk ini? Produk tidak akan lagi tampil di katalog pembeli.')) {
    router.post(route('admin.products.deactivate', id), {}, {
      onSuccess: () => {
        closeDetailModal()
        router.reload({ only: ['reportedProducts'] })
      }
    })
  }
}
</script>

<template>
  <Head title="Laporan Produk - Admin" />

  <AdminLayout>
    <div class="space-y-6">
      
      <!-- Header -->
      <div>
        <h2 class="text-2xl font-extrabold text-[#0A3551] tracking-tight">
          Daftar Laporan Pelanggaran Produk
        </h2>
        <p class="text-sm text-gray-500 mt-1">
          Pantau produk yang dilaporkan oleh pembeli karena melanggar ketentuan layanan.
        </p>
      </div>

      <!-- Empty State -->
      <div v-if="reportedProducts.length === 0" class="py-16 text-center text-gray-400 text-sm bg-white rounded-2xl border border-gray-100 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto text-gray-300 mb-3">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        Tidak ada laporan pelanggaran produk untuk saat ini. Katalog dalam keadaan bersih!
      </div>

      <!-- Reported Products Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="p in reportedProducts" 
          :key="p.id"
          class="bg-white border border-gray-100 rounded-2xl p-5 shadow-xs hover:shadow-md transition-all duration-300 flex flex-col justify-between relative group cursor-pointer"
          @click="openDetailModal(p)"
        >
          <!-- Badge Count of Reports -->
          <div class="absolute top-4 right-4 z-10 bg-rose-50 text-rose-700 border border-rose-100 font-bold px-2.5 py-1 rounded-xl text-[10px] flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5 text-rose-600">
              <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-9-3a1 1 0 1 1 2 0v4a1 1 0 1 1-2 0V7Zm1 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
            </svg>
            {{ p.reporters_count }} Laporan
          </div>

          <div class="space-y-4">
            <!-- Product Thumbnail & Basic info -->
            <div class="flex items-center gap-4">
              <img 
                :src="p.image || 'https://via.placeholder.com/150'" 
                class="w-16 h-16 object-cover rounded-xl border border-gray-100 flex-shrink-0"
              />
              <div class="min-w-0 flex-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                  {{ p.category }}
                </span>
                <h4 class="font-bold text-gray-800 text-sm truncate mt-1" :title="p.nama">
                  {{ p.nama }}
                </h4>
                <p class="text-xs text-gray-400 mt-0.5">Toko: <span class="font-semibold text-gray-600">{{ p.toko }}</span></p>
              </div>
            </div>

            <!-- Stats/Status summary -->
            <div class="flex items-center justify-between text-xs border-t border-gray-50 pt-3">
              <div>
                <span class="text-gray-400">Harga:</span>
                <span class="font-bold text-gray-800 ml-1">Rp{{ Number(p.harga).toLocaleString('id-ID') }}</span>
              </div>

              <div>
                <span 
                  class="px-2 py-0.5 rounded-full text-[10px] font-bold border"
                  :class="p.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-gray-50 text-gray-500 border-gray-200'"
                >
                  {{ p.is_active ? 'Aktif' : 'Non-aktif' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-between border-t border-gray-50 pt-4 mt-4" @click.stop>
            <button 
              @click="openDetailModal(p)"
              class="text-indigo-600 hover:text-indigo-800 text-xs font-bold inline-flex items-center gap-1 cursor-pointer"
            >
              Lihat Detail
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
              </svg>
            </button>

            <button
              v-if="p.is_active"
              @click="deactivateProduct(p.id)"
              class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-3 py-1.5 rounded-xl text-[10px] flex items-center gap-1 transition cursor-pointer"
            >
              Nonaktifkan
            </button>
          </div>
        </div>
      </div>

      <!-- ℹ️ DETAIL REPORTS MODAL -->
      <transition name="fade">
        <div v-if="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
          <div class="w-full max-w-2xl bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 flex flex-col max-h-[90vh]">
            
            <!-- Header -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
              <div>
                <h3 class="text-base font-bold text-gray-800">
                  {{ selectedReport ? 'Detail Laporan Aduan' : 'Daftar Laporan Masuk' }}
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">
                  {{ selectedReport ? `Oleh: ${selectedReport.reporter_name}` : `Total ${selectedProduct.reporters_count} laporan untuk produk ini` }}
                </p>
              </div>
              
              <button @click="closeDetailModal" class="text-gray-400 hover:text-gray-600 transition cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Body (Scrollable) -->
            <div class="p-6 overflow-y-auto space-y-6 flex-1">
              
              <!-- Product info panel -->
              <div class="flex items-center gap-4 bg-gray-50/50 border border-gray-100 rounded-2xl p-4">
                <img 
                  :src="selectedProduct.image || 'https://via.placeholder.com/150'" 
                  class="w-14 h-14 object-cover rounded-xl border border-gray-100 flex-shrink-0"
                />
                <div class="min-w-0 flex-1 space-y-0.5">
                  <div class="flex items-center gap-2 flex-wrap">
                    <h4 class="font-bold text-gray-800 text-sm truncate">{{ selectedProduct.nama }}</h4>
                    <span 
                      class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-bold border"
                      :class="selectedProduct.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-gray-50 text-gray-500 border-gray-200'"
                    >
                      {{ selectedProduct.is_active ? 'Aktif' : 'Non-aktif' }}
                    </span>
                  </div>
                  
                  <div class="text-xs text-gray-500 flex gap-4">
                    <span>Toko: <span class="font-bold text-gray-600">{{ selectedProduct.toko }}</span></span>
                    <span>Harga: <span class="font-bold text-gray-600">Rp{{ Number(selectedProduct.harga).toLocaleString('id-ID') }}</span></span>
                  </div>
                </div>
              </div>

              <!-- VIEW 1: DAFTAR LAPORAN (selectedReport === null) -->
              <div v-if="!selectedReport" class="space-y-3">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pilih Laporan untuk Melihat Detail</h4>
                
                <div class="divide-y divide-gray-100 border border-gray-100 rounded-2xl overflow-hidden bg-white">
                  <button 
                    v-for="(report, idx) in selectedProduct.reports" 
                    :key="report.id" 
                    @click="selectedReport = report"
                    class="w-full flex items-center justify-between gap-4 p-4 text-left hover:bg-gray-50/50 transition cursor-pointer"
                  >
                    <div class="space-y-1 min-w-0 flex-1">
                      <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-gray-700">{{ report.reporter_name }}</span>
                        <span class="text-[10px] text-gray-400 font-mono">{{ report.created_at }}</span>
                      </div>
                      <p class="text-xs text-[#0A3551] font-bold truncate">Alasan: {{ report.reason }}</p>
                    </div>

                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-gray-400">
                      <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                    </svg>
                  </button>
                </div>
              </div>

              <!-- VIEW 2: DETAIL LAPORAN TERTENTU (selectedReport !== null) -->
              <div v-else class="space-y-4">
                <!-- Back Button -->
                <button 
                  @click="selectedReport = null"
                  class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:text-indigo-800 transition cursor-pointer"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                    <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
                  </svg>
                  Kembali ke Daftar Laporan
                </button>

                <!-- Detailed Report Card -->
                <div class="border border-gray-100 rounded-2xl p-5 space-y-4 bg-white shadow-xs">
                  <div class="flex items-center justify-between text-xs pb-3 border-b border-gray-50">
                    <div>
                      <span class="text-gray-400">Pelapor:</span>
                      <span class="font-bold text-gray-800 ml-1">{{ selectedReport.reporter_name }}</span>
                    </div>
                    <div class="text-gray-400 font-mono text-[10px]">
                      {{ selectedReport.created_at }}
                    </div>
                  </div>

                  <!-- Alasan (Subject) -->
                  <div>
                    <span class="text-gray-400 block text-[10px] uppercase font-bold tracking-wider mb-1">Kategori Pelanggaran</span>
                    <span class="bg-rose-50 text-rose-700 border border-rose-100 px-3 py-1.5 rounded-xl text-xs font-bold inline-block">
                      {{ selectedReport.reason }}
                    </span>
                  </div>

                  <!-- Uraian Keluhan -->
                  <div class="space-y-1.5 text-xs leading-relaxed text-gray-600">
                    <div class="font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Penjelasan Detail / Deskripsi</div>
                    <p class="whitespace-pre-line bg-gray-50 rounded-xl p-4 border border-gray-100 text-gray-700">
                      {{ selectedReport.description }}
                    </p>
                  </div>

                  <!-- Proof/Bukti file (Mandatory) -->
                  <div v-if="selectedReport.bukti_url" class="space-y-2">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Bukti Lampiran Fisik</div>
                    <a 
                      :href="selectedReport.bukti_url" 
                      target="_blank" 
                      class="inline-block relative rounded-xl border border-gray-100 overflow-hidden group/img bg-gray-50"
                    >
                      <img 
                        :src="selectedReport.bukti_url" 
                        class="h-44 object-cover rounded-xl transition group-hover/img:scale-102"
                      />
                      <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/img:opacity-100 transition flex items-center justify-center text-white text-xs font-bold">
                        Perbesar Gambar ↗
                      </div>
                    </a>
                  </div>
                </div>
              </div>

            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between flex-shrink-0">
              <button 
                v-if="selectedProduct.is_active"
                @click="deactivateProduct(selectedProduct.id)"
                class="bg-amber-600 hover:bg-amber-700 text-white font-bold px-5 py-2.5 rounded-xl text-xs transition cursor-pointer"
              >
                Nonaktifkan Produk Ini
              </button>
              <div v-else class="text-xs font-bold text-gray-400">Produk Sudah Dinonaktifkan</div>

              <button 
                @click="closeDetailModal" 
                class="border border-gray-300 text-gray-600 font-bold px-5 py-2.5 rounded-xl text-xs transition hover:bg-gray-100 cursor-pointer shadow-xs"
              >
                Tutup
              </button>
            </div>

          </div>
        </div>
      </transition>

    </div>
  </AdminLayout>
</template>
