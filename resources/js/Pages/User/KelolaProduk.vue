<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, router, Link } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'
import { ref, computed } from 'vue'

const props = defineProps({
  products: Object,
  categories: Array,
})

/* 🔔 Notifikasi */
const notif = ref(null)

function showNotif(message, type = 'success') {
  notif.value = { message, type }
  setTimeout(() => (notif.value = null), 3000)
}

/* =========================
   FILTER SUB CATEGORY
========================= */

const filteredSubCategories = computed(() => {
  if (!form.kategori_id) return []

  const selected = props.categories.find(
    c => c.id == form.kategori_id
  )

  return selected ? selected.sub_categories : []
})

const filteredEditSubCategories = computed(() => {
  if (!editForm.kategori_id) return []

  const selected = props.categories.find(
    c => c.id == editForm.kategori_id
  )

  return selected ? selected.sub_categories : []
})

/* =========================
   FORM TAMBAH PRODUK
========================= */

const showForm = ref(false)

const form = useForm({
  nama: '',
  harga: '',
  stok: '',
  kategori_id: '',
  sub_kategori_id: '',
  deskripsi: '',
  image: null,
  warna: '',
  ukuran: '',
  berat: '',
})

function openForm() {
  form.reset()
  showForm.value = true
}

function closeForm() {
  showForm.value = false
}

function tambahProduk() {
  form.post(route('user.toko.store'), {
    forceFormData: true,

    onSuccess: () => {
      form.reset()
      closeForm()
      showNotif('Produk berhasil ditambahkan')
      router.reload({ only: ['products'] })
    }
  })
}

/* =========================
   EDIT PRODUK
========================= */

const editingId = ref(null)
const showEditForm = ref(false)
const showDeleteConfirm = ref(false)
const deletingId = ref(null)

const editForm = useForm({
  nama: '',
  harga: '',
  stok: '',
  kategori_id: '',
  sub_kategori_id: '',
  deskripsi: '',
  image: null,
  warna: '',
  ukuran: '',
  berat: '',
})

function startEditRow(produk) {
  editingId.value = produk.id

  editForm.nama = produk.nama
  editForm.harga = produk.harga
  editForm.stok = produk.stok
  editForm.kategori_id = produk.category?.id || ''
  editForm.sub_kategori_id = produk.sub_category?.id || ''
  editForm.deskripsi = produk.deskripsi
  editForm.berat = produk.berat
  editForm.warna = produk.warna
  editForm.ukuran = produk.ukuran
  editForm.image = null

  showEditForm.value = true
}

function closeEditForm() {
  showEditForm.value = false
}

function updateProduk() {
  editForm.transform((data) => ({
    ...data,
    _method: 'put',
  })).post(route('user.toko.update', editingId.value), {
    forceFormData: true,

    onSuccess: () => {
      closeEditForm()
      showNotif('Produk berhasil diperbarui')
      router.reload({ only: ['products'] })
    }
  })
}

/* =========================
   HAPUS PRODUK
========================= */

function hapusProduk(id) {
  deletingId.value = id
  showDeleteConfirm.value = true
}

function closeDeleteConfirm() {
  showDeleteConfirm.value = false
  deletingId.value = null
}

function confirmHapusProduk() {
  if (!deletingId.value) return

  router.delete(route('user.toko.destroy', deletingId.value), {
    onSuccess: () => {
      closeDeleteConfirm()
      showNotif('Produk berhasil dihapus')
      router.reload({ only: ['products'] })
    }
  })
}
</script>

<template>
<Head title="Kelola Produk - Semarak" />

<AuthenticatedLayout>

<template #header>
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
  <div>
    <h2 class="text-2xl font-extrabold text-[#0A3551] tracking-tight">
      Kelola Produk
    </h2>
    <p class="text-sm text-gray-500 mt-1">
      Kelola katalog produk yang Anda pasarkan di toko.
    </p>
  </div>
</div>
</template>

<div class="flex">

<!-- Sidebar -->
<Sidebar class="fixed left-0 top-0 h-screen z-10"/>

