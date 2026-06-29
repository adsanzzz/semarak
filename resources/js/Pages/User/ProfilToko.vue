<script setup>
import NavbarAuth from '@/Components/NavbarAuth.vue'
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  toko: Object,
  produkList: Array
})

const isAuthenticated = computed(() => !!usePage().props.auth?.user)

// Notifikasi
const notif = ref(null)

function showNotif(message, type = 'success') {
  notif.value = { message, type }
  setTimeout(() => notif.value = null, 2500)
}

function getInitials(name) {
  if (!name) return '?'
  const parts = name.split(' ')
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return name.slice(0, 2).toUpperCase()
}

// ========================
// REPORT TOKO MODAL
// ========================
const reportModalOpen = ref(false)
const reportStep = ref(1) // 1: Choose reason, 2: Form description + proof
const selectedReason = ref('')

const reportReasons = [
  'Penipuan / Toko palsu',
  'Penjualan barang terlarang / berbahaya',
  'Perilaku penjual kasar / tidak sopan',
  'Penyalahgunaan data pribadi',
  'Lainnya'
]

const reportForm = useForm({
  reason: '',
  description: '',
  bukti: null,
  reported_user_id: props.toko.id,
  reported_product_id: null
})

function openReportModal() {
  if (!isAuthenticated.value) {
    router.get(route('login'))
    return
  }
  reportModalOpen.value = true
  reportStep.value = 1
  selectedReason.value = ''
  reportForm.reset()
}

function selectReason(reason) {
  selectedReason.value = reason
  reportForm.reason = reason
  reportStep.value = 2
}

function handleFileChange(e) {
  reportForm.bukti = e.target.files[0]
}

function submitReport() {
  if (!reportForm.description.trim()) {
    showNotif('Deskripsi wajib diisi', 'error')
    return
  }
  if (!reportForm.bukti) {
    showNotif('Bukti berupa gambar wajib diunggah', 'error')
    return
  }

  reportForm.post(route('report.store'), {
    onSuccess: () => {
      reportModalOpen.value = false
      showNotif('Laporan akun berhasil terkirim', 'success')
      reportForm.reset()
    },
    onError: (errs) => {
      showNotif('Gagal mengirim laporan. Pastikan berkas sesuai.', 'error')
    }
  })
}
</script>

