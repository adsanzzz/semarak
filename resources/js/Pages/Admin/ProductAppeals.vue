<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  appeals: Array
})

const showReplyModal = ref(false)
const selectedAppeal = ref(null)

const replyForm = useForm({
  reply: '',
  decision: 'approve'
})

function openReplyModal(appeal) {
  selectedAppeal.value = appeal
  replyForm.reply = appeal.admin_reply || ''
  replyForm.decision = appeal.status === 'rejected' ? 'reject' : 'approve'
  replyForm.clearErrors()
  showReplyModal.value = true
}

function submitReply() {
  replyForm.post(route('admin.appeals.reply', selectedAppeal.value.id), {
    onSuccess: () => {
      showReplyModal.value = false
      replyForm.reset()
      router.reload({ only: ['appeals'] })
    }
  })
}
</script>

<template>
  <Head title="Pengajuan Banding Produk - Admin" />

  <AdminLayout>
    <div class="space-y-6">
      
      <!-- Header -->
      <div>
        <h2 class="text-2xl font-extrabold text-[#0A3551] tracking-tight">
          Pengajuan Banding Produk
        </h2>
        <p class="text-sm text-gray-500 mt-1">
          Daftar pengajuan banding dari toko atas produk yang telah dinonaktifkan oleh sistem atau moderator.
        </p>
      </div>

      <!-- Appeals Table -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50/75">
              <tr class="text-left text-gray-500 font-bold text-xs uppercase tracking-wider">
                <th class="px-6 py-4 w-16 text-center">ID</th>
                <th class="px-6 py-4">Toko</th>
                <th class="px-6 py-4">Produk</th>
                <th class="px-6 py-4">Alasan Dinonaktifkan</th>
                <th class="px-6 py-4">Alasan Banding</th>
                <th class="px-6 py-4 w-40">Bukti Pendukung</th>
                <th class="px-6 py-4 w-32 text-center">Status</th>
                <th class="px-6 py-4 w-28 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
              <tr 
                v-for="appeal in appeals" 
                :key="appeal.id"
                class="hover:bg-gray-50/50 transition animate-fade-in"
              >
                <!-- ID -->
                <td class="px-6 py-4 whitespace-nowrap text-center text-gray-400 font-mono text-xs">
                  #{{ appeal.id }}
                </td>

                <!-- Toko -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="font-bold text-gray-800">{{ appeal.toko_name }}</div>
                </td>

                <!-- Produk -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="font-medium text-gray-700">{{ appeal.product_name }}</div>
                  <div class="text-[10px] text-gray-400 font-mono mt-0.5">ID: #{{ appeal.product_id }}</div>
                </td>

                <!-- Alasan Dinonaktifkan -->
                <td class="px-6 py-4 max-w-xs">
                  <p class="text-xs text-amber-700 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-100/50 line-clamp-2" :title="appeal.alasan_dinonaktifkan">
                    {{ appeal.alasan_dinonaktifkan }}
                  </p>
                </td>

                <!-- Alasan Banding -->
                <td class="px-6 py-4 max-w-xs">
                  <p class="text-xs text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100 line-clamp-2" :title="appeal.alasan_banding">
                    {{ appeal.alasan_banding }}
                  </p>
                </td>

                <!-- Bukti Pendukung -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <a 
                    v-if="appeal.bukti_pendukung"
                    :href="appeal.bukti_pendukung" 
                    target="_blank"
                    class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 font-bold hover:underline cursor-pointer"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    Lihat Berkas
                  </a>
                  <span v-else class="text-xs text-gray-400">-</span>
                </td>

                <!-- Status -->
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span 
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border"
                    :class="appeal.admin_reply 
                      ? 'bg-emerald-50 text-emerald-700 border-emerald-100' 
                      : 'bg-amber-50 text-amber-700 border-amber-100 animate-pulse'"
                  >
                    {{ appeal.admin_reply ? 'Selesai' : 'Pending' }}
                  </span>
                </td>

                <!-- Aksi -->
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <button 
                    @click="openReplyModal(appeal)"
                    class="inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-3 py-1.5 rounded-lg text-xs transition cursor-pointer"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8Z" />
                    </svg>
                    {{ appeal.admin_reply ? 'Edit Balasan' : 'Balas' }}
                  </button>
                </td>
              </tr>

              <tr v-if="appeals.length === 0">
                <td colspan="8" class="py-16 text-center text-gray-400">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto text-gray-300 mb-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z" />
                  </svg>
                  Belum ada pengajuan banding produk dari toko saat ini.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- ⚖️ MODAL BALAS BANDING -->
    <transition name="fade">
      <div v-if="showReplyModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-gray-100 transition-all duration-300 space-y-4">
          
          <div class="flex items-center gap-3 text-indigo-600 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8Z" />
            </svg>
            <h3 class="text-lg font-bold text-gray-800">Tanggapi Banding</h3>
          </div>

          <!-- Info Ringkas -->
          <div class="text-xs text-gray-500 bg-gray-50 p-3 rounded-lg border border-gray-100 space-y-1">
            <p><strong>Toko:</strong> {{ selectedAppeal?.toko_name }}</p>
            <p><strong>Produk:</strong> {{ selectedAppeal?.product_name }}</p>
            <p><strong>Alasan Banding:</strong> "{{ selectedAppeal?.alasan_banding }}"</p>
          </div>

          <form @submit.prevent="submitReply" class="space-y-4">
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Tanggapan Admin <span class="text-red-500">*</span></label>
              <textarea 
                v-model="replyForm.reply" 
                required
                placeholder="Tulis balasan atau keputusan banding..." 
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm h-28 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
              ></textarea>
              <span v-if="replyForm.errors.reply" class="text-xs text-red-600 mt-1 block">{{ replyForm.errors.reply }}</span>
            </div>

            <!-- Keputusan Banding Radio -->
            <div class="space-y-1.5">
              <label class="block text-sm font-semibold text-gray-700">Keputusan Banding <span class="text-red-500">*</span></label>
              <div class="grid grid-cols-2 gap-3">
                <label 
                  class="flex items-center gap-2 px-4 py-3 rounded-xl border-2 cursor-pointer transition select-none text-center justify-center font-bold text-xs"
                  :class="replyForm.decision === 'approve' 
                    ? 'border-emerald-500 bg-emerald-50 text-emerald-800' 
                    : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50'"
                >
                  <input 
                    type="radio" 
                    v-model="replyForm.decision" 
                    value="approve" 
                    class="sr-only"
                  />
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-emerald-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                  </svg>
                  Setujui & Aktifkan
                </label>
                <label 
                  class="flex items-center gap-2 px-4 py-3 rounded-xl border-2 cursor-pointer transition select-none text-center justify-center font-bold text-xs"
                  :class="replyForm.decision === 'reject' 
                    ? 'border-rose-500 bg-rose-50 text-rose-800' 
                    : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50'"
                >
                  <input 
                    type="radio" 
                    v-model="replyForm.decision" 
                    value="reject" 
                    class="sr-only"
                  />
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-rose-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                  </svg>
                  Tolak Banding
                </label>
              </div>
            </div>

            <!-- Footer Buttons -->
            <div class="flex justify-end gap-3 pt-2">
              <button 
                type="button" 
                @click="showReplyModal = false" 
                class="rounded-xl bg-gray-100 hover:bg-gray-200 px-5 py-2.5 text-sm font-bold text-gray-700 transition cursor-pointer"
              >
                Batal
              </button>
              <button 
                type="submit" 
                :disabled="replyForm.processing"
                class="rounded-xl bg-indigo-600 hover:bg-indigo-700 px-5 py-2.5 text-sm font-bold text-white transition cursor-pointer disabled:opacity-50"
              >
                {{ replyForm.processing ? 'Mengirim...' : 'Kirim Balasan' }}
              </button>
            </div>
          </form>

        </div>
      </div>
    </transition>
  </AdminLayout>
</template>
