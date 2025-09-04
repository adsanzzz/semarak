<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    categories: Array
});

const form = useForm({
    name: ''
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
                        <form @submit.prevent="submitCategory" class="flex gap-2">
                            <input v-model="form.name" type="text" placeholder="Nama kategori" class="border rounded px-2 py-1" required />
                            <button type="submit" class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700">Tambah</button>
                        </form>
                        <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                    </div>
                    <table class="min-w-full border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2 text-left">#</th>
                                <th class="border px-4 py-2 text-left">Nama Kategori</th>
                                <th class="border px-4 py-2 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(cat, idx) in categories" :key="cat.id">
                                <td class="border px-4 py-2">{{ idx + 1 }}</td>
                                <td class="border px-4 py-2">
                                    <span v-if="editId !== cat.id">{{ cat.name }}</span>
                                    <form v-else @submit.prevent="updateCategory(cat.id)" class="inline-flex gap-2">
                                        <input v-model="editName" type="text" class="border rounded px-2 py-1" required />
                                        <button type="submit" class="bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">Simpan</button>
                                        <button type="button" @click="cancelEdit" class="bg-gray-400 text-white px-2 py-1 rounded hover:bg-gray-500">Batal</button>
                                    </form>
                                </td>
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
      this.editName = cat.name;
    },
    cancelEdit() {
      this.editId = null;
      this.editName = '';
    },
    updateCategory(id) {
      this.$inertia.put(this.route('admin.categories.update', id), { name: this.editName }, {
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
