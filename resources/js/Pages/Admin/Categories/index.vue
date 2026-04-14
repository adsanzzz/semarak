<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'

const props = defineProps({
    categories: {
        type: Array,
        default: () => []
    }
})

const notif = ref({
    show: false,
    type: 'success',
    message: ''
})

const form = useForm({
  nama_kategori: '',
  nama_subkategori: '',
})

const submit = () => {
    form.post('/admin/categories', {
        onSuccess: () => {
            form.reset()
            notif.value = {
                show: true,
                type: 'success',
                message: 'Data berhasil ditambahkan'
            }
        }
    })
}

const deleteSubCategory = (id) => {
    if (confirm('Yakin hapus sub kategori?')) {
        router.delete(`/admin/sub-categories/${id}`)
    }
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
type="text"
v-model="form.nama_kategori"
placeholder="Masukkan Category"
class="border rounded w-full px-3 py-2 mt-1"
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
type="text"
v-model="form.nama_subkategori"
placeholder="Masukkan Sub Category"
class="border rounded w-full px-3 py-2 mt-1"
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

<tr
v-for="(sub, index) in props.categories.flatMap(cat =>
    cat.sub_categories.map(sub => ({
        ...sub,
        nama_kategori: cat.nama_kategori
    }))
)"
:key="sub.id"
class="hover:bg-gray-50"
>

<td class="border px-4 py-2">
{{ index + 1 }}
</td>

<td class="border px-4 py-2">
{{ sub.nama_kategori }}
</td>

<td class="border px-4 py-2">
{{ sub.nama_subkategori }}
</td>

<td class="border px-4 py-2 space-x-2">
<button
@click.prevent="deleteSubCategory(sub.id)"
class="bg-red-600 text-white px-2 py-1 rounded"
>
Delete
</button>
</td>

</tr>

<tr v-if="props.categories.length === 0">
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