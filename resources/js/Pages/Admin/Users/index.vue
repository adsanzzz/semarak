<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, router } from '@inertiajs/vue3'

const props = defineProps({
    admin: Array,
    penjual: Array,
    pembeli: Array
})

function deleteUser(id) {
    if (confirm('Yakin ingin menghapus user ini?')) {
        router.delete(route('admin.users.destroy', id))
    }
}
</script>

<template>
    <Head title="Kelola Akun" />

    <AdminLayout>
        <div class="bg-white shadow rounded-lg p-6">

            <h2 class="text-xl font-bold mb-6">
                Kelola Akun
            </h2>

            <!-- ================= ADMIN ================= -->
            <h3 class="text-lg font-bold mb-3">Admin</h3>

            <div v-if="admin.length === 0" class="text-gray-500 mb-4">
                Tidak ada admin
            </div>

            <div v-for="(user, index) in admin" :key="user.id"
                class="flex justify-between items-center border p-3 rounded mb-2">
                <div>
                    <div class="font-semibold">{{ user.name }}</div>
                    <div class="text-sm text-gray-500">{{ user.email }}</div>
                </div>

                <button
                    @click="deleteUser(user.id)"
                    class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm"
                >
                    Hapus
                </button>
            </div>


            <!-- ================= PENJUAL ================= -->
            <h3 class="text-lg font-bold mt-8 mb-3">Penjual</h3>

            <div v-if="penjual.length === 0" class="text-gray-500 mb-4">
                Tidak ada penjual
            </div>

            <div v-for="(user, index) in penjual" :key="user.id"
                class="flex justify-between items-center border p-3 rounded mb-2">
                <div>
                    <div class="font-semibold">{{ user.name }}</div>
                    <div class="text-sm text-gray-500">{{ user.email }}</div>
                </div>

                <button
                    @click="deleteUser(user.id)"
                    class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm"
                >
                    Hapus
                </button>
            </div>


            <!-- ================= PEMBELI ================= -->
            <h3 class="text-lg font-bold mt-8 mb-3">Pembeli</h3>

            <div v-if="pembeli.length === 0" class="text-gray-500 mb-4">
                Tidak ada pembeli
            </div>

            <div v-for="(user, index) in pembeli" :key="user.id"
                class="flex justify-between items-center border p-3 rounded mb-2">
                <div>
                    <div class="font-semibold">{{ user.name }}</div>
                    <div class="text-sm text-gray-500">{{ user.email }}</div>
                </div>

                <button
                    @click="deleteUser(user.id)"
                    class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm"
                >
                    Hapus
                </button>
            </div>

        </div>
    </AdminLayout>
</template>