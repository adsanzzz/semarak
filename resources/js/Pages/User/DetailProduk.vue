<script setup>
import { usePage, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import NavbarAuth from '@/Components/NavbarAuth.vue'

// Ambil data dari controller
const { produk } = usePage().props

// Notifikasi
const notif = ref(null)

function showNotif(message, type = 'success') {
  notif.value = { message, type }
  setTimeout(() => notif.value = null, 2500)
}

// Tambah ke keranjang
function tambahKeranjang() {
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
</script>

<template>
  <div>
    <NavbarAuth />

    <div class="max-w-6xl mx-auto px-6 py-10">
      
      <!-- Notifikasi -->
      <transition name="fade">
        <div
          v-if="notif"
          class="fixed top-5 right-5 px-4 py-2 rounded shadow text-white z-50"
          :class="notif.type === 'success' ? 'bg-green-500' : 'bg-red-500'"
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
          <img
            :src="produk.image || 'https://via.placeholder.com/500x400'"
            class="w-full h-[400px] object-cover rounded-xl shadow"
          />
        </div>

        <!-- Detail Produk -->
        <div class="space-y-4">
          
          <!-- Nama -->
          <h1 class="text-2xl font-bold text-gray-800">
            {{ produk.nama }}
          </h1>

          <!-- Toko -->
          <p class="text-sm text-gray-500">
            Toko: {{ produk.toko || '-' }}
          </p>

          <!-- Kategori -->
          <p class="text-sm text-gray-500">
            Kategori: {{ produk.kategori || '-' }}
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
            <p class="text-gray-600 text-sm leading-relaxed">
              {{ produk.deskripsi || 'Tidak ada deskripsi' }}
            </p>
          </div>

          <!-- Tombol -->
          <div class="flex gap-4 pt-4">
            
            <button
              @click="tambahKeranjang"
              class="bg-yellow-400 hover:bg-yellow-500 text-white px-6 py-3 rounded-lg font-medium w-full"
            >
              + Keranjang
            </button>

            <button
              class="border border-gray-300 px-6 py-3 rounded-lg w-full hover:bg-gray-100"
            >
              Beli Sekarang
            </button>

             <!-- TOMBOL CHAT -->
  <a
    :href="route('chat.start', produk.user_id)"
    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg text-center"
  >
    💬 Chat Penjual
  </a>

          </div>

        </div>

      </div>

    </div>
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