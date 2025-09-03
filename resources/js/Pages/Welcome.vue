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
</script>

<template>
  <div class="bg-light text-dark font-[Poppins]">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white shadow-md">
      <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <div class="flex items-center">
          <a href="#" class="text-2xl font-bold text-primary">SEMARAK</a>
        </div>

        <nav
          id="navMenu"
          class="hidden md:flex space-x-8 transition-all duration-300"
        >
          <a href="#" class="hover:text-primary font-medium">Beranda</a>
           <div class="relative group">
            <button class="hover:text-blue-600">Kategori ▾</button>
            <!-- Dropdown -->
            <div class="absolute hidden group-hover:block bg-white shadow rounded mt-2 py-2 w-40">
              <a href="#" class="block px-4 py-2 hover:bg-gray-100">Makanan</a>
              <a href="#" class="block px-4 py-2 hover:bg-gray-100">Minuman</a>
              <a href="#" class="block px-4 py-2 hover:bg-gray-100">Kerajinan</a>
            </div>
          </div>
          <a href="#products" class="hover:text-primary font-medium">Produk</a>
          <a href="#promo" class="hover:text-primary font-medium">Promo dan Diskon</a>
          <a href="#about" class="hover:text-primary font-medium">Tentang Semarak</a>
        </nav>

        <div class="flex items-center space-x-4">
          <button class="relative">
            <i class="fas fa-shopping-cart text-xl"></i>
            <span
              class="absolute -top-2 -right-2 bg-accent text-white rounded-full w-5 h-5 flex items-center justify-center text-xs"
              >{{ cartCount }}</span
            >
          </button>
            <a href="/login" class="bg-primary text-black px-4 py-2 rounded-md hover:bg-blue-600">
              Login
            </a>
            <a href="/register" class="bg-accent text-black px-4 py-2 rounded-md hover:bg-blue-600">
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
      <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center gap-10">
        <!-- Text -->
        <div class="flex-1 space-y-4">
          <h1 class="text-3xl md:text-4xl font-bold text-gray-800">Selamat Datang di Semarak!</h1>
          <p class="text-gray-600">
            Pasar Digital UMKM Karanganyar, Belanja Mudah, Dukung Lokal!!
          </p>
          <button class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-navy-700">
            Lihat Semua Produk →
          </button>
        </div>

        <!-- Image -->
        <div class="flex-1">
          <img src="/images/marketplace.png" alt="Marketplace" class="w-62 h-61 rounded-full object-cover mx-auto" />
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
          Semarak hadir untuk memudahkan anda menemukan produk terbaik dari UMKM Karanganyar, inilah alasan kenapa belanja di SEMARAK adalah pilihan terbaik!
        </p>

        <!-- 4 Features -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
          <div class="space-y-3">
            <div class="text-yellow-400 text-4xl">🚚</div>
            <h3 class="font-semibold">Gratis Ongkir Seluruh Indonesia</h3>
            <p class="text-sm text-gray-600">
              Nikmati belanja tanpa khawatir biaya! Setiap pembelian di Semarak langsung GRATIS ongkos kirim ke seluruh wilayah Indonesia.
            </p>
          </div>

          <div class="space-y-3">
            <div class="text-yellow-400 text-4xl">💰</div>
            <h3 class="font-semibold">Harga Terjangkau, Kualitas Premium</h3>
            <p class="text-sm text-gray-600">
              Dapatkan produk lokal dengan kualitas terbaik dan harga bersahabat. Dukung UMKM tanpa menguras kantong!
            </p>
          </div>

          <div class="space-y-3">
            <div class="text-yellow-400 text-4xl">🛍️</div>
            <h3 class="font-semibold">Dukung Produk Lokal Langsung dari Pengrajin</h3>
            <p class="text-sm text-gray-600">
              Setiap pembelian Anda membantu menggerakkan ekonomi kreatif Karanganyar. Bangga menggunakan produk asli daerah!
            </p>
          </div>

          <div class="space-y-3">
            <div class="text-yellow-400 text-4xl">✅</div>
            <h3 class="font-semibold">Garansi Kepuasan Pelanggan</h3>
            <p class="text-sm text-gray-600">
              Tak perlu ragu! Kami menjamin produk original dan siap membantu jika ada kendala. Belanja aman, hati pun tenang.
            </p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
.hero-bg {
  transition: all 0.3s ease;
}
.category-card:hover {
  transform: translateY(-5px);
  transition: all 0.3s ease;
}
.product-card {
  transition: all 0.3s ease;
}
.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
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
