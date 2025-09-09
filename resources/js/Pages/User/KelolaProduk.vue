<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'
import { ref } from 'vue'

const props = defineProps({
  products: Array,
  categories: Array,
})

/* 🔔 Notifikasi (toast) */
const notif = ref(null)
function showNotif(message, type = 'success') {
  // type bisa: success | error | info
  notif.value = { message, type }
  setTimeout(() => { notif.value = null }, 3000)
}

/* ➕ Tambah Produk */
const showForm = ref(false)
const form = useForm({
  nama: '', harga: '', stok: '', kategori: '', deskripsi: '', image: null,
  warna: '', ukuran: '', berat: '',
  opsiWarna: false, opsiUkuran: false, opsiBerat: false
})

function tambahProduk() {
  const data = new FormData()
  data.append('nama', form.nama)
  data.append('harga', form.harga)
  data.append('stok', form.stok)
  data.append('kategori', form.kategori)
  data.append('deskripsi', form.deskripsi)
  if (form.image) data.append('image', form.image)
  if (form.opsiWarna) data.append('warna', form.warna)
  if (form.opsiUkuran) data.append('ukuran', form.ukuran)
  if (form.opsiBerat) data.append('berat', form.berat)

  router.post(route('user.toko.store'), data, {
    forceFormData: true,
    onSuccess: () => {
      form.reset()
      showForm.value = false             // ⬅️ tutup form
      showNotif('✅ Produk berhasil ditambahkan', 'success')
      router.reload({ only: ['products'] })
    },
    onError: () => {
      showNotif('⚠️ Gagal menambahkan produk', 'error')
    }
  })
}

/* ✏️ Inline Edit Produk */
const editingId = ref(null)
const editRow = useForm({
  id: null, nama: '', harga: '', stok: '', kategori: '', deskripsi: '', image: null,
  warna: '', ukuran: '', berat: '',
  opsiWarna: false, opsiUkuran: false, opsiBerat: false
})

function startEditRow(produk) {
  editingId.value = produk.id
  editRow.id = produk.id
  editRow.nama = produk.nama ?? ''
  editRow.harga = produk.harga ?? 0
  editRow.stok = produk.stok ?? 0
  editRow.kategori = produk.kategori ?? ''
  editRow.deskripsi = produk.deskripsi ?? ''
  editRow.image = null
  editRow.opsiWarna = !!produk.warna
  editRow.opsiUkuran = !!produk.ukuran
  editRow.opsiBerat = !!produk.berat
  editRow.warna = produk.warna || ''
  editRow.ukuran = produk.ukuran || ''
  editRow.berat = produk.berat || ''
}

function cancelEditRow() {
  editingId.value = null
  editRow.reset()
}

function saveEditRow() {
  const data = new FormData()
  data.append('nama', editRow.nama)
  data.append('harga', editRow.harga)
  data.append('stok', editRow.stok)
  data.append('kategori', editRow.kategori)
  data.append('deskripsi', editRow.deskripsi)
  if (editRow.image) data.append('image', editRow.image)
  if (editRow.opsiWarna) data.append('warna', editRow.warna)
  if (editRow.opsiUkuran) data.append('ukuran', editRow.ukuran)
  if (editRow.opsiBerat) data.append('berat', editRow.berat)
  data.append('_method', 'PUT')

  router.post(route('user.toko.update', editRow.id), data, {
    forceFormData: true,
    onSuccess: () => {
      editingId.value = null
      editRow.reset()
      showNotif('✅ Produk berhasil diperbarui', 'success')
      router.reload({ only: ['products'] })
    },
    onError: () => {
      showNotif('⚠️ Gagal memperbarui produk', 'error')
    }
  })
}

