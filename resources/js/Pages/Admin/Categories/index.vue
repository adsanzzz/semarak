<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    categories: Array
})

const form = useForm({
    kategori: '',
    subkategori: '',
})

const notif = ref({ show: false, message: '', type: 'success' })

function showNotif(message, type = 'success') {
    notif.value = { show: true, message, type }
    setTimeout(() => {
        notif.value.show = false
    }, 3000)
}

function submit() {
    form.post(route('admin.categories.store'), {
        onSuccess: () => {
            form.reset()
            showNotif('Kategori berhasil ditambahkan')
        },
        onError: () => {
            showNotif('Terjadi kesalahan', 'error')
        }
    })
}

function deleteCategory(id) {
    if (!confirm('Yakin ingin menghapus sub kategori ini?')) return

    router.delete(route('admin.categories.destroy', id), {
        onSuccess: () => showNotif('Data berhasil dihapus'),
        onError: () => showNotif('Gagal menghapus', 'error')
    })
}
</script>

<template>
<Head title="Kelola Kategori" />

<AdminLayout>

<!-- NOTIF -->
<div v-if="notif.show" class="fixed right-6 top-6 z-50">
    <div
        :class="notif.type === 'success' ? 'bg-green-500' : 'bg-red-500'"
        class="text-white px-4 py-2 rounded shadow"
    >
        {{ notif.message }}
    </div>
</div>

<div class="bg-white shadow rounded-lg p-6">

<!-- HEADER -->
<div class="flex justify-between items-center mb-6">
<h2 class="text-xl font-bold text-gray-800">
Kelola Kategori
</h2>
</div>

<!-- FORM -->
<form
@submit.prevent="submit"
class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6"
>

<div>
<label class="text-sm font-semibold text-gray-700">
Category
</label>

<input
v-model="form.nama_kategori"
type="text"
class="border rounded w-full px-3 py-2 mt-1 focus:ring-2 focus:ring-blue-500"
required
/>

<div v-if="form.errors.nama_kategori" class="text-red-500 text-sm mt-1">
{{ form.errors.nama_kategori }}
</div>
</div>

<div>
<label class="text-sm font-semibold text-gray-700">
Sub Category
</label>

<input
v-model="form.nama_subkategori"
type="text"
class="border rounded w-full px-3 py-2 mt-1 focus:ring-2 focus:ring-blue-500"
required
/>

<div v-if="form.errors.nama_subkategori" class="text-red-500 text-sm mt-1">
{{ form.errors.nama_subkategori }}
</div>
</div>

<div class="md:col-span-2">
<button
type="submit"
class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
:disabled="form.processing"
>
{{ form.processing ? 'Menyimpan...' : 'Tambah Data' }}
</button>
</div>

</form>

<!-- TABEL -->
<div class="overflow-x-auto">
<table class="min-w-full border text-sm">

<thead>
<tr class="bg-gray-100">
<th class="border px-4 py-2">#</th>
<th class="border px-4 py-2">Category</th>
<th class="border px-4 py-2">Sub Category</th>
<th class="border px-4 py-2">Aksi</th>
</tr>
</thead>

<tbody>

<template v-for="(cat,index) in categories" :key="cat.id">

<tr
v-for="sub in cat.subcategories"
:key="sub.id"
class="hover:bg-gray-50"
>

<td class="border px-4 py-2">
{{ index + 1 }}
</td>

<td class="border px-4 py-2">
{{ cat.nama_kategori }}
</td>

<td class="border px-4 py-2">
{{ sub.sub_kategori }}
</td>

<td class="border px-4 py-2">
<button
@click.prevent="deleteCategory(sub.id)"
class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700"
>
Delete
</button>
</td>

</tr>

</template>

<tr v-if="categories.length === 0">
<td colspan="4" class="text-center py-6 text-gray-500">
Belum ada data
</td>
</tr>

</tbody>

</table>
</div>

</div>

</AdminLayout>
</template>