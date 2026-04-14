<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Sidebar from '@/Components/Sidebar.vue'
import { ref } from 'vue'

const showModal = ref(false)

const pengaduanList = ref([
  { id: 1, judul: 'Barang rusak', isi: 'Pecah saat sampai' }
])
</script>

<template>
<AuthenticatedLayout>

<div class="flex">

<Sidebar class="fixed left-0 top-0 h-screen"/>

<div class="flex-1 bg-gray-100 min-h-screen p-8">

<h2 class="text-xl font-semibold mb-4">Daftar Pengaduan</h2>

<button
@click="showModal = true"
class="bg-blue-600 text-white px-4 py-2 rounded mb-4"
>
+ Pengaduan
</button>

<!-- MODAL -->
<div v-if="showModal" class="fixed inset-0 bg-black/40 flex items-center justify-center">
  <div class="bg-white p-6 rounded w-96">
    <h3 class="font-bold mb-2">Tambah Pengaduan</h3>
    <input placeholder="Judul" class="w-full border p-2 mb-2"/>
    <textarea placeholder="Isi" class="w-full border p-2 mb-2"></textarea>

    <button class="bg-green-500 text-white px-3 py-1 mr-2 rounded">Kirim</button>
    <button @click="showModal=false" class="bg-gray-400 px-3 py-1 rounded">Batal</button>
  </div>
</div>

<!-- TABEL -->
<table class="w-full bg-white rounded shadow">
  <thead class="bg-gray-200">
    <tr>
      <th class="p-2">Judul</th>
      <th class="p-2">Isi</th>
    </tr>
  </thead>
  <tbody>
    <tr v-for="item in pengaduanList" :key="item.id">
      <td class="p-2">{{ item.judul }}</td>
      <td class="p-2">{{ item.isi }}</td>
    </tr>
  </tbody>
</table>

</div>
</div>

</AuthenticatedLayout>
</template>