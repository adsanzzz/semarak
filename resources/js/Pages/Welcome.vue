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
          <a href="#categories" class="hover:text-primary font-medium">Kategori</a>
          <a href="#products" class="hover:text-primary font-medium">Produk</a>
          <a href="#promo" class="hover:text-primary font-medium">Promo</a>
          <a href="#about" class="hover:text-primary font-medium">Tentang Kami</a>
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

    <!-- Hero -->
  <section class="bg-white py-20">
  <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
    <!-- Kiri: Teks -->
    <div class="text-left md:text-left">
      <h1 class="text-3xl md:text-4xl font-bold mb-4 text-gray-900">
        Selamat Datang di Semarak
      </h1>
      <p class="text-lg text-gray-600 mb-6">
        Pasar Digital UMKM Karanganyar, Belanja Mudah, Dukung Lokal!
      </p>
      <a
        href="#products"
        class="inline-block bg-primary hover:bg-blue-600 text-white font-medium py-3 px-6 rounded-md shadow-md transition"
      >
        Lihat Produk Populer →
      </a>
    </div>

    <!-- Kanan: Gambar ilustrasi -->
    <div class="flex justify-center">
      <img
        src=""
        alt="Marketplace Illustration"
        class="max-w-md w-full"
      />
    </div>
  </div>
</section>

    <!-- Kategori -->
    <section id="categories" class="py-16 bg-white">
      <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">Kategori Populer</h2>
        <!-- isi kategori -->
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
