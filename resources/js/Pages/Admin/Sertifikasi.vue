<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, router, usePage } from '@inertiajs/vue3'

const props = defineProps({
  certifications: Array,
})

const page = usePage()
const flash = page.props.flash || {}

function statusLabel(status) {
  if (status === 'approved') return 'Disetujui'
  if (status === 'rejected') return 'Ditolak'
  if (status === 'pending') return 'Menunggu Persetujuan'
  return 'Belum Diupload'
}

function statusClass(status) {
  if (status === 'approved') return 'bg-green-100 text-green-700 border-green-200'
  if (status === 'rejected') return 'bg-red-100 text-red-700 border-red-200'
  if (status === 'pending') return 'bg-yellow-100 text-yellow-800 border-yellow-200'
  return 'bg-gray-100 text-gray-700 border-gray-200'
}

function approveCertification(id) {
  if (!confirm('Setujui sertifikasi ini?')) return

  router.post(route('admin.sertifikasi.approve', id), {}, {
    preserveScroll: true,
  })
}

function rejectCertification(id) {
  if (!confirm('Tolak sertifikasi ini?')) return

  router.post(route('admin.sertifikasi.reject', id), {}, {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Sertifikasi Toko" />

  <AdminLayout>
    <div class="py-12">
      <div class="mx-auto max-w-7xl px-6">
        <div class="rounded-lg bg-white p-6 shadow">
          <div class="mb-6 flex items-start justify-between gap-4">
            <div>
              <h1 class="text-2xl font-semibold text-gray-900">Sertifikasi Toko</h1>
              <p class="mt-1 text-sm text-gray-600">Daftar sertifikat yang di-upload penjual untuk ditinjau admin.</p>
            </div>
          </div>

          <div v-if="flash.success" class="mb-4 rounded-lg border border-green-300 bg-green-100 px-4 py-3 text-green-800">
            {{ flash.success }}
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">#</th>
                  <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Toko</th>
                  <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Penjual</th>
                  <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Jenis</th>
                  <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">File</th>
                  <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                  <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-for="(item, index) in certifications" :key="item.id">
                  <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ index + 1 }}</td>
                  <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ item.nama_toko || '-' }}</td>
                  <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ item.name || '-' }}</td>
                  <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                    {{ item.sertifikasi_jenis === 'halal' ? 'Sertifikasi Halal' : item.sertifikasi_jenis === 'haki' ? 'Sertifikasi HAKI' : '-' }}
                  </td>
                  <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                    <a
                      :href="`/storage/${item.sertifikasi_file}`"
                      target="_blank"
                      rel="noopener noreferrer"
                      class="text-indigo-600 hover:underline"
                    >
                      Lihat File
                    </a>
                  </td>
                  <td class="whitespace-nowrap px-4 py-3 text-sm">
                    <span class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold" :class="statusClass(item.sertifikasi_status)">
                      {{ statusLabel(item.sertifikasi_status) }}
                    </span>
                  </td>
                  <td class="whitespace-nowrap px-4 py-3 text-sm font-medium">
                    <div class="flex items-center gap-2">
                      <button
                        @click="approveCertification(item.id)"
                        class="rounded-md bg-green-600 px-3 py-2 text-white hover:bg-green-700"
                      >
                        Disetujui
                      </button>
                      <button
                        @click="rejectCertification(item.id)"
                        class="rounded-md bg-red-600 px-3 py-2 text-white hover:bg-red-700"
                      >
                        Ditolak
                      </button>
                    </div>
                  </td>
                </tr>
                <tr v-if="certifications.length === 0">
                  <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada sertifikasi yang di-upload penjual.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
