<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';

const props = defineProps({
    categories: Array
});

// mapping kategori → emoji
const iconMap = {
    makanan: "🍔",
    minuman: "🥤",
    elektronik: "💻",
    pakaian: "👕",
    olahraga: "⚽",
    hewan: "🐶",
    buku: "📚",
    musik: "🎵",
    mainan: "🎮",
    kecantikan: "💄",
    kesehatan: "💊",
    bunga: "🌸",
    kendaraan: "🚗",
    default: "🛒"
};

// fungsi ambil icon sesuai nama kategori
function getIcon(name) {
    if (!name) return iconMap.default;
    const lower = name.toLowerCase();
    return iconMap[lower] ?? iconMap.default;
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <!-- Header harus langsung di dalam AuthenticatedLayout -->
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <!-- Layout utama: sidebar + konten -->
        <div class="flex">
            <!-- Sidebar -->
            <Sidebar class="fixed left-0 top-0 h-screen" />

            <!-- Konten utama -->
            <div class="flex-1 ml-64 bg-gray-100 min-h-screen">
                <div class="py-12">
                    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Kategori</h3>

                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
                            >
                                <div
                                    v-for="cat in categories"
                                    :key="cat.id"
                                    class="bg-gradient-to-br from-yellow-100 to-orange-100 rounded-2xl shadow-md p-6 flex flex-col items-center justify-center hover:scale-105 hover:shadow-xl transition transform duration-300"
                                >
                                    <!-- Icon kategori -->
                                    <div
                                        class="w-16 h-16 mb-4 flex items-center justify-center bg-white rounded-full shadow"
                                    >
                                        <span class="text-3xl">{{
                                            getIcon(cat.name)
                                        }}</span>
                                    </div>

                                    <!-- Nama kategori -->
                                    <div class="text-lg font-semibold text-gray-800 mb-1">
                                        {{ cat.name }}
                                    </div>
                                    <div class="text-sm text-gray-500 italic">
                                        Kategori produk
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
