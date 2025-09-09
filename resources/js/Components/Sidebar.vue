<script setup>
import { ref } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

// Import Heroicons
import {
  HomeIcon,
  ShoppingBagIcon,
  Squares2X2Icon,
  Cog6ToothIcon,
  ArrowRightOnRectangleIcon,
} from "@heroicons/vue/24/outline";

const isOpen = ref(true);

// Data menu
const menus = [
  { name: "Dashboard", icon: HomeIcon, route: "/dashboard" },
  { name: "Produk", icon: ShoppingBagIcon, route: "/products" },
  { name: "Kategori", icon: Squares2X2Icon, route: "/categories" },
  { name: "Toko Saya", icon: Cog6ToothIcon, route: "/toko" },
  { name: "Pengaturan", icon: Cog6ToothIcon, route: "/settings" },
];

// Untuk deteksi route aktif
const page = usePage();
const currentUrl = page.url; // URL saat ini
</script>

<template>
  <aside
    :class="[
      'h-screen bg-white text-black flex flex-col transition-all duration-300 shadow-2xl',
      isOpen ? 'w-64' : 'w-20'
    ]"
  >
    <!-- Logo / Header -->
    <div class="flex items-center justify-between px-4 py-5 border-b border-white/20">
      <div v-show="isOpen" class="flex items-center space-x-2">
        <img
          src="/images/Logomark.png"
          alt="Logo Semarak"
          class="w-8 h-8 object-contain"
        />

        <!-- Teks SEMARAK -->
        <h1 class="text-2xl font-bold tracking-wide text-[#0A3551] whitespace-nowrap font-sans">
          Semarak
        </h1>
      </div>

      <button
        class="p-2 rounded-full hover:bg-white/20 transition cursor-pointer"
        @click="isOpen = !isOpen"
      >
        <span v-if="isOpen">◀</span>
        <span v-else>▶</span>
      </button>
    </div>

    <!-- Menu -->
    <nav class="flex-1 px-2 py-4 space-y-2 overflow-y-auto">
      <Link
        v-for="menu in menus"
        :key="menu.name"
        :href="menu.route"
        :class="[
          'flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group',
          currentUrl.startsWith(menu.route) 
            ? 'bg-yellow-300 text-white' 
            : 'hover:bg-yellow-100'
        ]"
      >
        <component
          :is="menu.icon"
          class="w-6 h-6 transition-colors"
          :class="currentUrl.startsWith(menu.route) ? 'text-white' : 'text-black group-hover:text-yellow-600'"
        />
        <span
          v-show="isOpen"
          class="text-sm font-medium transition-all duration-300"
        >
          {{ menu.name }}
        </span>
      </Link>
    </nav>

    <!-- Footer -->
    <div class="px-4 py-4 border-t border-white/20">
      <Link
        :href="route('logout')"
        method="post"
        as="button"
        class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-500/30 transition-all w-full"
      >
        <ArrowRightOnRectangleIcon class="w-6 h-6" />
        <span v-show="isOpen" class="text-sm font-medium">Logout</span>
      </Link>
    </div>
  </aside>
</template>
