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

// ➕ Form tambah produk dalam modal
const showForm = ref(false)

/* Checkbox opsional */
const enableBerat = ref(false)
const enableWarna = ref(false)
const enableUkuran = ref(false)

const form = useForm({
  nama: '',
  harga: '',
  stok: '',
  kategori_id: '',
  deskripsi: '',
  image: null,
  warna: '',
  ukuran: '',
  berat: '',
})

function openForm() {
  form.reset()
  enableBerat.value = false
  enableWarna.value = false
  enableUkuran.value = false
  showForm.value = true
}
function closeForm() {
  showForm.value = false
}

function tambahProduk() {
  const data = new FormData()
  data.append('nama', form.nama)
  data.append('harga', form.harga)
  data.append('stok', form.stok)
  data.append('kategori_id', form.kategori_id)
  data.append('deskripsi', form.deskripsi)
  if (form.image) data.append('image', form.image)

  if (enableBerat.value) data.append('berat', form.berat)
  if (enableWarna.value) data.append('warna', form.warna)
  if (enableUkuran.value) data.append('ukuran', form.ukuran)

  router.post(route('user.toko.store'), data, {
    forceFormData: true,
    onSuccess: () => {
      form.reset()
      closeForm()
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

      <div class="flex-1 ml-0 bg-gray-100 min-h-screen p-8">
        <!-- 🔔 Notifikasi -->
        <transition name="fade">
          <div
            v-if="notif"
            class="fixed top-5 right-5 z-50 px-4 py-2 rounded border shadow-lg"
            :class="{
              'bg-green-100 text-green-800 border-green-300': notif?.type==='success',
              'bg-red-100 text-red-800 border-red-300': notif?.type==='error'
            }"
          >
            {{ notif.message }}
          </div>
        </transition>

        <!-- 🆕 Tabel Produk -->
        <div class="bg-white rounded-lg shadow p-6">
          <div class="mb-6 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Daftar Produk</h3>
            <button @click="openForm" class="flex items-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
              <span class="mr-1">Tambah Produk</span>
              <span class="text-xl leading-none">＋</span>
            </button>
          </div>

          <!-- Modal Form -->
          <div v-if="showForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-8 relative animate-fadeIn overflow-y-auto max-h-screen">
              <button @click="closeForm" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 text-xl">×</button>
              <h3 class="text-xl font-bold mb-6 text-gray-800">Tambah Produk</h3>
              <form @submit.prevent="tambahProduk" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium mb-1">Nama Produk</label>
                  <input v-model="form.nama" type="text" class="w-full border rounded px-3 py-2" required />
                </div>
                <div class="flex gap-4">
                  <div class="flex-1">
                    <label class="block text-sm font-medium mb-1">Harga</label>
                    <input v-model="form.harga" type="number" class="w-full border rounded px-3 py-2" required />
                  </div>
                  <div class="flex-1">
                    <label class="block text-sm font-medium mb-1">Stok</label>
                    <input v-model="form.stok" type="number" class="w-full border rounded px-3 py-2" required />
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Kategori</label>
                  <select v-model="form.kategori_id" class="w-full border rounded px-3 py-2 text-gray-900" required>
                    <option value="" disabled>Pilih Kategori</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                      {{ cat.nama_kategori || cat.nama }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Deskripsi</label>
                  <textarea v-model="form.deskripsi" class="w-full border rounded px-3 py-2" rows="3"></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">Gambar Produk</label>
                  <input type="file" @change="e => form.image = e.target.files[0]" class="w-full" accept="image/*" />
                </div>

                <!-- Checkbox Opsional -->
                <div class="space-y-2">
                  <label class="flex items-center gap-2">
                    <input type="checkbox" v-model="enableBerat" />
                    <span>Tambah opsi Berat</span>
                  </label>
                  <label class="flex items-center gap-2">
                    <input type="checkbox" v-model="enableWarna" />
                    <span>Tambah opsi Warna</span>
                  </label>
                  <label class="flex items-center gap-2">
                    <input type="checkbox" v-model="enableUkuran" />
                    <span>Tambah opsi Ukuran</span>
                  </label>
                </div>

                <!-- Field Opsional -->
                <div v-if="enableBerat">
                  <label class="block text-sm font-medium mb-1">Berat (gram)</label>
                  <input v-model="form.berat" type="number" class="w-full border rounded px-3 py-2" placeholder="contoh: 500" />
                </div>
                <div v-if="enableWarna">
                  <label class="block text-sm font-medium mb-1">Warna</label>
                  <input v-model="form.warna" type="text" class="w-full border rounded px-3 py-2" placeholder="contoh: Merah, Biru" />
                </div>
                <div v-if="enableUkuran">
                  <label class="block text-sm font-medium mb-1">Ukuran</label>
                  <input v-model="form.ukuran" type="text" class="w-full border rounded px-3 py-2" placeholder="contoh: M, L, XL" />
                </div>

                <div class="flex justify-end gap-2 mt-6">
                  <button type="button" @click="closeForm" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Batal</button>
                  <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Simpan</button>
                </div>
              </form>
            </div>
          </div>

          <!-- Tabel Produk -->
          <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-t border-gray-200">
              <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                <tr>
                  <th class="px-4 py-3">Gambar</th>
                  <th class="px-4 py-3">Nama</th>
                  <th class="px-4 py-3">Harga (Rp)</th>
                  <th class="px-4 py-3">Stok</th>
                  <th class="px-4 py-3">Kategori</th>
                  <th class="px-4 py-3">Berat</th>
                  <th class="px-4 py-3">Warna</th>
                  <th class="px-4 py-3">Ukuran</th>
                  <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="produk in products" :key="produk.id" class="hover:bg-gray-50">
                  <td class="px-4 py-3">
                    <img
                      :src="produk.image_url || 'https://via.placeholder.com/40'"
                      alt="produk"
                      class="w-10 h-10 rounded object-cover border"
                    />
                  </td>
                  <td class="px-4 py-3">{{ produk.nama }}</td>
                  <td class="px-4 py-3">{{ Number(produk.harga).toLocaleString('id-ID') }}</td>
                  <td class="px-4 py-3">{{ produk.stok }}</td>
                  <td class="px-4 py-3">{{ produk.category?.nama || '-' }}</td>
                  <td class="px-4 py-3">{{ produk.berat || '-' }}</td>
                  <td class="px-4 py-3">{{ produk.warna || '-' }}</td>
                  <td class="px-4 py-3">{{ produk.ukuran || '-' }}</td>
                  <td class="px-4 py-3 text-center flex items-center justify-center gap-3">
                    <button @click="startEditRow(produk)" class="text-gray-500 hover:text-blue-600">✏️</button>
                    <button @click="hapusProduk(produk.id)" class="text-red-500 hover:text-red-700">🗑️</button>
                  </td>
                </tr>
                <tr v-if="products.length === 0">
                  <td colspan="9" class="text-center py-6 text-gray-500">Belum ada produk</td>
                </tr>
              </tbody>
            </table>
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
