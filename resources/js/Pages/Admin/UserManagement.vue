<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'

const props = defineProps({
  users: Array
})

function deleteUser(id) {
  if (confirm('Yakin ingin menghapus user ini?')) {
    router.delete(route('admin.users.destroy', id))
  }
}
</script>

<template>
  <Head title="Kelola User" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Kelola User</h2>
    </template>
    <div class="py-8 max-w-5xl mx-auto">
      <table class="w-full border">
        <thead>
          <tr class="bg-gray-100">
            <th class="border px-4 py-2">#</th>
            <th class="border px-4 py-2">Nama</th>
            <th class="border px-4 py-2">Email</th>
            <th class="border px-4 py-2">Role</th>
            <th class="border px-4 py-2">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(user, idx) in users" :key="user.id">
            <td class="border px-4 py-2">{{ idx + 1 }}</td>
            <td class="border px-4 py-2">{{ user.name }}</td>
            <td class="border px-4 py-2">{{ user.email }}</td>
            <td class="border px-4 py-2">
              <span v-if="user.role === 1">Admin</span>
              <span v-else-if="user.role === 2">Toko</span>
              <span v-else-if="user.role === 3">Buyer</span>
              <span v-else>-</span>
            </td>
            <td class="border px-4 py-2">
              <button @click="deleteUser(user.id)" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AuthenticatedLayout>
</template>
