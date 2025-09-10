<template>
  <header class="sticky top-0 z-50 bg-white shadow-md">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
      <!-- Logo -->
      <div class="flex items-center">
        <a href="/" class="text-2xl font-bold text-primary">SEMARAK</a>
      </div>

      <!-- Menu -->
      <nav
        id="navMenu"
        class="hidden md:flex space-x-8 transition-all duration-300"
      >
        <a href="/" class="hover:text-primary font-medium">Beranda</a>

        <div class="relative group">
          <button class="hover:text-blue-600">Kategori ▾</button>
          <div
            class="absolute hidden group-hover:block bg-white shadow rounded mt-2 py-2 w-40"
          >
            <a href="#" class="block px-4 py-2 hover:bg-gray-100">Makanan</a>
            <a href="#" class="block px-4 py-2 hover:bg-gray-100">Minuman</a>
            <a href="#" class="block px-4 py-2 hover:bg-gray-100">Kerajinan</a>
          </div>
        </div>

        <a href="#products" class="hover:text-primary font-medium">Produk</a>
        <a href="#promo" class="hover:text-primary font-medium">
          Promo dan Diskon
        </a>
        <a href="#about" class="hover:text-primary font-medium">
          Tentang Semarak
        </a>
      </nav>

      <!-- Action -->
      <div class="flex items-center space-x-4">
        <button class="relative">
          <i class="fas fa-shopping-cart text-xl"></i>
          <span
            class="absolute -top-2 -right-2 bg-accent text-white rounded-full w-5 h-5 flex items-center justify-center text-xs"
          >
            {{ cartCount }}
          </span>
        </button>

        <a
          href="/login"
          class="bg-primary text-black px-4 py-2 rounded-md hover:bg-blue-600"
        >
          Login
        </a>
        <a
          href="/register-type"
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
</template>

<script setup>
import { ref } from "vue"

const cartCount = ref(0)
</script>
