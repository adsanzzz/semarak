<script setup>
import Sidebar from '@/Components/Sidebar.vue'
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-gray-100">

        <!-- HEADER -->
        <div class="bg-white shadow px-6 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-gray-800">
                Admin Dashboard
            </h1>

            <div>
                <Link :href="route('logout')" method="post" as="button" class="bg-red-600 text-white px-3 py-1 rounded-md hover:bg-red-700">
                    Logout
                </Link>
            </div>
        </div>

        <div class="flex">

            <!-- SIDEBAR -->
            <Sidebar />

            <!-- CONTENT -->
            <div class="flex-1 ml-64 p-8">
                <slot />
            </div>

        </div>
    </div>
</template>