<script setup>
import { computed, ref } from 'vue'
import { Head, Link, usePage, useForm } from '@inertiajs/vue3'
import NavbarAuth from '@/Components/NavbarAuth.vue'

const props = defineProps({
  checkoutResult: {
    type: Object,
    default: () => null,
  }
})

const page = usePage()
const flashSuccess = computed(() => page.props.flash?.success)
const flashError = computed(() => page.props.flash?.error)

const isUploaded = ref(false)
const filePreview = ref(null)

const form = useForm({
  payment_proof: null,
  order_ids: props.checkoutResult ? props.checkoutResult.order_ids : [],
})

function onFileChange(event) {
  const file = event.target.files[0]
  form.payment_proof = file
  if (file) {
    filePreview.value = URL.createObjectURL(file)
  } else {
    filePreview.value = null
  }
}

function submitProof() {
  if (!form.payment_proof) {
    alert('Silakan pilih file bukti pembayaran terlebih dahulu.')
    return
  }

  form.post(route('checkout.upload-proof'), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      isUploaded.value = true
    }
  })
}

function formatCurrency(value) {
  return new Intl.NumberFormat("id-ID").format(value || 0)
}

function copyToClipboard(text) {
  navigator.clipboard.writeText(text)
  alert('Nomor rekening berhasil disalin!')
}
</script>