<!-- Main Content Area -->
<div class="flex-1 bg-gray-50 min-h-screen p-8 transition-all duration-300">

  <!-- 🔔 NOTIFICATION -->
  <transition name="fade">
    <div
      v-if="notif"
      class="fixed top-5 right-5 z-50 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-lg flex items-center gap-2"
    >
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-600">
        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9 9-9s9 3.615 9 9-4.365 9-9 9-9-3.615-9-9Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.74-5.24Z" clip-rule="evenodd" />
      </svg>
      <span class="font-medium text-sm">{{ notif.message }}</span>
    </div>
  </transition>

  <!-- 📦 DAFTAR PRODUK CARD -->
  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

    <div class="flex items-center justify-between mb-6 border-b border-gray-50 pb-4">
      <div>
        <h3 class="text-lg font-bold text-gray-800">Daftar Produk Toko</h3>
        <p class="text-xs text-gray-400 mt-0.5">Total terdaftar: {{ products.total }} produk</p>
      </div>
      
      <button
        @click="openForm"
        class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-bold text-sm shadow-sm hover:shadow transition duration-150 cursor-pointer"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Tambah Produk
      </button>
    </div>

    <!-- 📊 TABEL PRODUK -->
    <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-xs">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50/75">
          <tr class="text-left text-gray-500 font-bold text-xs uppercase tracking-wider">
            <th class="px-6 py-4 w-20 text-center">Gambar</th>
            <th class="px-6 py-4">Nama Produk</th>
            <th class="px-6 py-4">Deskripsi</th>
            <th class="px-6 py-4">Kategori / Sub</th>
            <th class="px-6 py-4 w-32">Harga</th>
            <th class="px-6 py-4 w-24 text-center">Stok</th>
            <th class="px-6 py-4 w-40 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
          <tr v-for="produk in products.data" :key="produk.id" class="hover:bg-gray-50/50 transition">
            <!-- Gambar -->
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <img 
                :src="produk.image_url || 'https://via.placeholder.com/150'" 
                class="w-12 h-12 object-cover rounded-lg border border-gray-200 shadow-xs mx-auto"
                alt="Gambar Produk"
              />
            </td>
            <!-- Nama -->
            <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-800">
              {{ produk.nama }}
            </td>
            <!-- Deskripsi -->
            <td class="px-6 py-4">
              <p class="text-xs text-gray-500 line-clamp-2 max-w-xs leading-relaxed" :title="produk.deskripsi">
                {{ produk.deskripsi || '-' }}
              </p>
            </td>
            <!-- Kategori -->
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                {{ produk.category?.nama_kategori || '-' }}
              </span>
              <div v-if="produk.sub_category?.nama_subkategori" class="text-[10px] text-gray-400 mt-1 pl-1">
                ↳ {{ produk.sub_category.nama_subkategori }}
              </div>
            </td>
            <!-- Harga -->
            <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900">
              Rp{{ Number(produk.harga).toLocaleString('id-ID') }}
            </td>
            <!-- Stok -->
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span :class="['px-2.5 py-0.5 rounded-md text-xs font-bold border', 
                            produk.stok > 10 
                              ? 'bg-emerald-50 text-emerald-700 border-emerald-100' 
                              : (produk.stok > 0 ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-red-50 text-red-700 border-red-100')]">
                {{ produk.stok }}
              </span>
            </td>
            <!-- Aksi -->
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <div class="flex justify-center gap-2">
                <button 
                  @click="startEditRow(produk)" 
                  class="inline-flex items-center gap-1 bg-yellow-400 hover:bg-yellow-500 text-white font-bold px-3 py-1.5 rounded-lg text-xs transition cursor-pointer"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                  </svg>
                  Edit
                </button>
                <button 
                  @click="hapusProduk(produk.id)" 
                  class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white font-bold px-3 py-1.5 rounded-lg text-xs transition cursor-pointer"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                  </svg>
                  Hapus
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="products.data.length === 0">
            <td colspan="7" class="py-12 text-center text-gray-400">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 mx-auto text-gray-300 mb-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
              </svg>
              Belum ada produk terdaftar. Klik "Tambah Produk" untuk menambahkan produk baru.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- 📄 PAGINATION -->
    <div v-if="products.links && products.links.length > 3" class="flex items-center justify-between border-t border-gray-100 bg-white px-4 py-4 sm:px-6 mt-4">
      <div class="flex flex-1 justify-between sm:hidden">
        <Link
          :href="products.prev_page_url || '#'"
          :class="['relative inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition', !products.prev_page_url ? 'opacity-50 cursor-not-allowed pointer-events-none' : '']"
        >
          Previous
        </Link>
        <Link
          :href="products.next_page_url || '#'"
          :class="['relative ml-3 inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition', !products.next_page_url ? 'opacity-50 cursor-not-allowed pointer-events-none' : '']"
        >
          Next
        </Link>
      </div>
      <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <div>
          <p class="text-xs text-gray-500">
            Menampilkan <span class="font-bold text-gray-800">{{ products.from || 0 }}</span> sampai <span class="font-bold text-gray-800">{{ products.to || 0 }}</span> dari <span class="font-bold text-gray-800">{{ products.total }}</span> produk
          </p>
        </div>
        <div>
          <nav class="isolate inline-flex -space-x-px rounded-xl shadow-xs" aria-label="Pagination">
            <template v-for="(link, i) in products.links" :key="i">
              <Link
                v-if="link.url"
                :href="link.url"
                v-html="link.label"
                :class="[
                  'relative inline-flex items-center px-3.5 py-2 text-xs font-semibold focus:z-20 transition-all duration-150',
                  link.active 
                    ? 'z-10 bg-indigo-600 text-white rounded-xl mx-0.5 shadow-sm' 
                    : 'text-gray-600 hover:bg-gray-50 rounded-xl mx-0.5 border border-transparent hover:border-gray-200',
                ]"
              />
              <span
                v-else
                v-html="link.label"
                class="relative inline-flex items-center px-3.5 py-2 text-xs font-semibold text-gray-300 rounded-xl mx-0.5 cursor-not-allowed"
              />
            </template>
          </nav>
        </div>
      </div>
    </div>

  </div>

  <!-- ➕ MODAL TAMBAH PRODUK -->
  <transition name="fade">
    <div
      v-if="showForm"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    >
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden border border-gray-100 transition-all duration-300">
        <!-- Header -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
          <h3 class="text-lg font-bold text-gray-800">
            Tambah Produk Baru
          </h3>
          <button @click="closeForm" class="text-gray-400 hover:text-gray-600 transition cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="tambahProduk" class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
          <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">Nama Produk</label>
            <input v-model="form.nama" placeholder="Masukkan nama produk" class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"/>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Harga (Rp)</label>
              <input v-model="form.harga" type="number" placeholder="Contoh: 15000" class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"/>
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Stok</label>
              <input v-model="form.stok" type="number" placeholder="Contoh: 50" class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"/>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Kategori</label>
              <select v-model="form.kategori_id" class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                <option value="">Pilih Kategori</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.nama_kategori }}
                </option>
              </select>
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Sub Kategori</label>
              <select v-model="form.sub_kategori_id" class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                <option value="">Pilih Sub Kategori</option>
                <option v-for="sub in filteredSubCategories" :key="sub.id" :value="sub.id">
                  {{ sub.nama_subkategori }}
                </option>
              </select>
            </div>
          </div>

          <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">Deskripsi Produk</label>
            <textarea v-model="form.deskripsi" placeholder="Tuliskan spesifikasi dan deskripsi produk..." class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm h-24 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"></textarea>
          </div>

          <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">Gambar Produk</label>
            <input type="file" @change="e => form.image = e.target.files[0]" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer"/>
          </div>

          <!-- Buttons Footer -->
          <div class="flex justify-end gap-3 pt-4 border-t border-gray-50">
            <button type="button" @click="closeForm" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-5 py-2 rounded-xl text-sm transition cursor-pointer">
              Batal
            </button>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-2 rounded-xl text-sm shadow-sm transition cursor-pointer">
              Simpan Produk
            </button>
          </div>
        </form>
      </div>
    </div>
  </transition>

  <!-- ✏️ MODAL EDIT PRODUK -->
  <transition name="fade">
    <div
      v-if="showEditForm"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    >
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden border border-gray-100 transition-all duration-300">
        <!-- Header -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
          <h3 class="text-lg font-bold text-gray-800">
            Edit Produk
          </h3>
          <button @click="closeEditForm" class="text-gray-400 hover:text-gray-600 transition cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="updateProduk" class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
          <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">Nama Produk</label>
            <input v-model="editForm.nama" placeholder="Masukkan nama produk" class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"/>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Harga (Rp)</label>
              <input v-model="editForm.harga" type="number" placeholder="Contoh: 15000" class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"/>
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Stok</label>
              <input v-model="editForm.stok" type="number" placeholder="Contoh: 50" class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"/>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Kategori</label>
              <select v-model="editForm.kategori_id" class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                <option value="">Pilih Kategori</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.nama_kategori }}
                </option>
              </select>
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Sub Kategori</label>
              <select v-model="editForm.sub_kategori_id" class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                <option value="">Pilih Sub Kategori</option>
                <option v-for="sub in filteredEditSubCategories" :key="sub.id" :value="sub.id">
                  {{ sub.nama_subkategori }}
                </option>
              </select>
            </div>
          </div>

          <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">Deskripsi Produk</label>
            <textarea v-model="editForm.deskripsi" placeholder="Tuliskan spesifikasi dan deskripsi produk..." class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm h-24 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"></textarea>
          </div>

          <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">Gambar Produk</label>
            <input type="file" @change="e => editForm.image = e.target.files[0]" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer"/>
          </div>

          <!-- Buttons Footer -->
          <div class="flex justify-end gap-3 pt-4 border-t border-gray-50">
            <button type="button" @click="closeEditForm" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-5 py-2 rounded-xl text-sm transition cursor-pointer">
              Batal
            </button>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-2 rounded-xl text-sm shadow-sm transition cursor-pointer">
              Simpan Perubahan
            </button>
          </div>
        </form>
      </div>
    </div>
  </transition>

  <!-- 🚨 MODAL HAPUS -->
  <transition name="fade">
    <div
      v-if="showDeleteConfirm"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    >
      <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-gray-100 transition-all duration-300">
        <div class="flex items-center gap-3 text-red-600 mb-3">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
          </svg>
          <h3 class="text-lg font-bold text-gray-800">Konfirmasi Hapus</h3>
        </div>
        <p class="mb-6 text-sm text-gray-600">Apakah Anda yakin ingin menghapus produk ini secara permanen dari katalog toko Anda? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="flex justify-end gap-3">
          <button @click="closeDeleteConfirm" class="rounded-xl bg-gray-100 hover:bg-gray-200 px-4 py-2.5 text-sm font-bold text-gray-700 transition cursor-pointer">Batal</button>
          <button @click="confirmHapusProduk" class="rounded-xl bg-red-600 hover:bg-red-700 px-4 py-2.5 text-sm font-bold text-white shadow-xs transition cursor-pointer">Hapus Permanen</button>
        </div>
      </div>
    </div>
  </transition>

</div>
</div>

</AuthenticatedLayout>
</template>