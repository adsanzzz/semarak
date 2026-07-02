<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, router, Link } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    reports: Array,
})

function deleteUser(id) {
    if (confirm('Yakin ingin menghapus user ini?')) {
        router.delete(route('admin.users.destroy', id))
    }
}

const deactivateReasons = [
    'Melanggar Ketentuan Layanan',
    'Dilaporkan Oleh Banyak Pengguna',
    'Menjual Barang Palsu/Mencurigakan',
    'Lainnya',
]

const deactivateModalOpen = ref(false)
const selectedSeller = ref(null)
const selectedReason = ref('')
const customReason = ref('')

function openDeactivateModal(user) {
    selectedSeller.value = user
    selectedReason.value = deactivateReasons[0]
    customReason.value = ''
    deactivateModalOpen.value = true
}

function closeDeactivateModal() {
    deactivateModalOpen.value = false
    selectedSeller.value = null
    selectedReason.value = ''
    customReason.value = ''
}

function confirmDeactivate() {
    const reasonText = selectedReason.value === 'Lainnya'
        ? customReason.value.trim()
        : selectedReason.value

    if (!selectedSeller.value || !reasonText) {
        return
    }

    router.post(route('admin.users.deactivate', selectedSeller.value.id), {
        reason: reasonText,
    }, {
        onSuccess: () => {
            closeDeactivateModal()
            // reload data
            router.reload()
        },
    })
}

function activateSeller(id) {
    if (confirm('Yakin ingin mengaktifkan kembali akun penjual ini?')) {
        router.post(route('admin.users.activate', id), {}, {
            onSuccess: () => {
                router.reload()
            }
        })
    }
}

function getRoleLabel(role) {
    if (Number(role) === 1) return 'Admin'
    if (Number(role) === 2) return 'Penjual'
    if (Number(role) === 3) return 'Pembeli'
    return 'User'
}
</script>

<template>
  <Head title="Laporan Akun - Admin" />

  <AdminLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h1 class="text-2xl font-bold text-[#0A3551]">Laporan Akun Bermasalah</h1>
            </div>

            <p class="text-gray-600 mb-6 text-sm">Berikut adalah daftar akun pengguna (penjual/pembeli) yang dilaporkan oleh pengguna lain beserta bukti pelanggaran.</p>

            <div class="overflow-x-auto rounded-xl border border-gray-200">
              <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pelapor</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Akun Dilaporkan</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Peran</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Alasan Laporan</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Deskripsi</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Bukti</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status Akun</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                  <tr v-for="report in reports" :key="report.id" class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4 whitespace-nowrap font-medium">{{ report.reporter_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex flex-col">
                        <span class="font-bold text-gray-900">{{ report.reported_name }}</span>
                        <span v-if="report.reported_nama_toko && report.reported_nama_toko !== '-'" class="text-xs text-indigo-600 font-semibold">{{ report.reported_nama_toko }}</span>
                        <span class="text-xs text-gray-400 font-medium">{{ report.reported_email }}</span>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold bg-gray-100 text-gray-800">
                        {{ getRoleLabel(report.reported_role) }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900 font-semibold">{{ report.reason }}</td>
                    <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate" :title="report.description">{{ report.description }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <a v-if="report.bukti" :href="report.bukti" target="_blank" class="text-xs text-blue-600 hover:underline font-bold flex items-center gap-1 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Lihat Bukti
                      </a>
                      <span v-else class="text-xs text-gray-400">-</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400">{{ report.created_at }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span v-if="report.reported_is_active" class="inline-flex rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-bold text-emerald-700">Aktif</span>
                      <span v-else class="inline-flex rounded-full bg-red-50 border border-red-200 px-2.5 py-0.5 text-xs font-bold text-red-700">Non-aktif</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs">
                      <div class="flex gap-2">
                        <!-- Deactivate/Activate only for Sellers (Role 2) -->
                        <template v-if="Number(report.reported_role) === 2">
                          <button
                            v-if="report.reported_is_active"
                            @click="openDeactivateModal({ id: report.reported_user_id, name: report.reported_name })"
                            class="rounded-lg bg-yellow-600 hover:bg-yellow-700 text-white px-2.5 py-1.5 font-bold transition shadow-sm"
                          >
                            Nonaktifkan Toko
                          </button>
                          <button
                            v-else
                            @click="activateSeller(report.reported_user_id)"
                            class="rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white px-2.5 py-1.5 font-bold transition shadow-sm"
                          >
                            Aktifkan Toko
                          </button>
                        </template>
                        <button
                          @click="deleteUser(report.reported_user_id)"
                          class="rounded-lg bg-red-600 hover:bg-red-700 text-white px-2.5 py-1.5 font-bold transition shadow-sm"
                        >
                          Hapus Akun
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="reports.length === 0">
                    <td colspan="9" class="px-6 py-12 text-center text-gray-400">Belum ada laporan akun masuk.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Nonaktifkan Akun Penjual -->
    <transition name="fade">
      <div v-if="deactivateModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-gray-100 transition-all duration-300 space-y-4">
          <div class="flex items-center gap-3 text-red-600 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 animate-pulse">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286zm0 13.036h.008v.008H12v-.008z" />
            </svg>
            <h3 class="text-lg font-bold text-gray-800">Nonaktifkan Akun Penjual</h3>
          </div>
          <div class="space-y-3">
            <p class="text-xs text-gray-500">Pilih alasan penonaktifan untuk akun penjual <strong>{{ selectedSeller?.name }}</strong>.</p>
            <div>
              <label class="mb-1 block text-xs font-bold text-gray-700 font-sans">Alasan Penonaktifan <span class="text-red-500">*</span></label>
              <select 
                v-model="selectedReason" 
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition font-medium text-gray-700 bg-white"
              >
                <option v-for="reason in deactivateReasons" :key="reason" :value="reason">
                  {{ reason }}
                </option>
              </select>
            </div>
            <div v-if="selectedReason === 'Lainnya'">
              <label class="mb-1 block text-xs font-bold text-gray-700">Tulis Alasan Kustom <span class="text-red-500">*</span></label>
              <textarea 
                v-model="customReason" 
                required
                placeholder="Masukkan alasan detail..." 
                class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm h-20 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition text-gray-700 bg-white"
              ></textarea>
            </div>
          </div>
          <div class="flex justify-end gap-3 pt-2">
            <button @click="closeDeactivateModal" class="rounded-xl bg-gray-100 hover:bg-gray-200 px-5 py-2.5 text-xs font-bold text-gray-700 transition cursor-pointer">
              Batal
            </button>
            <button @click="confirmDeactivate" class="rounded-xl bg-red-600 hover:bg-red-700 px-5 py-2.5 text-xs font-bold text-white transition cursor-pointer">
              Nonaktifkan
            </button>
          </div>
        </div>
      </div>
    </transition>
  </AdminLayout>
</template>
