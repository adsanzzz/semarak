<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'
import { ref, computed } from 'vue'

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
  editForm.post(route('user.toko.update', editingId.value), {
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
  if (!confirm('Yakin ingin menghapus produk ini?')) return

  router.delete(route('user.toko.destroy', id), {
    onSuccess: () => {
      showNotif('Produk berhasil dihapus')
      router.reload({ only: ['products'] })
    }
  })
}
</script>

<template>
<Head title="Kelola Produk" />

<AuthenticatedLayout>

<template #header>
<h2 class="text-xl font-semibold text-gray-800">
Kelola Produk
</h2>
</template>

<div class="flex">

<Sidebar class="fixed left-0 top-0 h-screen"/>

<div class="flex-1 bg-gray-100 min-h-screen p-8">

<!-- NOTIF -->
<div
v-if="notif"
class="fixed top-5 right-5 bg-green-100 border border-green-300 text-green-800 px-4 py-2 rounded shadow"
>
{{ notif.message }}
</div>

<!-- TABEL -->
<div class="bg-white rounded-lg shadow p-6">

<div class="flex justify-between mb-6">

<h3 class="text-lg font-semibold">
Daftar Produk
</h3>

<button
@click="openForm"
class="bg-blue-600 text-white px-4 py-2 rounded"
>
Tambah Produk
</button>

</div>

<!-- MODAL TAMBAH -->
<div
v-if="showForm"
class="fixed inset-0 flex items-center justify-center bg-black/40"
>

<div class="bg-white p-8 rounded-lg w-full max-w-lg">

<h3 class="text-xl font-bold mb-4">
Tambah Produk
</h3>

<form @submit.prevent="tambahProduk" class="space-y-4">

<input v-model="form.nama" placeholder="Nama Produk" class="w-full border rounded px-3 py-2"/>

<input v-model="form.harga" type="number" placeholder="Harga" class="w-full border rounded px-3 py-2"/>

<input v-model="form.stok" type="number" placeholder="Stok" class="w-full border rounded px-3 py-2"/>

<select v-model="form.kategori_id" class="w-full border rounded px-3 py-2">
<option value="">Pilih Kategori</option>
<option v-for="cat in categories" :key="cat.id" :value="cat.id">
{{ cat.nama_kategori }}
</option>
</select>

<select v-model="form.sub_kategori_id" class="w-full border rounded px-3 py-2">
<option value="">Pilih Sub Kategori</option>
<option v-for="sub in filteredSubCategories" :key="sub.id" :value="sub.id">
{{ sub.nama_subkategori }}
</option>
</select>

<textarea v-model="form.deskripsi" class="w-full border rounded px-3 py-2"></textarea>

<input type="file" @change="e => form.image = e.target.files[0]"/>

<div class="flex justify-end gap-2">
<button type="button" @click="closeForm" class="bg-gray-300 px-4 py-2 rounded">
Batal
</button>

<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
Simpan
</button>
</div>

</form>

</div>
</div>

<!-- TABLE -->
<table class="w-full text-sm">

<thead>
<tr class="bg-gray-100">
<th class="p-3">Nama</th>
<th class="p-3">Harga</th>
<th class="p-3">Stok</th>
<th class="p-3">Kategori</th>
<th class="p-3">Sub</th>
<th class="p-3">Aksi</th>
</tr>
</thead>

<tbody>
<tr v-for="produk in products" :key="produk.id">

<td class="p-3">{{ produk.nama }}</td>
<td class="p-3">{{ produk.harga }}</td>
<td class="p-3">{{ produk.stok }}</td>

<td class="p-3">
{{ produk.category?.nama_kategori }}
</td>

<td class="p-3">
{{ produk.sub_category?.nama_subkategori }}
</td>

<td class="p-3 flex gap-2">
<button @click="startEditRow(produk)">✏️</button>
<button @click="hapusProduk(produk.id)">🗑️</button>
</td>

</tr>
</tbody>

</table>

</div>
</div>
</div>

</AuthenticatedLayout>
</template>