<script setup>
import { ref, onMounted } from "vue";

const cartCount = ref(3);

onMounted(() => {
    const mobileMenuButton = document.querySelector("#mobileMenuButton");
    const navMenu = document.querySelector("#navMenu");

    mobileMenuButton.addEventListener("click", () => {
        navMenu.classList.toggle("mobile-active");
    });
});

const kategoriList = [
    { nama: "Makanan & Minuman", image: "https://via.placeholder.com/100" },
    { nama: "Fashion", image: "https://via.placeholder.com/100" },
    { nama: "Kerajinan", image: "https://via.placeholder.com/100" },
    { nama: "Aksesoris", image: "https://via.placeholder.com/100" },
    { nama: "Makeup & Skincare", image: "https://via.placeholder.com/100" },
    { nama: "Furnitur & Perabotan", image: "https://via.placeholder.com/100" },
    { nama: "Elektronik", image: "https://via.placeholder.com/100" },
    { nama: "Lainnya", image: "https://via.placeholder.com/100" },
];

const produkList = [
    {
        nama: "Kursi Rotan Tahan Lama",
        toko: "Karya Rotan Jaya",
        rating: 4.7,
        terjual: 15,
        hargaCoret: "Rp 450.000",
        harga: "Rp 239.000",
        kategori: "FASHION",
        image: "https://via.placeholder.com/300x200",
    },
    {
        nama: "Paperbag Custom Ukuran Warna",
        toko: "Toko Mustika",
        rating: 4.8,
        terjual: 17,
        hargaCoret: "Rp 30.000",
        harga: "Rp 17.000",
        kategori: "FASHION",
        image: "https://via.placeholder.com/300x200",
    },
    {
        nama: "Kaos Oversize All Size - Coklat",
        toko: "Toko Mitra",
        rating: 4.7,
        terjual: 11,
        hargaCoret: "Rp 92.000",
        harga: "Rp 47.000",
        kategori: "FASHION",
        image: "https://via.placeholder.com/300x200",
    },
    {
        nama: "Baggy Jeans Snow Blue",
        toko: "Toko Mitra",
        rating: 4.5,
        terjual: 10,
        hargaCoret: "Rp 195.000",
        harga: "Rp 97.000",
        kategori: "FASHION",
        image: "https://via.placeholder.com/300x200",
    },
];
</script>

