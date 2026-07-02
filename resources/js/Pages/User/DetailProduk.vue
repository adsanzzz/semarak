<script setup>
import { usePage, router, Link, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import NavbarAuth from '@/Components/NavbarAuth.vue'

// Ambil data dari controller
const { produk } = usePage().props
const reviews = computed(() => usePage().props.reviews || [])
const isAuthenticated = computed(() => !!usePage().props.auth?.user)
const chatSellerUrl = computed(() => route('chat.start', { seller: produk.user_id, product_id: produk.id }))

const activeImageIndex = ref(0)
const currentImage = computed(() => {
  if (produk.images && produk.images.length > 0) {
    return produk.images[activeImageIndex.value]
  }
  return produk.image || 'https://via.placeholder.com/500x400'
})

const prevImage = () => {
  if (produk.images && produk.images.length > 0) {
    const len = produk.images.length
    activeImageIndex.value = (activeImageIndex.value - 1 + len) % len
  }
}

const nextImage = () => {
  if (produk.images && produk.images.length > 0) {
    const len = produk.images.length
    activeImageIndex.value = (activeImageIndex.value + 1) % len
  }
}

const selectedVariations = ref({})
if (produk.variations && produk.variations.length > 0) {
  produk.variations.forEach(v => {
    selectedVariations.value[v.name] = ''
  })
}

const showPurchaseModal = ref(false)
const selectedQty = ref(1)
const purchaseActionType = ref('cart')

const openPurchaseModal = (action) => {
  if (!isAuthenticated.value) {
    router.get(route('login'))
    return
  }
  purchaseActionType.value = action
  selectedQty.value = 1
  showPurchaseModal.value = true
}

const changeModalQty = (amount) => {
  const newQty = selectedQty.value + amount
  if (newQty >= 1 && newQty <= produk.stok) {
    selectedQty.value = newQty
  }
}

const validateModalQtyInput = () => {
  if (selectedQty.value < 1) {
    selectedQty.value = 1
  } else if (selectedQty.value > produk.stok) {
    selectedQty.value = produk.stok
    showNotif('Jumlah melebihi stok yang tersedia', 'error')
  }
}

const purchaseTotalPrice = computed(() => {
  return produk.harga * selectedQty.value
})

const submitPurchaseAction = () => {
  if (produk.variations && produk.variations.length > 0) {
    for (const v of produk.variations) {
      if (!selectedVariations.value[v.name]) {
        showNotif(`Pilih ${v.name} terlebih dahulu`, 'error')
        return
      }
    }
  }

  if (purchaseActionType.value === 'cart') {
    router.post('/keranjang', {
      product_id: produk.id,
      qty: selectedQty.value,
      variations: selectedVariations.value
    }, {
      preserveScroll: true,
      onSuccess: () => {
        showPurchaseModal.value = false
        showNotif('Produk berhasil ditambahkan ke keranjang', 'success')
      },
      onError: (err) => {
        showNotif(err.error || 'Gagal menambahkan ke keranjang', 'error')
      }
    })
  } else {
    router.post(route('checkout.prepare.product'), {
      product_id: produk.id,
      qty: selectedQty.value,
      variations: selectedVariations.value
    }, {
      onError: (err) => {
        showNotif(err.error || 'Gagal melanjutkan ke checkout', 'error')
      }
    })
  }
}

// Notifikasi
const notif = ref(null)

function showNotif(message, type = 'success') {
  notif.value = { message, type }
  setTimeout(() => notif.value = null, 2500)
}

// Tambah ke keranjang
function tambahKeranjang() {
  if (!isAuthenticated.value) {
    router.get(route('login'))
    return
  }

  router.post('/keranjang', {
    product_id: produk.id
  }, {
    preserveScroll: true,
    onSuccess: () => {
      showNotif('Produk berhasil ditambahkan ke keranjang', 'success')
    },
    onError: () => {
      showNotif('Gagal menambahkan ke keranjang', 'error')
    }
  })
}

function beliSekarang() {
  if (!isAuthenticated.value) {
    router.get(route('login'))
    return
  }

  router.post(route('checkout.prepare.product'), {
    product_id: produk.id,
    qty: 1,
  })
}

// ========================
// REPORT PRODUCT MODAL
// ========================
const reportModalOpen = ref(false)
const reportStep = ref(1) // 1: Choose reason, 2: Form description + proof
const selectedReason = ref('')

const reportReasons = [
  'Produk berbahaya / ilegal / dilarang',
  'Penipuan atau barang palsu',
  'Konten tidak senonoh / tidak pantas',
  'Pelanggaran hak kekayaan intelektual (HAKI)',
  'Lainnya'
]

const reportForm = useForm({
  reason: '',
  description: '',
  bukti: null,
  reported_product_id: produk.id,
  reported_user_id: produk.user_id
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
      showNotif('Laporan produk berhasil terkirim', 'success')
      reportForm.reset()
    },
    onError: (errs) => {
      showNotif('Gagal mengirim laporan. Pastikan berkas sesuai.', 'error')
    }
  })
}
</script>

