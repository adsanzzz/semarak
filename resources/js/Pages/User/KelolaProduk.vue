<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'
import { ref } from 'vue'

const props = defineProps({
  products: Array,
  categories: Array,
})

/* 🔔 Notifikasi */
const notif = ref(null)
function showNotif(message, type = 'success') {
  notif.value = { message, type }
  setTimeout(() => (notif.value = null), 3000)
}

/* ➕ Tambah Produk */
const showForm = ref(false)
const form = useForm({
  nama: '', harga: '', stok: '', kategori: '', deskripsi: '', image: null,
})

function tambahProduk() {
  const data = new FormData()
  data.append('nama', form.nama)
  data.append('harga', form.harga)
  data.append('stok', form.stok)
  data.append('kategori', form.kategori)
  data.append('deskripsi', form.deskripsi)
  if (form.image) data.append('image', form.image)

  router.post(route('user.toko.store'), data, {
    forceFormData: true,
    onSuccess: () => {
      form.reset()
      showForm.value = false
      showNotif('✅ Produk berhasil ditambahkan', 'success')
      router.reload({ only: ['products'] })
    },
    onError: () => {
      showNotif('⚠️ Gagal menambahkan produk', 'error')
    }
  })
}

/* ✏️ Edit Produk */
const editingId = ref(null)
function startEditRow(produk) {
  editingId.value = produk.id
}
function hapusProduk(id) {
  if (!confirm('Yakin ingin menghapus produk ini?')) return
  router.delete(route('user.toko.destroy', id), {
    onSuccess: () => {
      showNotif('🗑️ Produk berhasil dihapus', 'success')
      router.reload({ only: ['products'] })
    }
  })
}
</script>

<template>
  <Head title="Kelola Produk" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Kelola Produk</h2>
    </template>

    <div class="flex">
      <Sidebar class="fixed left-0 top-0 h-screen" />

      <div class="flex-1 ml-64 bg-gray-100 min-h-screen p-8">
        <!-- 🔔 Notifikasi -->
        <transition name="fade">
          <div
            v-if="notif"
            class="fixed top-5 right-5 z-50 px-4 py-2 rounded border shadow-lg"
            :class="{
              'bg-green-100 text-green-800 border-green-300': notif?.type==='success',
              'bg-red-100 text-red-800 border-red-300': notif?.type==='error',
              'bg-blue-100 text-blue-800 border-blue-300': notif?.type==='info'
            }"
          >
            {{ notif.message }}
          </div>
        </transition>

        <!-- 🆕 Tabel Produk Modern -->
        <div class="bg-white rounded-lg shadow p-6">
          <!-- Header + Search -->
          <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-3">
            <h3 class="text-lg font-semibold text-gray-800">Daftar Produk</h3>
            <div class="flex items-center gap-3 w-full md:w-auto">
              <button
                @click="showForm = !showForm"
                class="flex items-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
              >
                <span class="mr-1">Tambah Produk</span>
                <span class="text-xl leading-none">＋</span>
              </button>

              <div class="relative">
                <input
                  type="text"
                  placeholder="Cari produk.."
                  class="border rounded-lg pl-10 pr-3 py-2 text-sm w-60 focus:ring focus:ring-blue-200"
                />
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-3 top-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Tabel -->
          <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-t border-gray-200">
              <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                <tr>
                  <!-- Kolom gambar dengan panah di header -->
                  <th class="px-4 py-3 w-16 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </th>
                  <th class="px-4 py-3">Nama Produk</th>
                  <th class="px-4 py-3">Penjual</th>
                  <th class="px-4 py-3">Harga (Rp)</th>
                  <th class="px-4 py-3">Sisa Stok</th>
                  <th class="px-4 py-3">Kategori</th>
                  <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="produk in products" :key="produk.id" class="hover:bg-gray-50">
                  <!-- gambar produk -->
                  <td class="px-4 py-3 text-center">
                    <img
                      :src="produk.image_url || 'https://via.placeholder.com/40'"
                      alt="produk"
                      class="w-10 h-10 rounded object-cover border mx-auto"
                    />
                  </td>
                  <!-- nama produk -->
                  <td class="px-4 py-3 text-gray-800 font-medium">
                    {{ produk.nama }}
                  </td>
                  <td class="px-4 py-3">{{ produk.penjual ?? 'Toko XYZ' }}</td>
                  <td class="px-4 py-3">{{ Number(produk.harga).toLocaleString('id-ID') }}</td>
                  <td class="px-4 py-3">{{ produk.stok }}</td>
                  <td class="px-4 py-3">{{ produk.kategori }}</td>
                  <td class="px-4 py-3 text-center flex items-center justify-center gap-3">
                    <button @click="startEditRow(produk)" class="text-gray-500 hover:text-blue-600" title="Edit">✏️</button>
                    <button @click="hapusProduk(produk.id)" class="text-red-500 hover:text-red-700" title="Hapus">🗑️</button>
                  </td>
                </tr>
                <tr v-if="products.length === 0">
                  <td colspan="7" class="text-center py-6 text-gray-500">Belum ada produk</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="flex justify-between items-center mt-4 text-sm text-gray-600">
            <span>Menampilkan 1-10 dari {{ products.length }} Hasil</span>
            <div class="flex items-center gap-2">
              <button class="px-3 py-1 border rounded text-gray-600 hover:bg-gray-100">&lt;</button>
              <button class="px-3 py-1 border rounded bg-blue-600 text-white">1</button>
              <button class="px-3 py-1 border rounded text-gray-600 hover:bg-gray-100">2</button>
              <span class="px-2">...</span>
              <button class="px-3 py-1 border rounded text-gray-600 hover:bg-gray-100">24</button>
              <button class="px-3 py-1 border rounded text-gray-600 hover:bg-gray-100">&gt;</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
