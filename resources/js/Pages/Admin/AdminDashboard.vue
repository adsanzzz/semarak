<script setup>
// import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

// nanti bisa kirim data dari controller:
// totalUsers, totalOrders, totalProducts, dll
defineProps({
    totalUsers: Number,
    totalOrders: Number,
    totalProducts: Number,
    totalComplaints: Number,
})
</script>

<template>
    <Head title="Dashboard Admin" />

    <AdminLayout>

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                Dashboard Admin
            </h2>
            <p class="text-gray-500 text-sm">
                Ringkasan data sistem
            </p>
        </div>

        <!-- CARD SUMMARY -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

            <div class="bg-white shadow rounded-lg p-6">
                <h4 class="text-sm text-gray-500">Total Akun</h4>
                <p class="text-2xl font-bold text-blue-600">
                    {{ totalUsers }}
                </p>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h4 class="text-sm text-gray-500">Total Pesanan</h4>
                <p class="text-2xl font-bold text-green-600">
                    {{ totalOrders }}
                </p>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h4 class="text-sm text-gray-500">Total Produk</h4>
                <p class="text-2xl font-bold text-purple-600">
                    {{ totalProducts }}
                </p>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h4 class="text-sm text-gray-500">Total Komplain</h4>
                <p class="text-2xl font-bold text-red-600">
                    {{ totalComplaints }}
                </p>
            </div>

        </div>

    </AdminLayout>
</template>