<template>
  <div>
    <NavbarAuth />

    <div class="max-w-6xl mx-auto px-6 py-10">
      
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

      <!-- Breadcrumb -->
      <nav class="text-sm text-gray-500 mb-6">
        <span>Beranda</span>
        <span class="mx-2">›</span>
        <span>Produk</span>
        <span class="mx-2">›</span>
        <span class="text-gray-700 font-medium">{{ produk.nama }}</span>
      </nav>

      <!-- Content -->
      <div class="grid md:grid-cols-2 gap-10">
        
        <!-- Gambar Produk -->
        <div>
          <!-- Large Main Image Wrapper -->
          <div class="relative w-full h-[400px] rounded-2xl overflow-hidden border border-gray-200 shadow-sm group bg-gray-50">
            <transition name="fade" mode="out-in">
              <img
                :key="activeImageIndex"
                :src="currentImage"
                class="w-full h-full object-cover transition duration-300"
              />
            </transition>
            
            <!-- Next and Previous buttons -->
            <div v-if="produk.images && produk.images.length > 1" class="absolute inset-0 flex items-center justify-between px-4 opacity-0 group-hover:opacity-100 transition-opacity">
              <button 
                type="button" 
                @click="prevImage"
                class="w-10 h-10 rounded-full bg-white/90 hover:bg-white text-gray-800 flex items-center justify-center shadow hover:shadow-md transition cursor-pointer font-bold text-lg select-none"
              >
                &lsaquo;
              </button>
              <button 
                type="button" 
                @click="nextImage"
                class="w-10 h-10 rounded-full bg-white/90 hover:bg-white text-gray-800 flex items-center justify-center shadow hover:shadow-md transition cursor-pointer font-bold text-lg select-none"
              >
                &rsaquo;
              </button>
            </div>
          </div>
          
          <!-- Thumbnail row -->
          <div v-if="produk.images && produk.images.length > 1" class="flex gap-3 mt-3 overflow-x-auto pb-1">
            <button
              v-for="(img, idx) in produk.images"
              :key="idx"
              @click="activeImageIndex = idx"
              class="w-20 h-20 rounded-xl overflow-hidden border-2 flex-shrink-0 transition-all duration-200"
              :class="activeImageIndex === idx ? 'border-indigo-600 ring-2 ring-indigo-100' : 'border-gray-200 hover:border-gray-300'"
            >
              <img :src="img" class="w-full h-full object-cover" />
            </button>
          </div>
        </div>

        <!-- Detail Produk -->
        <div class="space-y-4">
          
          <!-- Nama & Laporan -->
          <div class="flex items-start justify-between gap-4">
            <h1 class="text-2xl font-bold text-gray-800">
              {{ produk.nama }}
            </h1>

            <button 
              @click="openReportModal"
              class="flex-shrink-0 inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-700 px-3 py-1.5 rounded-xl text-xs font-bold transition border border-red-100 cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-6.005-10.483l-1.22 3.107a9 9 0 0 1-6.208-.682l-.108-.054a9 9 0 0 0-6.086-.71L3 5.25m0 9.75a9 9 0 0 1 2.77-.693V5.25" />
              </svg>
              Laporkan Produk
            </button>
          </div>

          <!-- Toko -->
          <p class="text-sm text-gray-500">
            Toko: 
            <Link 
              :href="route('toko.profile', produk.user_id)" 
              class="font-bold text-indigo-600 hover:text-indigo-800 hover:underline inline-flex items-center gap-1"
            >
              {{ produk.toko || '-' }}
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.22 5.08a.75.75 0 1 1 1.06-1.06l5.5 5.5a.75.75 0 0 1 0 1.06l-5.5 5.5a.75.75 0 1 1-1.06-1.06l4.168-4.17H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
              </svg>
            </Link>
          </p>

          <!-- Kategori -->
          <p class="text-sm text-gray-500">
            Kategori: {{ produk.kategori || '-' }}
          </p>

          <p class="text-sm text-gray-500">
            Rating:
            <span v-if="produk.rating" class="text-yellow-500 font-semibold">★ {{ produk.rating }}</span>
            <span v-else>Belum ada ulasan</span>
            <span v-if="produk.rating_count">({{ produk.rating_count }} ulasan)</span>
          </p>

          <!-- Harga -->
          <p class="text-3xl font-bold text-blue-600">
            Rp {{ Number(produk.harga).toLocaleString() }}
          </p>

          <!-- Info tambahan -->
          <div class="flex gap-6 text-sm text-gray-600">
            <p>Stok: {{ produk.stok }}</p>
            <p v-if="produk.warna">Warna: {{ produk.warna }}</p>
            <p v-if="produk.ukuran">Ukuran: {{ produk.ukuran }}</p>
          </div>



          <!-- Deskripsi -->
          <div>
            <h3 class="font-semibold mb-1 text-gray-800">Deskripsi</h3>
            <p class="text-gray-600 text-sm leading-relaxed whitespace-pre-line">
              {{ produk.deskripsi || 'Tidak ada deskripsi' }}
            </p>
          </div>


          <!-- Tombol -->
          <div class="flex gap-4 pt-4">
            <button
              @click="openPurchaseModal('cart')"
              class="bg-yellow-400 hover:bg-yellow-500 text-white px-4 py-2 rounded-lg font-medium flex-1 cursor-pointer"
            >
              + Keranjang
            </button>

            <button
              @click="openPurchaseModal('buy')"
              class="border border-gray-300 px-4 py-2 rounded-lg flex-1 hover:bg-gray-100 cursor-pointer"
            >
              Beli Sekarang
            </button>

            <!-- TOMBOL CHAT -->
            <a
              v-if="isAuthenticated"
              :href="chatSellerUrl"
              class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-center flex-1 cursor-pointer"
            >
              💬 Chat Penjual
            </a>
          </div>

        </div>

      </div>

      <!-- SECTION ULASAN PEMBELI -->
      <div class="mt-16 border-t pt-10">
        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
          <span>Ulasan Pembeli</span>
          <span class="text-sm bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-full font-semibold">
            {{ reviews.length }} Ulasan
          </span>
        </h2>

        <div v-if="reviews.length === 0" class="text-gray-500 text-sm py-6 bg-gray-50 rounded-xl text-center border">
          Belum ada ulasan untuk produk ini.
        </div>

        <div v-else class="space-y-6">
          <div v-for="review in reviews" :key="review.id" class="bg-white rounded-xl border p-5 shadow-sm space-y-3">
            <div class="flex justify-between items-start">
              <div>
                <p class="font-bold text-gray-800 text-sm">{{ review.buyer_name }}</p>
                <div class="flex items-center gap-1.5 mt-1">
                  <div class="flex text-yellow-450 text-base">
                    {{ '★'.repeat(review.rating) }}{{ '☆'.repeat(5 - review.rating) }}
                  </div>
                  <span class="text-xs text-gray-400">•</span>
                  <span class="text-xs text-gray-400 font-medium">{{ review.reviewed_at }}</span>
                </div>
              </div>
            </div>

            <p class="text-gray-700 text-sm leading-relaxed">{{ review.review_text || 'Pembeli tidak memberikan komentar tertulis.' }}</p>

            <div v-if="review.review_image" class="mt-2">
              <a :href="review.review_image" target="_blank" class="inline-block hover:opacity-90">
                <img :src="review.review_image" class="h-28 w-28 rounded-lg object-cover border bg-stone-50" />
              </a>
            </div>

            <div v-if="review.seller_reply" class="bg-indigo-50/50 border border-indigo-100 rounded-xl p-4 mt-3 space-y-1">
              <p class="text-xs font-bold text-indigo-900">Tanggapan Penjual:</p>
              <p class="text-xs text-indigo-950 leading-relaxed">{{ review.seller_reply }}</p>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- 🚨 MODAL LAPORKAN PRODUK -->
    <transition name="fade">
      <div v-if="reportModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 transition-all duration-300">
          
          <!-- Header -->
          <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-gray-800">Laporkan Produk Ini</h3>
            <button @click="reportModalOpen = false" class="text-gray-400 hover:text-gray-600 transition cursor-pointer">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- STEP 1: Pilih Alasan -->
          <div v-if="reportStep === 1" class="p-6 space-y-4">
            <p class="text-xs text-gray-400">Pilih alasan utama Anda melaporkan produk ini:</p>
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
            <!-- Info Produk -->
            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 text-xs space-y-1">
              <div class="text-gray-400">Produk Terlapor:</div>
              <div class="font-bold text-gray-800">{{ produk.nama }}</div>
              <div class="text-indigo-600 font-semibold mt-1">Alasan: {{ selectedReason }}</div>
            </div>

            <!-- Deskripsi (Wajib) -->
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-gray-600">Jelaskan detail keluhan/pelanggaran <span class="text-red-500">*</span></label>
              <textarea 
                v-model="reportForm.description"
                rows="4"
                placeholder="Tulis alasan detail mengapa produk ini melanggar aturan..."
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
              <p class="text-[10px] text-gray-400">Harap upload gambar tangkapan layar produk yang melanggar aturan.</p>
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

    <!-- 🛒 PURCHASE POPUP MODAL (MARKETPLACE-STYLE) -->
    <transition name="fade">
      <div 
        v-if="showPurchaseModal" 
        @click.self="showPurchaseModal = false"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
      >
        <!-- Scale transition container -->
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden border border-gray-100 transition-all duration-300 transform scale-100 relative">
          <!-- Close Button -->
          <button @click="showPurchaseModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
          </button>

          <div class="p-6 space-y-6">
            <!-- Product Header Info -->
            <div class="flex gap-4">
              <img :src="currentImage" alt="Produk" class="w-24 h-24 object-cover rounded-xl border border-gray-200 shadow-xs" />
              <div class="flex-1 flex flex-col justify-center text-left">
                <h4 class="font-bold text-gray-800 text-base line-clamp-1">{{ produk.nama }}</h4>
                <p class="text-xl font-extrabold text-blue-600 mt-1">Rp{{ Number(produk.harga).toLocaleString('id-ID') }}</p>
                <p class="text-xs text-gray-400 mt-0.5 font-semibold">Stok: {{ produk.stok }} {{ produk.satuan || '' }}</p>
              </div>
            </div>

            <!-- Variations List (if any) -->
            <div v-if="produk.variations && produk.variations.length > 0" class="space-y-4 border-t border-gray-100 pt-4 text-left">
              <div v-for="(v, idx) in produk.variations" :key="idx" class="space-y-2">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">{{ v.name }}</span>
                <div class="flex gap-2 flex-wrap">
                  <button 
                    v-for="(opt, optIdx) in v.options" 
                    :key="optIdx"
                    type="button"
                    @click="selectedVariations[v.name] = opt"
                    class="text-xs font-semibold px-4 py-2 rounded-xl border transition cursor-pointer"
                    :class="selectedVariations[v.name] === opt 
                      ? 'bg-indigo-600 text-white border-indigo-600 hover:bg-indigo-700 shadow-sm' 
                      : 'bg-white text-gray-700 border-gray-200 hover:border-gray-300 hover:bg-gray-50/50'"
                  >
                    {{ opt }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Quantity Settings -->
            <div class="flex items-center justify-between border-t border-gray-100 pt-4">
              <span class="text-sm font-bold text-gray-700">Jumlah</span>
              <div class="flex items-center gap-1">
                <!-- Minus Button -->
                <button 
                  type="button" 
                  @click="changeModalQty(-1)" 
                  :disabled="selectedQty <= 1"
                  class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-gray-800 disabled:opacity-50 disabled:bg-gray-100 disabled:cursor-not-allowed transition font-bold select-none cursor-pointer text-sm"
                >
                  &minus;
                </button>
                
                <!-- Number Input -->
                <input 
                  type="number" 
                  v-model.number="selectedQty" 
                  @input="validateModalQtyInput"
                  class="w-12 h-8 border border-gray-200 rounded-lg text-center text-sm font-bold focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />

                <!-- Plus Button -->
                <button 
                  type="button" 
                  @click="changeModalQty(1)" 
                  :disabled="selectedQty >= produk.stok"
                  class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-gray-800 disabled:opacity-50 disabled:bg-gray-100 disabled:cursor-not-allowed transition font-bold select-none cursor-pointer text-sm"
                >
                  &plus;
                </button>
              </div>
            </div>

            <!-- Total Price Display -->
            <div class="flex items-center justify-between border-t border-gray-100 pt-4">
              <span class="text-sm font-bold text-gray-700">Total</span>
              <span class="text-xl font-extrabold text-indigo-600">Rp{{ Number(purchaseTotalPrice).toLocaleString('id-ID') }}</span>
            </div>

            <!-- Footer Buttons -->
            <div class="flex gap-3 pt-2">
              <button 
                type="button" 
                @click="purchaseActionType = 'cart'; submitPurchaseAction()"
                class="flex-1 bg-yellow-400 hover:bg-yellow-500 text-white font-bold py-2.5 rounded-xl text-sm shadow-sm transition cursor-pointer"
              >
                + Keranjang
              </button>
              <button 
                type="button" 
                @click="purchaseActionType = 'buy'; submitPurchaseAction()"
                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-xl text-sm shadow-sm transition cursor-pointer"
              >
                Beli Sekarang
              </button>
            </div>
          </div>
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