<template>
  <Head :title="`Profil Toko ${toko.nama_toko || toko.name} - Semarak`" />

  <div>
    <NavbarAuth />

    <!-- Notifikasi -->
    <transition name="fade">
      <div
        v-if="notif"
        class="fixed top-5 right-5 px-4 py-2.5 rounded-xl shadow-lg text-white z-50 text-xs font-bold"
        :class="notif.type === 'success' ? 'bg-emerald-600' : 'bg-red-600'"
      >
        {{ notif.message }}
      </div>
    </transition>

    <div class="max-w-6xl mx-auto px-6 py-10 space-y-10">
      
      <!-- 🏪 STORE HEADER INFO CARD -->
      <div class="bg-white rounded-3xl border border-gray-100 shadow-xs p-6 md:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative overflow-hidden">
        <div class="flex items-center gap-5">
          <!-- Store Avatar -->
          <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-700 flex items-center justify-center font-extrabold text-2xl flex-shrink-0">
            {{ getInitials(toko.nama_toko || toko.name) }}
          </div>

          <!-- Store Text Details -->
          <div class="space-y-1.5 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
              <h2 class="text-xl md:text-2xl font-black text-gray-800 truncate">
                {{ toko.nama_toko || toko.name }}
              </h2>
              <span 
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border"
                :class="toko.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-700 border-red-100'"
              >
                {{ toko.is_active ? 'Toko Aktif' : 'Toko Nonaktif' }}
              </span>
            </div>
            
            <p class="text-xs text-gray-500 font-medium">Pemilik: {{ toko.name }}</p>
            <p class="text-xs text-gray-400 flex items-center gap-1">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-gray-400">
                <path fill-rule="evenodd" d="M9.69 18.933a.75.75 0 0 1-1.38 0L3.81 10.38a6.75 6.75 0 1 1 12.38 0l-4.5 8.553ZM10 12a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z" clip-rule="evenodd" />
              </svg>
              {{ toko.alamat_penjual || '-' }}, {{ toko.kecamatan || '-' }}, {{ toko.kabupaten || '-' }}
            </p>
          </div>
        </div>

        <!-- Store Actions -->
        <div class="flex flex-row md:flex-col gap-3 w-full md:w-auto items-stretch md:items-end">
          <button 
            @click="openReportModal"
            class="flex-1 md:flex-none inline-flex items-center justify-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-700 px-4 py-2.5 rounded-xl text-xs font-bold transition border border-red-100 cursor-pointer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            Laporkan Akun Toko
          </button>
          
          <a 
            v-if="toko.phone"
            :href="`https://wa.me/${toko.phone.replace(/[^0-9]/g, '')}`" 
            target="_blank"
            class="flex-1 md:flex-none inline-flex items-center justify-center gap-1.5 bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition shadow-sm cursor-pointer"
          >
            💬 Hubungi via WA
          </a>
        </div>
      </div>

      <!-- ℹ️ ADDITIONAL STORE DETAILS ACCORDION/GRID -->
      <div class="bg-white rounded-3xl border border-gray-100 shadow-xs p-6 space-y-6">
        <div>
          <h3 class="text-base font-black text-gray-800">Informasi Lengkap Toko</h3>
          <p class="text-xs text-gray-400 mt-0.5">Informasi profil usaha dan sosial media dari toko ini.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-xs leading-relaxed">
          <!-- Usaha Info -->
          <div class="space-y-3 bg-gray-50/50 rounded-2xl p-4 border border-gray-50">
            <h4 class="font-bold text-gray-500 uppercase tracking-wider">Detail Usaha</h4>
            <div class="space-y-1.5 text-gray-700">
              <div><span class="text-gray-400">Kategori Usaha:</span> {{ toko.kategori_usaha || '-' }}</div>
              <div><span class="text-gray-400">Toko Terdaftar:</span> {{ new Date(toko.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) }}</div>
              <div><span class="text-gray-400">Email:</span> {{ toko.email }}</div>
            </div>
          </div>

          <!-- Sertifikasi Info -->
          <div class="space-y-3 bg-gray-50/50 rounded-2xl p-4 border border-gray-50">
            <h4 class="font-bold text-gray-500 uppercase tracking-wider">Sertifikasi & Kemitraan</h4>
            <div class="space-y-1.5 text-gray-700">
              <div><span class="text-gray-400">Jenis Sertifikasi:</span> {{ toko.sertifikasi_jenis === 'halal' ? 'Halal' : toko.sertifikasi_jenis === 'haki' ? 'HAKI' : '-' }}</div>
              <div>
                <span class="text-gray-400">Status Sertifikasi:</span> 
                <span class="ml-1 capitalize text-[10px] font-bold px-2 py-0.5 bg-gray-100 rounded-full border border-gray-200">
                  {{ toko.sertifikasi_status || 'Belum Mengajukan' }}
                </span>
              </div>
              <div><span class="text-gray-400">Kemitraan Usaha:</span> {{ toko.informasi_kemitraan || '-' }}</div>
            </div>
          </div>

          <!-- Sosmed Info -->
          <div class="space-y-3 bg-gray-50/50 rounded-2xl p-4 border border-gray-50">
            <h4 class="font-bold text-gray-500 uppercase tracking-wider">Media Sosial</h4>
            <div class="space-y-1.5 text-gray-700">
              <div><span class="text-gray-400">Instagram:</span> {{ toko.sosmed_instagram ? `@${toko.sosmed_instagram}` : '-' }}</div>
              <div><span class="text-gray-400">TikTok:</span> {{ toko.sosmed_tiktok ? `@${toko.sosmed_tiktok}` : '-' }}</div>
              <div><span class="text-gray-400">Marketplace Lain:</span> {{ toko.tautan_marketplace || '-' }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- 🛍️ STORE PRODUCTS SECTION -->
      <div class="space-y-6">
        <div>
          <h3 class="text-lg font-black text-gray-800">Katalog Produk Toko</h3>
          <p class="text-xs text-gray-400 mt-0.5">Menampilkan seluruh produk aktif dari toko ini.</p>
        </div>

        <div v-if="produkList.length === 0" class="py-16 text-center text-gray-400 text-sm bg-white rounded-3xl border border-gray-100">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto text-gray-300 mb-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
          </svg>
          Toko ini belum memasarkan produk apa pun saat ini.
        </div>

        <!-- Products Catalog Grid -->
        <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <Link 
            v-for="p in produkList" 
            :key="p.id" 
            :href="route('produk.show', p.id)"
            class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-xs hover:shadow-md hover:-translate-y-1 transition duration-300 group flex flex-col justify-between"
          >
            <!-- Product Thumbnail -->
            <div class="h-44 bg-gray-100 overflow-hidden relative">
              <img 
                :src="p.image || 'https://via.placeholder.com/300'" 
                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
              />
              <span class="absolute top-2.5 left-2.5 px-2 py-0.5 bg-indigo-50/90 text-indigo-700 font-bold text-[9px] rounded-md backdrop-blur-xs">
                {{ p.kategori }}
              </span>
            </div>

            <!-- Product Info -->
            <div class="p-4 space-y-2 flex-1 flex flex-col justify-between">
              <div class="space-y-1">
                <h4 class="font-bold text-gray-800 text-xs line-clamp-2 leading-relaxed" :title="p.nama">
                  {{ p.nama }}
                </h4>

                <div class="flex items-center gap-1 text-[10px] text-gray-400">
                  <span v-if="p.rating" class="text-yellow-500 font-bold">★ {{ p.rating }}</span>
                  <span v-else>Belum diulas</span>
                  <span>•</span>
                  <span>{{ p.terjual }} Terjual</span>
                </div>
              </div>

              <div class="flex items-end justify-between pt-2">
                <div class="font-extrabold text-sm text-indigo-600">
                  Rp{{ Number(p.harga).toLocaleString('id-ID') }}
                </div>
                
                <span class="text-[9px] font-bold text-gray-400">Stok: {{ p.stok }}</span>
              </div>
            </div>
          </Link>
        </div>
      </div>

    </div>

    <!-- 🚨 MODAL LAPORKAN AKUN TOKO -->
    <transition name="fade">
      <div v-if="reportModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 transition-all duration-300">
          
          <!-- Header -->
          <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-gray-800">Laporkan Akun Toko</h3>
            <button @click="reportModalOpen = false" class="text-gray-400 hover:text-gray-600 transition cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- STEP 1: Pilih Alasan -->
          <div v-if="reportStep === 1" class="p-6 space-y-4">
            <p class="text-xs text-gray-400">Pilih alasan utama Anda melaporkan akun toko ini:</p>
            <div class="space-y-2.5">
              <button 
                v-for="reason in reportReasons" 
                :key="reason"
                @click="selectReason(reason)"
                class="w-full text-left px-4 py-3 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-indigo-50/50 hover:border-indigo-200 transition cursor-pointer"
              >
                {{ reason }}
              </button>
            </div>
          </div>

          <!-- STEP 2: Isi Deskripsi & Bukti -->
          <form v-else @submit.prevent="submitReport" class="p-6 space-y-4">
            <!-- Info Profil Akun Terlapor -->
            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 text-xs space-y-1">
              <div class="text-gray-400">Toko Terlapor:</div>
              <div class="font-bold text-gray-800">{{ toko.nama_toko || toko.name }}</div>
              <div class="text-gray-500">Email: {{ toko.email }}</div>
              <div class="text-indigo-600 font-semibold mt-1">Alasan: {{ selectedReason }}</div>
            </div>

            <!-- Deskripsi (Wajib) -->
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-gray-600">Jelaskan detail pelanggaran toko <span class="text-red-500">*</span></label>
              <textarea 
                v-model="reportForm.description"
                rows="4"
                placeholder="Tulis alasan detail mengapa toko ini patut dilaporkan..."
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-xs text-gray-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none focus:border-indigo-500 resize-none"
                required
              ></textarea>
            </div>

            <!-- Bukti (Wajib) -->
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-gray-600">Unggah bukti foto/gambar <span class="text-red-500">*</span></label>
              <input 
                type="file"
                accept="image/*"
                @change="handleFileChange"
                class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer"
                required
              />
              <p class="text-[10px] text-gray-400">Harap upload tangkapan layar chat, pesanan, atau pelanggaran toko.</p>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-2">
              <button 
                type="button" 
                @click="reportStep = 1"
                class="border border-gray-200 text-gray-600 font-bold px-4 py-2.5 rounded-xl text-xs transition hover:bg-gray-50 cursor-pointer"
              >
                Kembali
              </button>
              <button 
                type="submit"
                :disabled="reportForm.processing"
                class="bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white font-bold px-4 py-2.5 rounded-xl text-xs flex-1 transition cursor-pointer"
              >
                Kirim Laporan
              </button>
            </div>
          </form>

        </div>
      </div>
    </transition>

  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.4s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