<template>
  <div>
    <NavbarAuth />

    <Head title="Checkout Berhasil" />

    <div class="max-w-3xl mx-auto p-6 space-y-6">
      <!-- FLASH NOTIFICATION -->
      <div
        v-if="flashSuccess"
        class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-700 shadow-sm transition duration-300"
      >
        {{ flashSuccess }}
      </div>

      <div
        v-if="flashError"
        class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-700 shadow-sm transition duration-300"
      >
        {{ flashError }}
      </div>

      <!-- IF NO TRANSACTION ACTIVE -->
      <div v-if="!checkoutResult" class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 text-center space-y-4">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <h1 class="text-xl font-bold text-gray-800">Tidak ada transaksi yang aktif</h1>
        <p class="text-gray-500 max-w-sm mx-auto text-sm">
          Silakan kembali ke toko untuk mencari produk dan membuat pesanan baru.
        </p>
        <Link
          :href="route('produk.lihat')"
          class="inline-block px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold shadow-md hover:bg-blue-700 transition"
        >
          Kembali Belanja
        </Link>
      </div>

      <!-- IF COD (CASH ON DELIVERY) / CASH -->
      <div v-else-if="checkoutResult.payment_method !== 'qris'" class="bg-white border border-green-100 rounded-2xl shadow-md p-8 text-center space-y-5">
        <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto text-green-500">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div>
          <h1 class="text-2xl font-bold text-green-700 mb-1">Pesanan Berhasil Dibuat!</h1>
          <p class="text-gray-600 text-sm">
            Metode Pembayaran: <span class="font-semibold text-gray-800">Cash On Delivery (COD)</span>
          </p>
        </div>
        <p class="text-sm text-gray-500 max-w-md mx-auto">
          Silakan siapkan pembayaran uang tunai sebesar <span class="font-bold text-gray-800">Rp {{ formatCurrency(checkoutResult.total_payment) }}</span> saat pesanan dikirimkan oleh kurir atau diambil di toko.
        </p>
        <div class="pt-4 border-t flex justify-center gap-3">
          <Link
            :href="route('user.riwayat-pemesanan')"
            class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-sm"
          >
            Lihat Pesanan Saya
          </Link>
          <Link
            :href="route('produk.lihat')"
            class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition"
          >
            Kembali Belanja
          </Link>
        </div>
      </div>

      <!-- IF QRIS FLOW -->
      <div v-else class="space-y-6">
        <!-- STEPS PROGRESS -->
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
          <div class="flex items-center justify-between text-xs sm:text-sm text-center">
            <div class="flex-1">
              <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-600 font-bold mb-1">✓</span>
              <p class="font-medium text-gray-700">Buat Pesanan</p>
            </div>
            <div class="h-0.5 bg-gray-200 flex-1 mx-2"></div>
            <div class="flex-1">
              <span :class="['inline-flex items-center justify-center w-8 h-8 rounded-full font-bold mb-1', isUploaded ? 'bg-green-100 text-green-600' : 'bg-blue-600 text-white shadow-md']">
                {{ isUploaded ? '✓' : '2' }}
              </span>
              <p :class="['font-medium', isUploaded ? 'text-gray-700' : 'text-blue-600']">Pembayaran & Upload Bukti</p>
            </div>
            <div class="h-0.5 bg-gray-200 flex-1 mx-2"></div>
            <div class="flex-1">
              <span :class="['inline-flex items-center justify-center w-8 h-8 rounded-full font-bold mb-1', isUploaded ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-400']">3</span>
              <p :class="['font-medium', isUploaded ? 'text-blue-600' : 'text-gray-400']">Verifikasi Penjual</p>
            </div>
          </div>
        </div>

        <!-- TOTAL BILL -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-md space-y-2 text-center">
          <h2 class="text-sm uppercase tracking-wider text-blue-100">Total Harga Pesanan</h2>
          <div class="text-3xl font-extrabold">Rp {{ formatCurrency(checkoutResult.total_payment) }}</div>
          <p class="text-xs text-blue-200">Silakan scan QRIS atau transfer rekening di bawah dan unggah bukti pembayarannya.</p>
        </div>

        <!-- BEFORE UPLOAD - QRIS DETAILS -->
        <div v-if="!isUploaded" class="space-y-6">
          <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-6">
            <h3 class="text-lg font-bold text-gray-800 border-b pb-3">Informasi Pembayaran QRIS Toko</h3>
            
            <div v-for="store in checkoutResult.stores" :key="store.id" class="space-y-6">
              <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 space-y-4">
                <div class="flex items-center justify-between">
                  <h4 class="font-bold text-gray-800 text-base">Toko: {{ store.nama_toko || store.name }}</h4>
                  <span class="px-2.5 py-1 text-xs font-semibold rounded bg-amber-100 text-amber-800">Pembayaran QRIS</span>
                </div>

                <div class="flex flex-col items-center justify-center p-2 text-center">
                  <!-- QRIS IMAGE -->
                  <div class="text-center space-y-3">
                    <div v-if="store.qris_image" class="bg-white p-3 rounded-xl border border-gray-200 inline-block shadow-sm">
                      <img :src="store.qris_image" alt="QRIS Toko" class="w-48 h-48 mx-auto object-contain" />
                    </div>
                    <div v-else class="w-48 h-48 mx-auto border border-dashed border-gray-300 rounded-xl flex flex-col items-center justify-center bg-white text-gray-400 p-4">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                      </svg>
                      <p class="text-xs text-center">QRIS Toko belum diunggah</p>
                    </div>

                    <div v-if="store.qris_image" class="pt-1">
                      <a
                        :href="store.qris_image"
                        :download="'QRIS_' + (store.nama_toko || store.name) + '.png'"
                        target="_blank"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        Download QRIS
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- MANDATORY UPLOAD FORM -->
          <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-4">
            <h3 class="text-lg font-bold text-gray-800">Upload Bukti Pembayaran (Wajib)</h3>
            <p class="text-xs text-gray-500">Silakan unggah tangkapan layar (screenshot) bukti transfer sukses atau struk pembayaran Anda.</p>

            <form @submit.prevent="submitProof" class="space-y-4">
              <!-- FILE INPUT & PREVIEW -->
              <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 p-6 text-center hover:bg-gray-100 transition cursor-pointer relative">
                <input
                  type="file"
                  accept="image/*"
                  required
                  @change="onFileChange"
                  class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                />
                
                <div v-if="!filePreview" class="space-y-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <p class="text-sm font-semibold text-blue-600">Klik atau seret file gambar bukti bayar di sini</p>
                  <p class="text-xs text-gray-400">JPG, JPEG, atau PNG (Maks 2MB)</p>
                </div>
                <div v-else class="space-y-3">
                  <img :src="filePreview" alt="Preview Bukti Pembayaran" class="max-h-48 rounded-lg object-contain shadow border mx-auto" />
                  <p class="text-xs text-gray-500 font-medium">{{ form.payment_proof?.name }}</p>
                  <p class="text-xs text-blue-600 font-semibold">Klik/seret untuk mengganti gambar</p>
                </div>
              </div>

              <!-- BUTTON SUBMIT -->
              <button
                type="submit"
                :disabled="form.processing || !form.payment_proof"
                class="w-full py-3 rounded-xl bg-green-600 text-white font-bold text-base hover:bg-green-700 transition shadow disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
              >
                <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ form.processing ? 'Mengirim...' : 'Kirim Bukti Pembayaran' }}</span>
              </button>
            </form>
          </div>
        </div>

        <!-- AFTER UPLOAD - SUCCESS ACTIONS -->
        <div v-else class="bg-white border border-green-200 rounded-2xl shadow-md p-8 text-center space-y-5">
          <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto text-green-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 animate-pulse" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold text-green-700 mb-1">Bukti Pembayaran Terkirim!</h1>
            <p class="text-gray-600 text-sm">Terima kasih atas pembayaran Anda.</p>
          </div>
          <p class="text-sm text-gray-500 max-w-md mx-auto">
            Bukti transfer telah kami kirimkan ke penjual untuk dilakukan verifikasi. Silakan pantau status pesanan Anda secara berkala di halaman riwayat pesanan.
          </p>
          <div class="pt-4 border-t flex justify-center gap-3">
            <Link
              :href="route('user.riwayat-pemesanan')"
              class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-sm"
            >
              Lihat Pesanan Saya
            </Link>
            <Link
              :href="route('produk.lihat')"
              class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition"
            >
              Kembali Belanja
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>