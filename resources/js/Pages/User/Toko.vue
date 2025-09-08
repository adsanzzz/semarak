<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
  products: Array,
  categories: Array
});

const showForm = ref(false);
// Ambil kategori dari props.categories
const kategoriList = props.categories || [];
const form = useForm({
  nama: '',
  harga: '',
  stok: '',
  kategori: '',
  deskripsi: '',
  image: ''
});

const editForm = useForm({
  id: null,
  nama: '',
  harga: '',
  stok: '',
  kategori: '',
  deskripsi: '',
  image: ''
});
const showEditForm = ref(false);
function openEditProduk(produk) {
  editForm.id = produk.id;
  editForm.nama = produk.nama;
  editForm.harga = produk.harga;
  editForm.stok = produk.stok;
  editForm.kategori = produk.kategori;
  editForm.deskripsi = produk.deskripsi;
  editForm.image = produk.image;
  showEditForm.value = true;
}

function updateProduk() {
  editForm.put(route('user.toko.update', editForm.id), {
    onSuccess: () => {
      showEditForm.value = false;
      router.reload({ only: ['products'] });
    }
  });
}

function hapusProduk(id) {
  if (confirm('Yakin ingin menghapus produk ini?')) {
    router.delete(route('user.toko.destroy', id), {
      onSuccess: () => router.reload({ only: ['products'] })
    });
  }
}

function tambahProduk() {
  form.post(route('user.toko.store'), {
    onSuccess: () => {
      form.reset();
      showForm.value = false;
      router.reload({ only: ['products'] });
    }
  });
}
</script>

<template>
  <div class="max-w-5xl mx-auto py-10 px-4">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-2xl font-bold text-gray-800">Toko Saya</h1>
      <button @click="showForm = !showForm" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Tambah Barang</button>
    </div>

    <div v-if="showForm" class="mb-8 bg-white p-8 rounded shadow-lg border border-blue-100">
      <h2 class="text-lg font-semibold mb-4 text-blue-700">Tambah Barang Baru</h2>
      <form @submit.prevent="tambahProduk" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-gray-700 mb-1">Nama Barang</label>
          <input v-model="form.nama" type="text" placeholder="Nama Barang" class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-400" required />
        </div>
        <div>
          <label class="block text-gray-700 mb-1">Harga</label>
          <input v-model="form.harga" type="number" min="0" placeholder="Harga" class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-400" required />
        </div>
        <div>
          <label class="block text-gray-700 mb-1">Stok</label>
          <input v-model="form.stok" type="number" min="0" placeholder="Stok" class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-400" required />
        </div>
        <div>
          <label class="block text-gray-700 mb-1">Kategori</label>
          <select v-model="form.kategori" class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-400" required>
            <option value="" disabled>Pilih Kategori</option>
            <option v-for="kat in kategoriList" :key="kat.id" :value="kat.name">{{ kat.name }}</option>
          </select>
        </div>
        <div class="md:col-span-2">
          <label class="block text-gray-700 mb-1">Deskripsi</label>
          <textarea v-model="form.deskripsi" rows="3" placeholder="Deskripsi barang" class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-400"></textarea>
        </div>
        <div class="md:col-span-2">
          <label class="block text-gray-700 mb-1">Upload Gambar (opsional)</label>
          <input type="file" accept="image/*" @change="e => form.image = e.target.files[0]" class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-400" />
        </div>
        <div class="md:col-span-2 flex justify-end gap-2">
          <button type="button" @click="showForm = false" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Batal</button>
          <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Simpan</button>
        </div>
      </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="produk in products" :key="produk.id" class="bg-white rounded-lg shadow p-4 flex flex-col items-center border border-blue-100 relative">
  <img :src="produk.image ? (produk.image.startsWith('produk/') ? '/storage/' + produk.image : produk.image) : ''" :alt="produk.nama" class="w-32 h-32 object-cover rounded mb-4" />
        <div class="text-lg font-semibold text-gray-800 mb-1">{{ produk.nama }}</div>
        <div class="text-blue-600 font-bold mb-1">Rp{{ produk.harga.toLocaleString('id-ID') }}</div>
        <div class="text-sm text-gray-500 mb-1">Stok: {{ produk.stok }}</div>
        <div class="text-sm text-gray-500 mb-1">Kategori: {{ produk.kategori }}</div>
        <div class="text-xs text-gray-400 mb-2 text-center">{{ produk.deskripsi }}</div>
        <div class="text-sm text-gray-500">Terjual: {{ produk.terjual }}</div>
        <div class="text-xs text-gray-400 mt-1">Oleh: {{ produk.user?.name || '-' }}</div>
        <div class="absolute top-2 right-2 flex gap-1">
          <button @click="openEditProduk(produk)" class="bg-yellow-400 hover:bg-yellow-500 text-white px-2 py-1 rounded text-xs">Edit</button>
          <button @click="hapusProduk(produk.id)" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">Hapus</button>
        </div>
      </div>
    </div>
    <!-- Modal Edit Produk -->
    <div v-if="showEditForm" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
      <div class="bg-white p-8 rounded shadow-lg w-full max-w-lg relative">
        <button @click="showEditForm = false" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">✕</button>
        <h2 class="text-lg font-semibold mb-4 text-blue-700">Edit Barang</h2>
        <form @submit.prevent="updateProduk" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-gray-700 mb-1">Nama Barang</label>
            <input v-model="editForm.nama" type="text" class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-400" required />
          </div>
          <div>
            <label class="block text-gray-700 mb-1">Harga</label>
            <input v-model="editForm.harga" type="number" min="0" class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-400" required />
          </div>
          <div>
            <label class="block text-gray-700 mb-1">Stok</label>
            <input v-model="editForm.stok" type="number" min="0" class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-400" required />
          </div>
          <div>
            <label class="block text-gray-700 mb-1">Kategori</label>
            <select v-model="editForm.kategori" class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-400" required>
              <option value="" disabled>Pilih Kategori</option>
              <option v-for="kat in kategoriList" :key="kat.id" :value="kat.name">{{ kat.name }}</option>
            </select>
          </div>
          <div class="md:col-span-2">
            <label class="block text-gray-700 mb-1">Deskripsi</label>
            <textarea v-model="editForm.deskripsi" rows="3" class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-400"></textarea>
          </div>
          <div class="md:col-span-2">
            <label class="block text-gray-700 mb-1">Upload Gambar (opsional)</label>
            <input type="file" accept="image/*" @change="e => editForm.image = e.target.files[0]" class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-400" />
          </div>
          <div class="md:col-span-2 flex justify-end gap-2">
            <button type="button" @click="showEditForm = false" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Batal</button>
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Simpan</button>
          </div>
        </form>
      </div>
    </div>

    <div class="mt-10 bg-white p-6 rounded shadow border border-blue-100">
      <h2 class="text-lg font-semibold mb-4">Hasil Penjualan</h2>
      <div class="text-xl font-bold text-green-700">
        Rp{{ products.reduce((total, p) => total + (p.harga * p.terjual), 0).toLocaleString('id-ID') }}
      </div>
      <div class="text-gray-500 text-sm">Total pendapatan dari semua barang terjual</div>
    </div>
  </div>
</template>

<style scoped>
input:focus, textarea:focus, select:focus {
  outline: 2px solid #2563eb;
}
</style>
