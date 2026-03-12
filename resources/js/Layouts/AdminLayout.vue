<script setup>
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
            <div class="w-64 bg-white shadow-md min-h-screen p-4">

                <h3 class="text-lg font-bold mb-4 border-b pb-2">
                    Menu Admin
                </h3>

                <ul class="space-y-2 text-sm">

                    <li>
                        <Link :href="route('admin.categories.index')"
                              class="block px-3 py-2 rounded-md hover:bg-blue-100 hover:text-blue-700">
                            📂 Kelola Kategori
                        </Link>
                    </li>

                    <li>
                        <Link href="#"
                              class="block px-3 py-2 rounded-md hover:bg-blue-100 hover:text-blue-700">
                            🧾 Histori Pesanan
                        </Link>
                    </li>

                    <li>
                        <Link href="#"
                              class="block px-3 py-2 rounded-md hover:bg-blue-100 hover:text-blue-700">
                            ⚠ Kelola Pengaduan Komplain
                        </Link>
                    </li>

                    <li>
                        <Link :href="route('admin.users.index')"
                              class="block px-3 py-2 rounded-md hover:bg-blue-100 hover:text-blue-700">
                            👤 Kelola Akun
                        </Link>
                    </li>

                    <li>
                        <Link href="#"
                              class="block px-3 py-2 rounded-md hover:bg-blue-100 hover:text-blue-700">
                            🛍 Kelola Produk
                        </Link>
                    </li>

                </ul>
            </div>

            <!-- CONTENT -->
            <div class="flex-1 p-8">
                <slot />
            </div>

        </div>
    </div>
</template>