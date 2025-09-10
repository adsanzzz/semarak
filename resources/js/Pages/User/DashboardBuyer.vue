<script setup>
import { ref } from "vue";
import {
    ShoppingCartIcon,
    BellIcon,
    ChatBubbleOvalLeftEllipsisIcon,
    UserIcon,
} from "@heroicons/vue/24/outline";

const menus = [
    { name: "Beranda", route: "/" },
    { name: "Kategori", route: "/kategori" },
    { name: "Promo & Diskon", route: "/promo" },
    { name: "Tentang Semarak", route: "/tentang" },
];
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Top Info Bar -->
        <div class="bg-blue-900 text-white text-sm text-center py-2">
            Data anda belum lengkap, untuk melanjutkan checkout, harap lengkapi
            informasi pribadi di Pengaturan Akun terlebih dahulu!
            <a href="#" class="underline ml-1">Klik Disini</a>
        </div>

        <!-- Navbar -->
        <header class="bg-white shadow-sm">
            <div
                class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between"
            >
                <!-- Logo -->
                <div class="flex items-center space-x-2">
                    <img
                        src="images/Logomark.png"
                        alt="Semarak"
                        class="h-8 w-8"
                    />
                    <span class="font-bold text-lg">Semarak</span>
                </div>

                <!-- Menu -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a
                        v-for="menu in menus"
                        :key="menu.name"
                        :href="menu.route"
                        class="text-gray-700 hover:text-blue-600"
                    >
                        {{ menu.name }}
                    </a>
                </nav>

                <!-- Search + Icons -->
                <div class="flex items-center space-x-5">
                    <div class="relative">
                        <input
                            type="text"
                            placeholder="Cari produk.."
                            class="border rounded-md pl-10 pr-4 py-2 w-64 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5 absolute left-3 top-2.5 text-gray-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"
                            />
                        </svg>
                    </div>

                    <!-- Icons -->
                    <ShoppingCartIcon
                        class="w-6 h-6 text-gray-600 hover:text-blue-600 cursor-pointer"
                    />
                    <BellIcon
                        class="w-6 h-6 text-gray-600 hover:text-blue-600 cursor-pointer"
                    />
                    <ChatBubbleOvalLeftEllipsisIcon
                        class="w-6 h-6 text-gray-600 hover:text-blue-600 cursor-pointer"
                    />
                    <UserIcon
                        class="w-6 h-6 text-gray-600 hover:text-blue-600 cursor-pointer"
                    />
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
        <section class="max-w-7xl mx-auto px-6 py-16">
            <!-- Judul Section -->
            <div class="text-center mb-10">
                <h2 class="text-xl font-semibold text-gray-800">
                    Produk Populer
                </h2>
                <p class="text-gray-600">
                    Produk Favorit yang Paling Diminati, Langsung dari UMKM
                    Karanganyar!
                </p>
                <h3 class="mt-6 text-lg font-semibold text-gray-800">
                    Produk Rekomendasi
                </h3>
                <p class="text-gray-600">
                    Pilihan Terbaik dari UMKM Karanganyar, Produk Berkualitas
                    yang Wajib Anda Coba!
                </p>
            </div>

            <!-- Grid Produk -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card Produk -->
                <div
                    v-for="item in [
                        {
                            id: 1,
                            name: 'Kursi Rotan Tahan Lama',
                            seller: 'Karya Rotan Jaya',
                            distance: '3.2 KM',
                            sold: 15,
                            rating: 4.7,
                            category: 'FASHION',
                            oldPrice: 'Rp 450.000,00',
                            price: 'Rp 239.000,00',
                            image: '/produk/kursi.jpg',
                        },
                        {
                            id: 2,
                            name: 'Paperbag Custom Ukuran Warna',
                            seller: 'Toko Merdeka',
                            distance: '6.7 KM',
                            sold: 17,
                            rating: 4.6,
                            category: 'FASHION',
                            oldPrice: 'Rp 23.000,00',
                            price: 'Rp 17.000,00',
                            image: '/produk/paperbag.jpg',
                        },
                        {
                            id: 3,
                            name: 'Kaos Oversize All Size - Coklat',
                            seller: 'Toko Satria Nusa',
                            distance: '6.7 KM',
                            sold: 11,
                            rating: 4.7,
                            category: 'FASHION',
                            oldPrice: 'Rp 90.000,00',
                            price: 'Rp 47.000,00',
                            image: '/produk/kaos.jpg',
                        },
                        {
                            id: 4,
                            name: 'Baggy Jeans Snow Blue',
                            seller: 'Toko Millia',
                            distance: '6.7 KM',
                            sold: 10,
                            rating: 4.5,
                            category: 'FASHION',
                            oldPrice: 'Rp 237.000,00',
                            price: 'Rp 97.000,00',
                            image: '/produk/jeans.jpg',
                        },
                    ]"
                    :key="item.id"
                    class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition flex flex-col"
                >
                    <!-- Gambar -->
                    <div class="relative">
                        <img
                            :src="item.image"
                            alt=""
                            class="w-full h-56 object-cover"
                        />
                        <!-- Tombol love/like (kanan atas) -->
                        <button
                            class="absolute top-3 right-3 bg-white rounded-full p-2 shadow hover:scale-110 transition"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-5 h-5 text-gray-500"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 
                   4.5 0 116.364 6.364L12 21.364l-7.682-7.682a4.5 
                   4.5 0 010-6.364z"
                                />
                            </svg>
                        </button>
                    </div>

                    <!-- Detail -->
                    <div class="p-4 flex flex-col flex-grow">
                        <!-- Rating & Terjual -->
                        <div
                            class="flex items-center justify-between text-sm text-gray-600 mb-2"
                        >
                            <div class="flex items-center space-x-1">
                                <!-- Icon bintang -->
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-5 h-5 text-yellow-400"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 
                     3.292a1 1 0 00.95.69h3.462c.969 0 
                     1.371 1.24.588 1.81l-2.8 2.034a1 1 
                     0 00-.364 1.118l1.07 3.292c.3.921-.755 
                     1.688-1.54 1.118l-2.8-2.034a1 1 
                     0 00-1.176 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 
                     1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 
                     1 0 00.951-.69l1.07-3.292z"
                                    />
                                </svg>
                                <span>{{ item.rating }}</span>
                            </div>
                            <span>{{ item.sold }} Terjual</span>
                        </div>

                        <!-- Nama Produk -->
                        <h4 class="font-semibold text-gray-800 leading-snug">
                            {{ item.name }}
                        </h4>

                        <!-- Seller & Distance -->
                        <p class="text-sm text-gray-500 mt-1">
                            {{ item.seller }} · {{ item.distance }}
                        </p>

                        <!-- Kategori -->
                        <span
                            class="inline-block mt-2 px-3 py-1 text-xs bg-gray-200 text-gray-700 rounded-full"
                        >
                            {{ item.category }}
                        </span>

                        <!-- Harga -->
                        <div class="mt-3">
                            <p class="text-sm text-gray-400 line-through">
                                {{ item.oldPrice }}
                            </p>
                            <p class="text-blue-600 font-bold">
                                {{ item.price }}
                            </p>
                        </div>
                    </div>

                    <!-- Tombol -->
                    <div class="p-4 border-t">
                        <button
                            class="w-full bg-yellow-500 hover:bg-yellow-600 text-white flex items-center justify-center space-x-2 py-2 rounded-md font-medium transition"
                        >
                            <!-- Icon keranjang -->
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
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.293 
                   2.293a1 1 0 00.707 1.707H19m-12 
                   0a1 1 0 100 2 1 1 0 000-2zm12 
                   0a1 1 0 100 2 1 1 0 000-2z"
                                />
                            </svg>
                            <span>Tambah ke Keranjang</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Navigasi Carousel -->
            <div class="flex justify-center items-center mt-8 space-x-4">
                <button
                    class="w-10 h-10 rounded-full border flex items-center justify-center hover:bg-gray-100"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M12.707 15.707a1 1 0 01-1.414 0L6.586 
            11l4.707-4.707a1 1 0 111.414 1.414L9.414 
            11l3.293 3.293a1 1 0 010 1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </button>
                <button
                    class="w-10 h-10 rounded-full border flex items-center justify-center hover:bg-gray-100"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M7.293 4.293a1 1 0 011.414 0L13.414 
            9l-4.707 4.707a1 1 0 01-1.414-1.414L10.586 
            9 7.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </button>
            </div>

            <!-- Tombol bawah -->
            <div class="flex flex-col items-center mt-6 space-y-6">
                <a
                    href="#"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md font-medium"
                >
                    Lihat Semua Produk Rekomendasi
                </a>
                <a
                    href="#"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md font-medium"
                >
                    Lihat Semua Produk Populer
                </a>
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
