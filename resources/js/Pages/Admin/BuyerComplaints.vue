<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  buyerComplaints: Array,
  sellers: Array,
})

const page = usePage()
const flash = ref(page.props.flash || {})
const forwardModalOpen = ref(false)
const selectedComplaint = ref(null)
const selectedSellerId = ref(null)

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
</script>

<template>
  <Head title="Kelola Komplain Pembeli - Admin" />

  <AdminLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h1 class="text-2xl font-bold text-[#0A3551]">Kelola Komplain Pembeli</h1>
            </div>

            <div v-if="flash.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
              {{ flash.success }}
            </div>

            <p class="text-gray-600 mb-6 text-sm">Berikut adalah daftar pengaduan dan komplain dari pihak Pembeli yang masuk ke sistem.</p>

            <div class="overflow-x-auto rounded-xl border border-gray-200">
              <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Pembeli</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Toko Terlapor</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Uraian Keluhan</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Masukan</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                  <tr v-for="complaint in buyerComplaints" :key="complaint.id" class="hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-gray-400 font-bold">#{{ complaint.id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap font-bold">{{ complaint.user ? complaint.user.name : complaint.sender_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">{{ complaint.complained_store }}</td>
                    <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate" :title="complaint.uraian_keluhan">{{ complaint.uraian_keluhan }}</td>
                    <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate" :title="complaint.masukan">{{ complaint.masukan || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span v-if="complaint.is_forwarded" class="inline-flex rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-bold text-emerald-700">Diteruskan</span>
                      <span v-else class="inline-flex rounded-full bg-amber-50 border border-amber-200 px-2.5 py-0.5 text-xs font-bold text-amber-700">Belum diteruskan</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <button
                        v-if="!complaint.is_forwarded"
                        @click="openForwardModal(complaint)"
                        class="rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 text-xs font-bold transition shadow-sm"
                      >
                        Teruskan ke penjual
                      </button>
                      <span v-else class="text-gray-400 font-medium text-xs">Selesai</span>
                    </td>
                  </tr>
                  <tr v-if="buyerComplaints.length === 0">
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">Belum ada komplain pembeli.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Teruskan ke Penjual -->
    <transition name="fade">
      <div v-if="forwardModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-gray-100 transition-all duration-300 space-y-4">
          <div class="flex items-center gap-3 text-indigo-600 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 animate-bounce">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
            </svg>
            <h3 class="text-lg font-bold text-gray-800">Teruskan Komplain</h3>
          </div>
          <div class="space-y-3">
            <p class="text-xs text-gray-500">Pilih Toko/Penjual tujuan untuk meneruskan laporan pengaduan ini agar dapat mereka tindak lanjuti.</p>
            <div>
              <label class="mb-1 block text-xs font-bold text-gray-700">Toko Penjual <span class="text-red-500">*</span></label>
              <select 
                v-model="selectedSellerId" 
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition font-medium text-gray-700 bg-white"
              >
                <option v-for="seller in sellers" :key="seller.id" :value="seller.id">
                  {{ seller.nama_toko || seller.name }}
                </option>
              </select>
            </div>
          </div>
          <div class="flex justify-end gap-3 pt-2">
            <button @click="closeForwardModal" class="rounded-xl bg-gray-100 hover:bg-gray-200 px-5 py-2.5 text-xs font-bold text-gray-700 transition cursor-pointer">
              Batal
            </button>
            <button @click="forwardComplaint" class="rounded-xl bg-indigo-600 hover:bg-indigo-700 px-5 py-2.5 text-xs font-bold text-white transition cursor-pointer">
              Teruskan
            </button>
          </div>
        </div>
      </div>
    </transition>
  </AdminLayout>
</template>