<template>
    <div class="bg-light text-dark font-[Poppins]">
        <!-- Header -->
        <header class="sticky top-0 z-50 bg-white shadow-md">
            <div
                class="container mx-auto px-4 py-4 flex justify-between items-center"
            >
                <div class="flex items-center">
                    <a href="#" class="text-2xl font-bold text-primary"
                        >SEMARAK</a
                    >
                </div>

                <nav
                    id="navMenu"
                    class="hidden md:flex space-x-8 transition-all duration-300"
                >
                    <a href="#" class="hover:text-primary font-medium"
                        >Beranda</a
                    >
                    <div class="relative group">
                        <button class="hover:text-blue-600">Kategori ▾</button>
                        <!-- Dropdown -->
                        <div
                            class="absolute hidden group-hover:block bg-white shadow rounded mt-2 py-2 w-40"
                        >
                            <a
                                href="#"
                                class="block px-4 py-2 hover:bg-gray-100"
                                >Makanan</a
                            >
                            <a
                                href="#"
                                class="block px-4 py-2 hover:bg-gray-100"
                                >Minuman</a
                            >
                            <a
                                href="#"
                                class="block px-4 py-2 hover:bg-gray-100"
                                >Kerajinan</a
                            >
                        </div>
                    </div>
                    <a href="#products" class="hover:text-primary font-medium"
                        >Produk</a
                    >
                    <a href="#promo" class="hover:text-primary font-medium"
                        >Promo dan Diskon</a
                    >
                    <a href="#about" class="hover:text-primary font-medium"
                        >Tentang Semarak</a
                    >
                </nav>

                <div class="flex items-center space-x-4">
                    <button class="relative">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span
                            class="absolute -top-2 -right-2 bg-accent text-white rounded-full w-5 h-5 flex items-center justify-center text-xs"
                            >{{ cartCount }}</span
                        >
                    </button>
                    <a
                        href="/login"
                        class="bg-primary text-black px-4 py-2 rounded-md hover:bg-blue-600"
                    >
                        Login
                    </a>
                    <a
                        href="/register"
                        class="bg-accent text-black px-4 py-2 rounded-md hover:bg-blue-600"
                    >
                        Register
                    </a>
                    <button id="mobileMenuButton" class="md:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="bg-gray-50 py-12">
            <div
                class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center gap-10"
            >
                <!-- Text -->
                <div class="flex-1 space-y-4">
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-800">
                        Selamat Datang di Semarak!
                    </h1>
                    <p class="text-gray-600">
                        Pasar Digital UMKM Karanganyar, Belanja Mudah, Dukung
                        Lokal!!
                    </p>
                    <button
                        class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-navy-700"
                    >
                        Lihat Semua Produk →
                    </button>
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

        <!-- Why Section -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                    Kenapa harus belanja di Semarak?
                </h2>
                <p class="text-gray-600 mb-12">
                    Semarak hadir untuk memudahkan anda menemukan produk terbaik
                    dari UMKM Karanganyar, inilah alasan kenapa belanja di
                    SEMARAK adalah pilihan terbaik!
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
                            pembelian di Semarak langsung GRATIS ongkos kirim ke
                            seluruh wilayah Indonesia.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <div class="text-yellow-400 text-4xl">💰</div>
                        <h3 class="font-semibold">
                            Harga Terjangkau, Kualitas Premium
                        </h3>
                        <p class="text-sm text-gray-600">
                            Dapatkan produk lokal dengan kualitas terbaik dan
                            harga bersahabat. Dukung UMKM tanpa menguras
                            kantong!
                        </p>
                    </div>

                    <div class="space-y-3">
                        <div class="text-yellow-400 text-4xl">🛍️</div>
                        <h3 class="font-semibold">
                            Dukung Produk Lokal Langsung dari Pengrajin
                        </h3>
                        <p class="text-sm text-gray-600">
                            Setiap pembelian Anda membantu menggerakkan ekonomi
                            kreatif Karanganyar. Bangga menggunakan produk asli
                            daerah!
                        </p>
                    </div>

                    <div class="space-y-3">
                        <div class="text-yellow-400 text-4xl">✅</div>
                        <h3 class="font-semibold">
                            Garansi Kepuasan Pelanggan
                        </h3>
                        <p class="text-sm text-gray-600">
                            Tak perlu ragu! Kami menjamin produk original dan
                            siap membantu jika ada kendala. Belanja aman, hati
                            pun tenang.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ✅ Bagian Kategori & Produk Rekomendasi -->
        <!-- Section Oleh-Oleh Khas Karanganyar -->
        <section class="w-full bg-gray-100 py-20 mt-12">
            <!-- Bungkus full width -->
            <div class="px-6">
                <!-- Kontainer isi di tengah -->
                <div
                    class="max-w-7xl mx-auto grid md:grid-cols-2 items-center gap-8"
                >
                    <!-- Bagian Teks -->
                    <div class="relative z-10 text-left">
                        <h2
                            class="text-2xl md:text-3xl font-bold text-gray-900 mb-4"
                        >
                            Cari Oleh-Oleh Khas Karanganyar
                        </h2>
                        <p class="text-gray-600 mb-6">
                            Jelajahi dan Temukan Beragam Oleh-Oleh Khas
                            Karanganyar, Diproduksi Langsung oleh UMKM Lokal
                            dengan Kualitas Terbaik!
                        </p>
                        <button
                            class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-md transition"
                        >
                            Lihat Produk
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 ml-2"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5l7 7-7 7"
                                />
                            </svg>
                        </button>
                    </div>

                    <!-- Gambar di kanan -->
                    <div class="flex justify-center md:justify-end">
                        <div
                            class="w-[185px] h-[185px] rounded-full overflow-hidden shadow-md mr-6"
                        >
                            <img
                                src="/images/timus.jpg"
                                alt="Oleh-Oleh Karanganyar"
                                class="w-full h-full object-cover"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section Produk Populer -->
        <section class="w-full bg-white py-16">
            <div class="max-w-6xl mx-auto px-6 text-center">
                <!-- Judul -->
                <h2 class="text-xl md:text-2xl font-semibold text-gray-900 mb-2">
                    Informasi Produk UMKM
                </h2>
                <p class="text-gray-600 mb-10">
                    Temukan berbagai produk unggulan dari UMKM Karanganyar. Dukung produk lokal dan dapatkan kualitas terbaik!
                </p>
                <div class="bg-blue-50 rounded-lg p-8 text-left shadow">
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Produk UMKM Karanganyar tersedia mulai dari makanan, fashion, kerajinan, hingga elektronik.</li>
                        <li>Setiap produk telah melalui kurasi kualitas oleh tim Semarak.</li>
                        <li>Belanja langsung dari pengrajin dan pelaku UMKM lokal.</li>
                        <li>Harga terjangkau, kualitas premium, dan layanan ramah.</li>
                        <li>Transaksi aman, pengiriman cepat ke seluruh Indonesia.</li>
                        <li>Produk yang ditampilkan adalah hasil karya asli UMKM Karanganyar.</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Section Ulasan Pembeli -->
        <section class="w-full bg-gray-100 py-16">
            <div class="max-w-6xl mx-auto px-6 text-center">
                <!-- Judul -->
                <h2
                    class="text-xl md:text-2xl font-semibold text-gray-900 mb-2"
                >
                    Ulasan Pembeli Semarak
                </h2>
                <p class="text-gray-600 mb-10">
                    Apa Kata Mereka? Simak Ulasan Pembeli tentang Produk dan
                    Layanan di SEMARAK!
                </p>

                <!-- Carousel Ulasan -->
                <div class="flex items-center justify-center space-x-6">
                    <!-- Card sebelah kiri (blur/lebih kecil) -->
                    <div
                        class="hidden md:block w-80 bg-white rounded-xl shadow-md opacity-50 scale-90"
                    >
                        <div class="flex p-6 space-x-4">
                            <img
                                src="/images/woman1.jpg"
                                alt="Maudy"
                                class="w-20 h-20 rounded-md object-cover"
                            />
                            <div class="text-left">
                                <p class="text-sm text-gray-500">
                                    Produk berkualitas, pengiriman cepat, dan
                                    kemasan rapi!
                                </p>
                                <div class="flex text-yellow-400 mt-2">
                                    ★★★★★
                                </div>
                                <h4 class="font-semibold text-gray-800">
                                    Maudy
                                </h4>
                                <p class="text-sm text-gray-500">
                                    Jebres, Surakarta
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Card aktif (tengah) -->
                    <div
                        class="w-[420px] bg-white rounded-xl shadow-lg transform scale-100"
                    >
                        <div class="flex p-6 space-x-4">
                            <img
                                src="/images/woman2.jpg"
                                alt="Almira Jati"
                                class="w-24 h-24 rounded-md object-cover"
                            />
                            <div class="text-left">
                                <p class="text-sm text-gray-600 mb-2">
                                    Produk berkualitas, pengiriman cepat, dan
                                    kemasan rapi! Senang bisa belanja oleh-oleh
                                    khas Karanganyar di SEMARAK. Pasti repeat
                                    order!
                                </p>
                                <div class="flex text-yellow-400 mb-2">
                                    ★★★★★
                                </div>
                                <h4 class="font-semibold text-gray-900">
                                    Almira Jati
                                </h4>
                                <p class="text-sm text-gray-500">
                                    Jebres, Surakarta
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Card sebelah kanan (blur/lebih kecil) -->
                    <div
                        class="hidden md:block w-80 bg-white rounded-xl shadow-md opacity-50 scale-90"
                    >
                        <div class="flex p-6 space-x-4">
                            <img
                                src="/images/woman3.jpg"
                                alt="Kiky Saputri"
                                class="w-20 h-20 rounded-md object-cover"
                            />
                            <div class="text-left">
                                <p class="text-sm text-gray-500">
                                    Produk berkualitas, pengiriman cepat, dan
                                    kemasan rapi!
                                </p>
                                <div class="flex text-yellow-400 mt-2">
                                    ★★★★★
                                </div>
                                <h4 class="font-semibold text-gray-800">
                                    Kiky Saputri
                                </h4>
                                <p class="text-sm text-gray-500">
                                    Jebres, Surakarta
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigasi Carousel -->
                <div class="flex justify-center space-x-4 mt-8">
                    <button
                        class="w-10 h-10 rounded-full border flex items-center justify-center hover:bg-gray-100"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 19l-7-7 7-7"
                            />
                        </svg>
                    </button>
                    <button
                        class="w-10 h-10 rounded-full border flex items-center justify-center hover:bg-gray-100"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </section>

        <!-- Section Warisan Lokal -->
        <section class="w-full bg-white py-16">
            <div
                class="max-w-6xl mx-auto px-6 grid md:grid-cols-2 items-stretch gap-8"
            >
                <!-- Kotak Biru dengan Teks -->
                <div
                    class="bg-blue-900 text-white p-10 shadow-md h-full flex flex-col justify-center"
                >
                    <h2
                        class="text-2xl md:text-3xl font-bold mb-4 leading-snug"
                    >
                        Keindahan Warisan Lokal, <br />
                        Sentuhan Khas Karanganyar
                    </h2>
                    <p class="mb-6 leading-relaxed text-gray-100">
                        Dibuat dengan cinta oleh para pengrajin berbakat, setiap
                        produk kerajinan khas Karanganyar menghadirkan keunikan
                        dan nilai budaya yang tinggi. Dari batik tulis yang
                        elegan hingga anyaman tangan yang indah, setiap detail
                        mencerminkan kearifan lokal yang diwariskan
                        turun-temurun. Dukung UMKM lokal dan miliki karya seni
                        autentik yang penuh makna. Temukan koleksi terbaik hanya
                        di SEMARAK!
                    </p>
                    <button
                        class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 transition"
                    >
                        Lihat Produk Selengkapnya
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 ml-2"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Gambar di kanan -->
                <div class="h-full">
                    <img
                        src="/images/batik.jpg"
                        alt="Kerajinan Karanganyar"
                        class="w-full h-full object-cover shadow-md"
                    />
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2; /* untuk browser WebKit */
    -webkit-box-orient: vertical;
    overflow: hidden;

    line-clamp: 2; /* properti standar */
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
