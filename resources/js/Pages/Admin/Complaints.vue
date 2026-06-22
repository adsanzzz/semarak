<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  buyerComplaints: Array,
  sellerComplaints: Array,
  sellers: Array,
})

const page = usePage()
const flash = ref(page.props.flash || {})
const forwardModalOpen = ref(false)
const selectedComplaint = ref(null)
const selectedSellerId = ref(null)
const replyModalOpen = ref(false)
const replyText = ref('')

function openForwardModal(complaint) {
  selectedComplaint.value = complaint
  selectedSellerId.value = props.sellers.length > 0 ? props.sellers[0].id : null
  forwardModalOpen.value = true
}

function closeForwardModal() {
  forwardModalOpen.value = false
  selectedComplaint.value = null
  selectedSellerId.value = null
}

function forwardComplaint() {
  if (!selectedComplaint.value || !selectedSellerId.value) {
    return
  }

  router.post(route('admin.komplain.forward', selectedComplaint.value.id), {
    seller_id: selectedSellerId.value,
  }, {
    onSuccess: () => {
      closeForwardModal()
    },
  })
}

function complaintSourceLabel(complaint) {
  if (complaint.forwarded_from_id) {
    return 'Terusan dari pembeli'
  }

  return complaint.complaint_group === 'seller' ? 'Penjual' : 'Pembeli'
}

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
</script>

<template>
  <Head title="Kelola Pengaduan Komplain" />

  <AdminLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h1 class="text-2xl font-semibold">Kelola Pengaduan Komplain</h1>
            </div>

            <div v-if="flash.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
              {{ flash.success }}
            </div>

            <p class="text-gray-600 mb-6">Histori pengaduan dan komplain dipisah berdasarkan penjual dan pembeli.</p>

            <div class="space-y-10">
              <div>
                <div class="mb-4 flex items-center justify-between">
                  <div>
                    <h2 class="text-lg font-semibold text-gray-900">Tabel Pembeli</h2>
                    <p class="text-sm text-gray-500">Komplain dari pembeli dan yang bisa diteruskan ke penjual.</p>
                  </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-200">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama / Nama Toko</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uraian</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Masukan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <tr v-for="complaint in buyerComplaints" :key="complaint.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ complaint.id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ complaint.nama_toko || (complaint.user ? complaint.user.name : 'N/A') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ complaint.uraian_keluhan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ complaint.masukan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          <span v-if="complaint.is_forwarded" class="inline-flex rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700">Diteruskan</span>
                          <span v-else class="inline-flex rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-700">Belum diteruskan</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          <button
                            v-if="!complaint.is_forwarded"
                            @click="openForwardModal(complaint)"
                            class="rounded-lg bg-blue-600 px-3 py-2 text-white hover:bg-blue-700"
                          >
                            Teruskan ke penjual
                          </button>
                          <span v-else class="text-gray-400">-</span>
                        </td>
                      </tr>
                      <tr v-if="buyerComplaints.length === 0">
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada komplain pembeli.</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <div>
                <div class="mb-4 flex items-center justify-between">
                  <div>
                    <h2 class="text-lg font-semibold text-gray-900">Tabel Penjual</h2>
                    <p class="text-sm text-gray-500">Komplain yang dibuat langsung oleh penjual.</p>
                  </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-200">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sumber</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama / Nama Toko</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uraian</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Masukan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balasan Admin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <tr v-for="complaint in sellerComplaints" :key="complaint.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ complaint.id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ complaintSourceLabel(complaint) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ complaint.nama_toko || (complaint.user ? complaint.user.name : 'N/A') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ complaint.uraian_keluhan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ complaint.masukan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ complaint.admin_reply || '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ complaint.created_at ? new Date(complaint.created_at).toLocaleDateString('id-ID') : '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          <button
                            @click="openReplyModal(complaint)"
                            class="rounded-lg bg-indigo-600 px-3 py-2 text-white hover:bg-indigo-700"
                          >
                            {{ complaint.admin_reply ? 'Edit Balasan' : 'Balas' }}
                          </button>
                        </td>
                      </tr>
                      <tr v-if="sellerComplaints.length === 0">
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">Belum ada komplain penjual.</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="forwardModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
      <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
        <h3 class="text-lg font-semibold text-gray-900">Pilih toko tujuan</h3>
        <p class="mt-1 text-sm text-gray-500">
          Pilih toko penjual yang akan menerima terusan komplain dari pembeli.
        </p>

        <div class="mt-5">
          <label class="mb-2 block text-sm font-medium text-gray-700">Toko penjual</label>
          <select
            v-model="selectedSellerId"
            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
          >
            <option
              v-for="seller in sellers"
              :key="seller.id"
              :value="seller.id"
            >
              {{ seller.nama_toko || seller.name }}
            </option>
          </select>
        </div>

        <p v-if="sellers.length === 0" class="mt-3 text-sm text-red-600">
          Tidak ada akun penjual yang tersedia.
        </p>

        <div class="mt-6 flex justify-end gap-3">
          <button
            type="button"
            @click="closeForwardModal"
            class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
          >
            Batal
          </button>
          <button
            type="button"
            :disabled="sellers.length === 0"
            @click="forwardComplaint"
            class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-gray-400"
          >
            Teruskan
          </button>
        </div>
      </div>
    </div>

    <div v-if="replyModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
      <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
        <h3 class="text-lg font-semibold text-gray-900">Balasan Admin untuk Penjual</h3>
        <p class="mt-1 text-sm text-gray-500">
          Tulis balasan admin yang akan terlihat di tabel pengaduan komplain penjual.
        </p>

        <div class="mt-5">
          <label class="mb-2 block text-sm font-medium text-gray-700">Balasan Admin</label>
          <textarea
            v-model="replyText"
            rows="5"
            placeholder="Tulis balasan admin"
            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100 resize-none"
          ></textarea>
        </div>

        <div class="mt-6 flex justify-end gap-3">
          <button
            type="button"
            @click="closeReplyModal"
            class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
          >
            Batal
          </button>
          <button
            type="button"
            @click="submitReply"
            class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
          >
            Simpan Balasan
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
