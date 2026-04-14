<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
  complaints: Array,
})
</script>

<template>
  <Head title="Kelola Pengaduan Komplain" />

  <AdminLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <h1 class="text-2xl font-semibold mb-4">Kelola Pengaduan Komplain</h1>
            <p class="text-gray-600 mb-6">Histori pengaduan dan komplain dari pembeli dan toko.</p>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama / Nama Toko</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uraian Keluhan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Masukan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="complaint in complaints" :key="complaint.id">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ complaint.namaToko }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ complaint.uraianKeluhan }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ complaint.masukan }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ complaint.created_at ? new Date(complaint.created_at).toLocaleDateString('id-ID') : '-' }}</td>
                  </tr>
                  <tr v-if="complaints.length === 0">
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada pengaduan atau komplain.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
