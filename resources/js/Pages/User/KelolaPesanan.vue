<script setup>
import { ref, watch } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Sidebar from '@/Components/Sidebar.vue'

const props = defineProps({
  orders: Array,
  categories: Array, // supaya bisa pilih kategori produk
})

// Modal visibility
const showModal = ref(false)
const successMessage = ref('')

// Form produk
const form = useForm({
  nama: '',
  harga: '',
  stok: '',
  kategori_id: '',
  deskripsi: '',
  image: null,
})

watch(showModal, (val) => {
  if (val) form.reset()
})

function submit() {
  const data = new FormData()
  data.append('nama', form.nama)
  data.append('harga', form.harga)
  data.append('stok', form.stok)
  data.append('kategori_id', form.kategori_id)
  data.append('deskripsi', form.deskripsi)
  if (form.image) data.append('image', form.image)

  form.post(route('user.toko.store'), {
    forceFormData: true,
    onSuccess: () => {
      form.reset()
      showModal.value = false // modal tertutup
      successMessage.value = 'Produk berhasil ditambahkan!'
      setTimeout(() => (successMessage.value = ''), 3000)
    },
  })
}
</script>

<template>
  <Head title="Kelola Pesanan" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Kelola Pesanan
      </h2>
    </template>

    <div class="flex">
      <Sidebar class="fixed left-0 top-0 h-screen" />
      <div class="flex-1 ml-64 bg-gray-100 min-h-screen">
        <div class="py-12">
          <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Daftar Pesanan</h3>
                <button
                  @click="showModal = true"
                  class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                >
                  + Tambah Produk
                </button>
              </div>

              <div v-if="orders.length === 0" class="text-gray-500">
                Belum ada pesanan.
              </div>
              <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead>
                    <tr>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pembeli</th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="order in orders" :key="order.id" class="bg-white even:bg-gray-50">
                      <td class="px-4 py-2">{{ order.id }}</td>
                      <td class="px-4 py-2">{{ order.product_nama }}</td>
                      <td class="px-4 py-2">{{ order.buyer_name }}</td>
                      <td class="px-4 py-2">{{ order.jumlah }}</td>
                      <td class="px-4 py-2">{{ order.status }}</td>
                      <td class="px-4 py-2">{{ order.created_at }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Tambah Produk -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-8 relative animate-fadeIn">
        <button @click="showModal = false" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 text-xl">
          ×
        </button>
        <h3 class="text-xl font-bold mb-6 text-gray-800">Tambah Produk</h3>
        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Nama Produk</label>
            <input v-model="form.nama" type="text" class="w-full border rounded px-3 py-2" required />
          </div>
          <div class="flex gap-4">
            <div class="flex-1">
              <label class="block text-sm font-medium mb-1">Harga</label>
              <input v-model="form.harga" type="number" class="w-full border rounded px-3 py-2" required />
            </div>
            <div class="flex-1">
              <label class="block text-sm font-medium mb-1">Stok</label>
              <input v-model="form.stok" type="number" class="w-full border rounded px-3 py-2" required />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Kategori</label>
            <select v-model="form.kategori_id" class="w-full border rounded px-3 py-2 text-gray-900" required>
              <option value="" disabled hidden>Pilih Kategori</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                {{ cat.nama_kategori || cat.nama }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Deskripsi</label>
            <textarea v-model="form.deskripsi" class="w-full border rounded px-3 py-2" rows="3"></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Gambar Produk</label>
            <input type="file" @change="e => form.image = e.target.files[0]" class="w-full" accept="image/*" />
          </div>
          <div class="flex justify-end gap-2 mt-6">
            <button type="button" @click="showModal = false" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">
              Batal
            </button>
            <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Notifikasi -->
    <transition name="fade">
      <div v-if="successMessage" class="fixed bottom-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg">
        {{ successMessage }}
      </div>
    </transition>
  </AuthenticatedLayout>
</template>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: none; }
}
.animate-fadeIn { animation: fadeIn 0.3s; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.5s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
