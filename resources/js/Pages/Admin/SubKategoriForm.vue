<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

const props = defineProps({
  categories: Array,
  subcategories: Array
})

const form = useForm({
  category_id: '',
  nama_subkategori: '',
  deskripsi: ''
})

function submit() {
  form.post(route('admin.subcategories.store'), {
    onSuccess: () => {
      form.reset()
    }
  })
}

function deleteSub(id) {
  if (confirm('Yakin ingin menghapus subkategori ini?')) {
    router.delete(route('admin.subcategories.destroy', id))
  }
}
</script>

<template>
  <div class="bg-white rounded shadow p-6 mb-8">
    <h3 class="text-lg font-bold mb-4">Tambah Subkategori</h3>
    <form @submit.prevent="submit" class="flex flex-col md:flex-row gap-4 items-end">
      <div class="flex-1">
        <label class="block text-xs font-semibold mb-1">Kategori</label>
        <select v-model="form.category_id" class="border rounded px-2 py-1 w-full" required>
          <option value="" disabled>Pilih Kategori</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.nama_kategori }}</option>
        </select>
      </div>
      <div class="flex-1">
        <label class="block text-xs font-semibold mb-1">Nama Subkategori</label>
        <input v-model="form.nama_subkategori" type="text" class="border rounded px-2 py-1 w-full" required />
      </div>
      <div class="flex-1">
        <label class="block text-xs font-semibold mb-1">Deskripsi</label>
        <input v-model="form.deskripsi" type="text" class="border rounded px-2 py-1 w-full" />
      </div>
      <button type="submit" class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700">Tambah</button>
    </form>
    <div v-if="form.errors.nama_subkategori" class="text-red-500 text-sm mt-1">{{ form.errors.nama_subkategori }}</div>
    <div v-if="form.errors.category_id" class="text-red-500 text-sm mt-1">{{ form.errors.category_id }}</div>
  </div>

  <div class="bg-white rounded shadow p-6">
    <h3 class="text-lg font-bold mb-4">Daftar Subkategori</h3>
    <div v-for="cat in categories" :key="cat.id" class="mb-4">
      <div class="font-semibold text-gray-700 mb-1">{{ cat.nama_kategori }}</div>
      <ul class="list-disc ml-6">
        <li v-for="sub in subcategories.filter(s => s.category_id === cat.id)" :key="sub.id" class="flex items-center gap-2">
          <span>{{ sub.nama_subkategori }}</span>
          <span class="text-xs text-gray-400">{{ sub.deskripsi }}</span>
          <button @click="deleteSub(sub.id)" class="text-red-500 hover:text-red-700 text-xs ml-2">Hapus</button>
        </li>
        <li v-if="subcategories.filter(s => s.category_id === cat.id).length === 0" class="text-gray-400 text-xs">Belum ada subkategori</li>
      </ul>
    </div>
  </div>
</template>
