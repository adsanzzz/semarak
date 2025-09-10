<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    categories: Array
});

const iconOptions = [
    { label: 'Toko', value: 'store' },
    { label: 'Makanan', value: 'food' },
    { label: 'Fashion', value: 'fashion' },
    { label: 'Elektronik', value: 'electronics' },
    { label: 'Kerajinan', value: 'craft' },
    { label: 'Aksesoris', value: 'accessories' },
    { label: 'Lainnya', value: 'other' },
];

const form = useForm({
    icon: '',
    nama_kategori: '',
    deskripsi: '',
});

function submitCategory() {
    form.post(route('admin.categories.store'), {
        onSuccess: () => {
            form.reset();
        }
    });
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold">Kategori</h3>
                        <button @click="showForm = !showForm" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">+</button>
                    </div>
                    <div v-if="showForm" class="mb-4">
                        <form @submit.prevent="submitCategory" class="grid grid-cols-1 md:grid-cols-3 gap-2 items-end">
                            <div>
                                <label class="block text-xs font-semibold mb-1">Icon</label>
                                <select v-model="form.icon" class="border rounded px-2 py-1 w-full" required>
                                    <option value="" disabled>Pilih Icon</option>
                                    <option v-for="opt in iconOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold mb-1">Nama Kategori</label>
                                <input v-model="form.nama_kategori" type="text" placeholder="Nama kategori" class="border rounded px-2 py-1 w-full" required />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold mb-1">Deskripsi</label>
                                <input v-model="form.deskripsi" type="text" placeholder="Deskripsi kategori" class="border rounded px-2 py-1 w-full" />
                            </div>
                            <button type="submit" class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700 md:col-span-3">Tambah</button>
                        </form>
                        <div v-if="form.errors.icon" class="text-red-500 text-sm mt-1">{{ form.errors.icon }}</div>
                        <div v-if="form.errors.nama_kategori" class="text-red-500 text-sm mt-1">{{ form.errors.nama_kategori }}</div>
                    </div>

                    <!-- Tabel Kategori -->
                    <table class="min-w-full border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2 text-left">#</th>
                                <th class="border px-4 py-2 text-left">Icon</th>
                                <th class="border px-4 py-2 text-left">Nama Kategori</th>
                                <th class="border px-4 py-2 text-left">Deskripsi</th>
                                <th class="border px-4 py-2 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(cat, idx) in categories" :key="cat.id">
                                <td class="border px-4 py-2">{{ idx + 1 }}</td>
                                <td class="border px-4 py-2">{{ cat.icon }}</td>
                                <td class="border px-4 py-2">
                                    <span v-if="editId !== cat.id">{{ cat.nama_kategori }}</span>
                                    <form v-else @submit.prevent="updateCategory(cat.id)" class="inline-flex gap-2">
                                        <input v-model="editName" type="text" class="border rounded px-2 py-1" required />
                                        <button type="submit" class="bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">Simpan</button>
                                        <button type="button" @click="cancelEdit" class="bg-gray-400 text-white px-2 py-1 rounded hover:bg-gray-500">Batal</button>
                                    </form>
                                </td>
                                <td class="border px-4 py-2">{{ cat.deskripsi }}</td>
                                <td class="border px-4 py-2">
                                    <button v-if="editId !== cat.id" @click="startEdit(cat)" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 mr-2">Edit</button>
                                    <button @click="deleteCategory(cat.id)" class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
export default {
  data() {
    return {
      showForm: false,
      editId: null,
      editName: ''
    };
  },
  methods: {
    startEdit(cat) {
      this.editId = cat.id;
      this.editName = cat.nama_kategori;
    },
    cancelEdit() {
      this.editId = null;
      this.editName = '';
    },
    updateCategory(id) {
      this.$inertia.put(this.route('admin.categories.update', id), { nama_kategori: this.editName }, {
        onSuccess: () => {
          this.cancelEdit();
        }
      });
    },
    deleteCategory(id) {
      if (confirm('Yakin ingin menghapus kategori ini?')) {
        this.$inertia.delete(this.route('admin.categories.destroy', id));
      }
    }
  }
};
</script>