/* 🗑️ Hapus Produk */
function hapusProduk(id) {
  if (!confirm('Yakin ingin menghapus produk ini?')) return
  router.delete(route('user.toko.destroy', id), {
    onSuccess: () => {
      showNotif('🗑️ Produk berhasil dihapus', 'success')
      router.reload({ only: ['products'] })
    },
    onError: () => {
      showNotif('⚠️ Gagal menghapus produk', 'error')
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

        <!-- 🔔 Toast Notifikasi -->
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

        <!-- Header + Tambah Produk -->
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-bold">Daftar Produk Saya</h3>
          <button
            @click="showForm = !showForm"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          >
            + Tambah Produk
          </button>
        </div>

        <!-- Form Tambah Produk -->
        <div v-if="showForm" class="mb-8 bg-gray-50 p-6 rounded-lg border border-blue-200 shadow">
          <form @submit.prevent="tambahProduk" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-gray-700 mb-1">Nama Produk</label>
              <input v-model="form.nama" type="text" class="border rounded px-3 py-2 w-full" required />
            </div>
            <div>
              <label class="block text-gray-700 mb-1">Harga</label>
              <input v-model="form.harga" type="number" min="0" class="border rounded px-3 py-2 w-full" required />
            </div>
            <div>
              <label class="block text-gray-700 mb-1">Stok</label>
              <input v-model="form.stok" type="number" min="0" class="border rounded px-3 py-2 w-full" required />
            </div>
            <div>
              <label class="block text-gray-700 mb-1">Kategori</label>
              <select v-model="form.kategori" class="border rounded px-3 py-2 w-full" required>
                <option value="" disabled>Pilih Kategori</option>
                <option v-for="kat in categories" :key="kat.id" :value="kat.name">{{ kat.name }}</option>
              </select>
            </div>
            <div class="md:col-span-2">
              <label class="block text-gray-700 mb-1">Deskripsi</label>
              <textarea v-model="form.deskripsi" rows="3" class="border rounded px-3 py-2 w-full"></textarea>
            </div>
            <div class="md:col-span-2">
              <label class="block text-gray-700 mb-1">Upload Gambar</label>
              <input type="file" accept="image/*" @change="e => form.image = e.target.files[0]" class="border rounded px-3 py-2 w-full" />
            </div>
            <!-- Opsi tambahan -->
            <div class="md:col-span-2 flex gap-6 items-center mt-2">
              <label class="flex items-center gap-2">
                <input type="checkbox" v-model="form.opsiWarna" /> Opsi Warna
              </label>
              <label class="flex items-center gap-2">
                <input type="checkbox" v-model="form.opsiUkuran" /> Opsi Ukuran
              </label>
              <label class="flex items-center gap-2">
                <input type="checkbox" v-model="form.opsiBerat" /> Opsi Berat
              </label>
            </div>
            <div v-if="form.opsiWarna" class="md:col-span-2">
              <label class="block text-gray-700 mb-1">Warna</label>
              <input v-model="form.warna" type="text" class="border rounded px-3 py-2 w-full" placeholder="Contoh: Merah, Biru, Hitam" />
            </div>
            <div v-if="form.opsiUkuran" class="md:col-span-2">
              <label class="block text-gray-700 mb-1">Ukuran</label>
              <input v-model="form.ukuran" type="text" class="border rounded px-3 py-2 w-full" placeholder="Contoh: S, M, L, XL" />
            </div>
            <div v-if="form.opsiBerat" class="md:col-span-2">
              <label class="block text-gray-700 mb-1">Berat (gram)</label>
              <input v-model="form.berat" type="text" class="border rounded px-3 py-2 w-full" placeholder="Contoh: 500" />
            </div>
            <div class="md:col-span-2 flex justify-end gap-2">
              <button type="button" @click="showForm = false" class="bg-gray-300 text-gray-700 px-4 py-2 rounded">Batal</button>
              <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Simpan</button>
            </div>
          </form>
        </div>

        <!-- Tabel Produk -->
        <div class="overflow-x-auto">
          <table class="w-full border border-gray-200 text-sm">
            <thead class="bg-gray-100 text-gray-700">
              <tr>
                <th class="px-4 py-2 border">#</th>
                <th class="px-4 py-2 border">Nama</th>
                <th class="px-4 py-2 border">Harga</th>
                <th class="px-4 py-2 border">Stok</th>
                <th class="px-4 py-2 border">Kategori</th>
                <th class="px-4 py-2 border">Terjual</th>
                <th class="px-4 py-2 border">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(produk, index) in products" :key="produk.id" class="text-center align-top">
                <td class="px-4 py-2 border">{{ index + 1 }}</td>

                <!-- Mode Edit -->
                <template v-if="editingId === produk.id">
                  <td class="px-4 py-2 border">
                    <input v-model="editRow.nama" type="text" class="border rounded px-2 py-1 w-full" required />
                  </td>
                  <td class="px-4 py-2 border">
                    <input v-model="editRow.harga" type="number" min="0" class="border rounded px-2 py-1 w-full" required />
                  </td>
                  <td class="px-4 py-2 border">
                    <input v-model="editRow.stok" type="number" min="0" class="border rounded px-2 py-1 w-full" required />
                  </td>
                  <td class="px-4 py-2 border">
                    <select v-model="editRow.kategori" class="border rounded px-2 py-1 w-full" required>
                      <option value="" disabled>Pilih Kategori</option>
                      <option v-for="kat in categories" :key="kat.id" :value="kat.name">{{ kat.name }}</option>
                    </select>
                  </td>
                  <td class="px-4 py-2 border">{{ produk.terjual ?? 0 }}</td>
                  <td class="px-4 py-2 border">
                    <div class="flex flex-col gap-2">
                      <input type="file" accept="image/*" @change="e => editRow.image = e.target.files[0]" class="border rounded px-2 py-1 w-full" />
                      <textarea v-model="editRow.deskripsi" rows="2" class="border rounded px-2 py-1 w-full" placeholder="Deskripsi (opsional)"></textarea>

                      <div class="grid grid-cols-1 gap-2 text-left">
                        <label class="flex items-center gap-2">
                          <input type="checkbox" v-model="editRow.opsiWarna" /> Opsi Warna
                        </label>
                        <input v-if="editRow.opsiWarna" v-model="editRow.warna" type="text" class="border rounded px-2 py-1 w-full" placeholder="Contoh: Merah, Biru, Hitam" />

                        <label class="flex items-center gap-2">
                          <input type="checkbox" v-model="editRow.opsiUkuran" /> Opsi Ukuran
                        </label>
                        <input v-if="editRow.opsiUkuran" v-model="editRow.ukuran" type="text" class="border rounded px-2 py-1 w-full" placeholder="Contoh: S, M, L, XL" />

                        <label class="flex items-center gap-2">
                          <input type="checkbox" v-model="editRow.opsiBerat" /> Opsi Berat
                        </label>
                        <input v-if="editRow.opsiBerat" v-model="editRow.berat" type="text" class="border rounded px-2 py-1 w-full" placeholder="Contoh: 500" />
                      </div>

                      <div class="flex gap-2 justify-center">
                        <button @click="saveEditRow" class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700">Simpan</button>
                        <button @click="cancelEditRow" class="bg-gray-400 text-white px-3 py-1 rounded text-xs hover:bg-gray-500">Batal</button>
                      </div>
                    </div>
                  </td>
                </template>

                <!-- Mode Tampil -->
                <template v-else>
                  <td class="px-4 py-2 border text-left">{{ produk.nama }}</td>
                  <td class="px-4 py-2 border">Rp{{ Number(produk.harga ?? 0).toLocaleString('id-ID') }}</td>
                  <td class="px-4 py-2 border">{{ produk.stok }}</td>
                  <td class="px-4 py-2 border">{{ produk.kategori }}</td>
                  <td class="px-4 py-2 border">{{ produk.terjual ?? 0 }}</td>
                  <td class="px-4 py-2 border">
                    <button @click="startEditRow(produk)" class="bg-yellow-500 text-white px-3 py-1 rounded text-xs mr-2 hover:bg-yellow-600">Edit</button>
                    <button @click="hapusProduk(produk.id)" class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700">Hapus</button>
                  </td>
                </template>
              </tr>

              <tr v-if="products.length === 0">
                <td colspan="7" class="text-center py-4 text-gray-500">Belum ada produk</td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
