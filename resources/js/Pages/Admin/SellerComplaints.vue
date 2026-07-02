<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  sellerComplaints: Array,
})

const page = usePage()
const flash = ref(page.props.flash || {})
const selectedComplaint = ref(null)
const replyModalOpen = ref(false)
const replyText = ref('')

function openReplyModal(complaint) {
  selectedComplaint.value = complaint
  replyText.value = complaint.admin_reply || ''
  replyModalOpen.value = true
}

function closeReplyModal() {
  replyModalOpen.value = false
  selectedComplaint.value = null
  replyText.value = ''
}

function submitReply() {
  if (!selectedComplaint.value || !replyText.value.trim()) {
    return
  }

  router.post(route('admin.komplain.reply', selectedComplaint.value.id), {
    admin_reply: replyText.value.trim(),
  }, {
    onSuccess: () => {
      closeReplyModal()
    },
  })
}

function complaintSourceLabel(complaint) {
  if (complaint.forwarded_from_id) {
    return 'Terusan dari pembeli'
  }
  return 'Penjual'
}
</script>

<template>
  <Head title="Kelola Komplain Penjual - Admin" />

  <AdminLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h1 class="text-2xl font-bold text-[#0A3551]">Kelola Komplain Penjual</h1>
            </div>

            <div v-if="flash.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
              {{ flash.success }}
            </div>

            <p class="text-gray-600 mb-6 text-sm">Berikut adalah daftar pengaduan dan komplain langsung dari pihak Penjual/Toko yang masuk ke sistem.</p>

            <div class="overflow-x-auto rounded-xl border border-gray-200">
              <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Sumber</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama / Nama Toko</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Uraian Keluhan</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Masukan</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Balasan Admin</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                  <tr v-for="complaint in sellerComplaints" :key="complaint.id" class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-gray-400 font-bold">#{{ complaint.id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">{{ complaintSourceLabel(complaint) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap font-bold">{{ complaint.nama_toko || (complaint.user ? complaint.user.name : 'N/A') }}</td>
                    <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate" :title="complaint.uraian_keluhan">{{ complaint.uraian_keluhan }}</td>
                    <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate" :title="complaint.masukan">{{ complaint.masukan || '-' }}</td>
                    <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate" :title="complaint.admin_reply">{{ complaint.admin_reply || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400">{{ complaint.created_at ? new Date(complaint.created_at).toLocaleDateString('id-ID') : '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <button
                        @click="openReplyModal(complaint)"
                        class="rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 text-xs font-bold transition shadow-sm"
                      >
                        {{ complaint.admin_reply ? 'Edit Balasan' : 'Balas' }}
                      </button>
                    </td>
                  </tr>
                  <tr v-if="sellerComplaints.length === 0">
                    <td colspan="8" class="px-6 py-12 text-center text-gray-400">Belum ada komplain penjual.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Balas Komplain Penjual -->
    <transition name="fade">
      <div v-if="replyModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-gray-100 transition-all duration-300 space-y-4">
          <div class="flex items-center gap-3 text-indigo-600 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
            </svg>
            <h3 class="text-lg font-bold text-gray-800">Kirim Balasan Komplain</h3>
          </div>
          <div class="space-y-3">
            <p class="text-xs text-gray-500">Tulis tanggapan atau instruksi penyelesaian untuk laporan pengaduan penjual ini.</p>
            <div>
              <label class="mb-1 block text-xs font-bold text-gray-700">Tanggapan Admin <span class="text-red-500">*</span></label>
              <textarea 
                v-model="replyText" 
                required
                placeholder="Tulis balasan di sini..." 
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm h-28 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-gray-700 bg-white"
              ></textarea>
            </div>
          </div>
          <div class="flex justify-end gap-3 pt-2">
            <button @click="closeReplyModal" class="rounded-xl bg-gray-100 hover:bg-gray-200 px-5 py-2.5 text-xs font-bold text-gray-700 transition cursor-pointer">
              Batal
            </button>
            <button @click="submitReply" class="rounded-xl bg-indigo-600 hover:bg-indigo-700 px-5 py-2.5 text-xs font-bold text-white transition cursor-pointer">
              Kirim Balasan
            </button>
          </div>
        </div>
      </div>
    </transition>
  </AdminLayout>
</template>
