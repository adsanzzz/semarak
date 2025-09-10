<template>
  <div class="min-h-screen flex flex-col">
    <!-- Navbar khusus user login -->
    <NavbarAuth />

    <!-- Konten halaman -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer (opsional) -->
    <footer class="bg-gray-100 py-4 text-center text-sm text-gray-500">
      &copy; 2025 Semarak. All rights reserved.
    </footer>
  </div>
</template>

<script setup>
import NavbarAuth from "@/Components/NavbarAuth.vue"
</script>
