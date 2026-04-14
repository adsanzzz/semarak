<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Sidebar from '@/Components/Sidebar.vue'
import { ref } from 'vue'

const showModal = ref(false)
const namaPesanan = ref('')
const namaToko = ref('')
const isiKomplain = ref('')

const komplain = ref([
  { id: 1, namaPesanan: 'Order #1234', namaToko: 'Toko A', isiKomplain: 'Barang tidak sesuai', balasan: '' }
])

function submitKomplain() {
  if (!namaPesanan.value.trim() || !namaToko.value.trim() || !isiKomplain.value.trim()) return

  komplain.value.push({
    id: komplain.value.length + 1,
    namaPesanan: namaPesanan.value,
    namaToko: namaToko.value,
    isiKomplain: isiKomplain.value,
    balasan: ''
  })

  namaPesanan.value = ''
  namaToko.value = ''
  isiKomplain.value = ''
  showModal.value = false
}
</script>

<template>
<AuthenticatedLayout>

<div class="flex">

<Sidebar class="fixed left-0 top-0 h-screen"/>

<div class="flex-1 bg-gray-100 min-h-screen p-8">

<div class="flex items-center justify-between mb-4">
  <h2 class="text-xl font-semibold">Daftar Komplain</h2>
  <button
    @click="showModal = true"
    class="bg-blue-600 text-white px-4 py-2 rounded"
  >
    + Komplain
  </button>
</div>

<table class="w-full bg-white rounded shadow border-collapse">
  <thead class="bg-gray-200">
    <tr>
      <th class="p-4 border-b border-gray-300 text-left font-semibold">Nama Pesanan</th>
      <th class="p-4 border-b border-gray-300 text-left font-semibold">Nama Toko</th>
      <th class="p-4 border-b border-gray-300 text-left font-semibold">Isi Komplain</th>
    </tr>
  </thead>
  <tbody>
    <tr v-for="item in komplain" :key="item.id" class="border-b border-gray-200 hover:bg-gray-50">
      <td class="p-4 text-gray-700">{{ item.namaPesanan }}</td>
      <td class="p-4 text-gray-700">{{ item.namaToko }}</td>
      <td class="p-4 text-gray-600">{{ item.isiKomplain }}</td>
    </tr>
  </tbody>
</table>

<!-- MODAL TAMBAH KOMPLAIN -->
<div v-if="showModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
  <div class="bg-white p-6 rounded w-96">
    <h3 class="font-bold text-lg mb-4">Tambah Komplain</h3>
    <div class="mb-4">
      <label class="block text-sm font-semibold mb-1">Nama Pesanan</label>
      <input
        v-model="namaPesanan"
        type="text"
        placeholder="Masukkan nama pesanan"
        class="w-full border p-2 rounded"
      />
    </div>
    <div class="mb-4">
      <label class="block text-sm font-semibold mb-1">Nama Toko</label>
      <input
        v-model="namaToko"
        type="text"
        placeholder="Masukkan nama toko"
        class="w-full border p-2 rounded"
      />
    </div>
    <div class="mb-4">
      <label class="block text-sm font-semibold mb-1">Isi Komplain</label>
      <textarea
        v-model="isiKomplain"
        placeholder="Tuliskan isi komplain Anda"
        class="w-full border p-2 rounded h-28 resize-none"
      ></textarea>
    </div>

    <div class="flex justify-end gap-2">
      <button @click="showModal = false" class="bg-gray-400 text-white px-4 py-2 rounded">
        Batal
      </button>
      <button @click="submitKomplain" class="bg-blue-600 text-white px-4 py-2 rounded">
        Kirim
      </button>
    </div>
  </div>
</div>

</div>
</div>

</AuthenticatedLayout>
</template>