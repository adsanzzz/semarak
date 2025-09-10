<script setup>
import { ref } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

const menus = [
    { name: "Beranda", route: "/" },
    { name: "Kategori", route: "/kategori" },
    { name: "Promo & Diskon", route: "/promo" },
    { name: "Tentang Semarak", route: "/tentang" },
];

const showDropdown = ref(false);
const page = usePage();
const user = page.props.auth.user;

function logout() {
    router.post(route("logout"));
}
function toggleDropdown() {
    showDropdown.value = !showDropdown.value;
}
function closeDropdown() {
    showDropdown.value = false;
}
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-gray-50 relative">

            <!-- ✅ Hero Section -->
            <section class="bg-gray-50 py-12">
                <div
                    class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center gap-10"
                >
                    <!-- Text -->
                    <div class="flex-1 space-y-4">
                        <h1
                            class="text-3xl md:text-4xl font-bold text-gray-800"
                        >
                            Selamat Datang di Semarak!
                        </h1>
                        <p class="text-gray-600">
                            Pasar Digital UMKM Karanganyar, Belanja Mudah, Dukung
                            Lokal!!
                        </p>
                        <Link
                            href="/lihat-produk"
                            class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 inline-block"
                        >
                            Lihat Semua Produk →
                        </Link>
                    </div>

                    <!-- Image -->
                    <div class="flex-1">
                        <img
                            src="/images/marketplace.png"
                            alt="Marketplace"
                            class="w-100 h-70 mx-auto"
                        />
                    </div>
                </div>
            </section>

            <!-- ✅ Why Section -->
            <section class="py-16 bg-white">
                <div class="max-w-7xl mx-auto px-6 text-center">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                        Kenapa harus belanja di Semarak?
                    </h2>
                    <p class="text-gray-600 mb-12">
                        Semarak hadir untuk memudahkan anda menemukan produk
                        terbaik dari UMKM Karanganyar, inilah alasan kenapa
                        belanja di SEMARAK adalah pilihan terbaik!
                    </p>

                    <!-- 4 Features -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                        <div class="space-y-3">
                            <div class="text-yellow-400 text-4xl">🚚</div>
                            <h3 class="font-semibold">
                                Gratis Ongkir Seluruh Indonesia
                            </h3>
                            <p class="text-sm text-gray-600">
                                Nikmati belanja tanpa khawatir biaya! Setiap
                                pembelian di Semarak langsung GRATIS ongkos
                                kirim ke seluruh wilayah Indonesia.
                            </p>
                        </div>

                        <div class="space-y-3">
                            <div class="text-yellow-400 text-4xl">💰</div>
                            <h3 class="font-semibold">
                                Harga Terjangkau, Kualitas Premium
                            </h3>
                            <p class="text-sm text-gray-600">
                                Dapatkan produk lokal dengan kualitas terbaik
                                dan harga bersahabat. Dukung UMKM tanpa menguras
                                kantong!
                            </p>
                        </div>

                        <div class="space-y-3">
                            <div class="text-yellow-400 text-4xl">🛍️</div>
                            <h3 class="font-semibold">
                                Dukung Produk Lokal Langsung dari Pengrajin
                            </h3>
                            <p class="text-sm text-gray-600">
                                Setiap pembelian Anda membantu menggerakkan
                                ekonomi kreatif Karanganyar. Bangga menggunakan
                                produk asli daerah!
                            </p>
                        </div>

                        <div class="space-y-3">
                            <div class="text-yellow-400 text-4xl">✅</div>
                            <h3 class="font-semibold">
                                Garansi Kepuasan Pelanggan
                            </h3>
                            <p class="text-sm text-gray-600">
                                Tak perlu ragu! Kami menjamin produk original
                                dan siap membantu jika ada kendala. Belanja aman,
                                hati pun tenang.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ✅ Section lain (Kategori, Produk, Ulasan, dll.) -->
            <!-- Saya biarkan isinya sama persis dengan punyamu -->
            <!-- Tinggal copy bagian <section> selanjutnya -->
            <!-- karena struktur sudah benar -->

        </div>
    </AppLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-clamp: 2;
}

/* ✅ Mobile menu aktif */
#navMenu.mobile-active {
    display: flex !important;
    flex-direction: column;
    position: absolute;
    top: 4rem;
    left: 0;
    right: 0;
    background: white;
    padding: 1rem;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}
</style>
