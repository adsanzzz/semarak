<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Sidebar from '@/Components/Sidebar.vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import { computed, ref, reactive } from 'vue'

const props = defineProps({
  complaints: Array,
  forwardedComplaints: {
    type: Array,
    default: () => [],
  },
})

const page = usePage()
const flash = page.props.flash || {}
const isSeller = computed(() => Number(page.props.auth?.user?.role || 0) === 2)

const showForm = ref(false)
const form = reactive({
  name_or_store: '',
  issue_description: '',
  input: '',
})

const openForm = () => {
  showForm.value = true
}

const closeForm = () => {
  showForm.value = false
  form.name_or_store = ''
  form.issue_description = ''
  form.input = ''
}

const submitPengaduan = () => {
  if (!form.name_or_store.trim() || !form.issue_description.trim() || !form.input.trim()) {
    return
  }

  router.post(route('pengaduan.store'), form, {
    onSuccess: () => {
      closeForm()
    },
  })
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
              <p class="text-sm text-gray-500 mt-1">Buat pengaduan, komplain, atau ajukan banding jika akun toko dinonaktifkan.</p>
            </div>

            <button
              @click="openForm"
              class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
            >
              + Pengaduan & Komplain
            </button>
          </div>

          <div v-if="flash.success" class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
            {{ flash.success }}
          </div>

          <div v-if="flash.error" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            {{ flash.error }}
          </div>

          <transition name="fade">
            <div v-if="showForm" class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
              <h2 class="text-lg font-semibold text-gray-900 mb-4">Tambah Pengaduan, Komplain, atau Banding</h2>

              <div class="grid gap-4">
                <label class="block">
                  <span class="text-sm font-medium text-gray-700">Nama / Nama Toko</span>
                  <input
                    v-model="form.name_or_store"
                    type="text"
                    placeholder="Masukkan nama atau nama toko"
                    class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                  />
                </label>

                <label class="block">
                  <span class="text-sm font-medium text-gray-700">Uraian Masalah</span>
                  <textarea
                    v-model="form.issue_description"
                    placeholder="Jelaskan uraian masalah..."
                    rows="4"
                    class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 resize-none"
                  ></textarea>
                </label>

                <label class="block">
                  <span class="text-sm font-medium text-gray-700">Masukan</span>
                  <textarea
                    v-model="form.input"
                    placeholder="Tulis masukan tambahan..."
                    rows="3"
                    class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 resize-none"
                  ></textarea>
                </label>

                <div class="flex justify-end gap-3 pt-2">
                  <button
                    type="button"
                    @click="closeForm"
                    class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                  >
                    Batal
                  </button>
                  <button
                    type="button"
                    @click="submitPengaduan"
                    class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                  >
                    Simpan
                  </button>
                </div>
              </div>
            </div>
          </transition>

          <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
              <div class="flex items-center justify-between mb-5">
                <div>
                  <h2 class="text-lg font-semibold text-gray-900">
                    {{ isSeller ? 'Pengaduan & Komplain Penjual' : 'Daftar Pengaduan & Komplain' }}
                  </h2>
                  <p class="text-sm text-gray-500">
                    {{ isSeller ? 'Daftar pengaduan yang dibuat oleh penjual.' : 'Semua pengaduan, komplain, dan banding yang telah tercatat.' }}
                  </p>
                </div>
              </div>

              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama / Nama Toko</th>
                      <th class="px-4 py-3 text-left font-semibold text-gray-600">Uraian Masalah</th>
                      <th class="px-4 py-3 text-left font-semibold text-gray-600">Masukan</th>
                      <th class="px-4 py-3 text-left font-semibold text-gray-600">Balasan Admin</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="item in complaints" :key="item.id" class="hover:bg-gray-50">
                      <td class="px-4 py-4 text-gray-700">{{ item.nama_toko }}</td>
                      <td class="px-4 py-4 text-gray-700">{{ item.uraian_keluhan }}</td>
                      <td class="px-4 py-4 text-gray-700">{{ item.masukan }}</td>
                      <td class="px-4 py-4 text-gray-700">{{ item.admin_reply || '-' }}</td>
                    </tr>
                    <tr v-if="complaints.length === 0">
                      <td colspan="4" class="px-4 py-6 text-center text-gray-500">Belum ada pengaduan atau komplain.</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div v-if="isSeller" class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
              <div class="flex items-center justify-between mb-5">
                <div>
                  <h2 class="text-lg font-semibold text-gray-900">Terusan Pengaduan dari Admin</h2>
                  <p class="text-sm text-gray-500">Pengaduan dari pembeli yang diteruskan admin ke penjual.</p>
                </div>
              </div>

              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama / Nama Toko</th>
                      <th class="px-4 py-3 text-left font-semibold text-gray-600">Uraian Masalah</th>
                      <th class="px-4 py-3 text-left font-semibold text-gray-600">Masukan</th>
                      <th class="px-4 py-3 text-left font-semibold text-gray-600">Balasan Admin</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="item in forwardedComplaints" :key="item.id" class="hover:bg-gray-50">
                      <td class="px-4 py-4 text-gray-700">{{ item.nama_toko }}</td>
                      <td class="px-4 py-4 text-gray-700">{{ item.uraian_keluhan }}</td>
                      <td class="px-4 py-4 text-gray-700">{{ item.masukan }}</td>
                      <td class="px-4 py-4 text-gray-700">{{ item.admin_reply || '-' }}</td>
                    </tr>
                    <tr v-if="forwardedComplaints.length === 0">
                      <td colspan="4" class="px-4 py-6 text-center text-gray-500">Belum ada terusan pengaduan dari admin.</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
