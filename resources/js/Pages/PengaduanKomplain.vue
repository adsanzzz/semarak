<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Sidebar from '@/Components/Sidebar.vue'
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'

const showForm = ref(false)
const namaToko = ref('')
const uraianKeluhan = ref('')
const masukan = ref('')

const pengaduanList = ref([
  {
    id: 1,
    namaToko: 'Toko Semarak Jaya',
    uraianKeluhan: 'Pesanan terlambat datang',
    masukan: 'Mohon perbaiki estimasi pengiriman agar lebih akurat.'
  }
])

const submitPengaduan = () => {
  if (!namaToko.value.trim() || !uraianKeluhan.value.trim() || !masukan.value.trim()) {
    return
  }

  pengaduanList.value.push({
    id: pengaduanList.value.length + 1,
    namaToko: namaToko.value,
    uraianKeluhan: uraianKeluhan.value,
    masukan: masukan.value
  })

  namaToko.value = ''
  uraianKeluhan.value = ''
  masukan.value = ''
  showForm.value = false
}
</script>

<template>
  <Head title="Pengaduan & Komplain" />

  <AuthenticatedLayout>
    <div class="flex">
      <Sidebar class="fixed left-0 top-0 h-screen" />

      <div class="flex-1 bg-gray-100 min-h-screen p-8">
        <div class="max-w-7xl mx-auto space-y-6">
          <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
              <h1 class="text-2xl font-semibold text-gray-900">Pengaduan & Komplain</h1>
              <p class="text-sm text-gray-500 mt-1">Buat pengaduan atau komplain baru, lalu lihat daftarnya di bawah.</p>
            </div>

            <button
              @click="showForm = !showForm"
              class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
            >
              + Pengaduan & Komplain
            </button>
          </div>

          <transition name="fade">
            <div v-if="showForm" class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
              <h2 class="text-lg font-semibold text-gray-900 mb-4">Tambah Pengaduan & Komplain</h2>

              <div class="grid gap-4">
                <label class="block">
                  <span class="text-sm font-medium text-gray-700">Nama / Nama Toko</span>
                  <input
                    v-model="namaToko"
                    type="text"
                    placeholder="Masukkan nama atau nama toko"
                    class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                  />
                </label>

                <label class="block">
                  <span class="text-sm font-medium text-gray-700">Uraian Keluhan</span>
                  <textarea
                    v-model="uraianKeluhan"
                    placeholder="Jelaskan keluhan Anda..."
                    rows="4"
                    class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 resize-none"
                  ></textarea>
                </label>

                <label class="block">
                  <span class="text-sm font-medium text-gray-700">Masukan</span>
                  <textarea
                    v-model="masukan"
                    placeholder="Tulis masukan tambahan..."
                    rows="3"
                    class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 resize-none"
                  ></textarea>
                </label>

                <div class="flex justify-end gap-3 pt-2">
                  <button
                    @click="showForm = false"
                    class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                  >
                    Batal
                  </button>
                  <button
                    @click="submitPengaduan"
                    class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                  >
                    Simpan
                  </button>
                </div>
              </div>
            </div>
          </transition>

          <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5">
              <div>
                <h2 class="text-lg font-semibold text-gray-900">Daftar Pengaduan & Komplain</h2>
                <p class="text-sm text-gray-500">Semua pengaduan dan komplain yang telah tercatat.</p>
              </div>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama / Nama Toko</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Uraian Keluhan</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Masukan</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                  <tr v-for="item in pengaduanList" :key="item.id" class="hover:bg-gray-50">
                    <td class="px-4 py-4 text-gray-700">{{ item.namaToko }}</td>
                    <td class="px-4 py-4 text-gray-700">{{ item.uraianKeluhan }}</td>
                    <td class="px-4 py-4 text-gray-700">{{ item.masukan }}</td>
                  </tr>
                  <tr v-if="pengaduanList.length === 0">
                    <td colspan="3" class="px-4 py-6 text-center text-gray-500">Belum ada pengaduan atau komplain.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
