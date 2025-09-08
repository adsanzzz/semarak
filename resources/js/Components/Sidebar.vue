<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";

// Import Heroicons
import {
  HomeIcon,
  ShoppingBagIcon,
  Squares2X2Icon,
  Cog6ToothIcon,
  ArrowRightOnRectangleIcon,
} from "@heroicons/vue/24/outline";

const isOpen = ref(true);

const menus = [
  { name: "Dashboard", icon: HomeIcon, route: "/dashboard" },
  { name: "Produk", icon: ShoppingBagIcon, route: "/products" },
  { name: "Kategori", icon: Squares2X2Icon, route: "/categories" },
  { name: "Toko Saya", icon: Cog6ToothIcon, route: "/toko" },
  { name: "Pengaturan", icon: Cog6ToothIcon, route: "/settings" },
];
</script>

<template>
  <aside
    :class="[
      'h-screen bg-gradient-to-b from-indigo-600 to-purple-800 text-white flex flex-col transition-all duration-300 shadow-2xl',
      isOpen ? 'w-64' : 'w-20'
    ]"
  >
    <!-- Logo / Header -->
    <div class="flex items-center justify-between px-4 py-5 border-b border-white/20">
      <h1
        v-show="isOpen"
        class="text-2xl font-bold tracking-wide bg-clip-text text-transparent bg-gradient-to-r from-pink-300 to-yellow-300 whitespace-nowrap"
      >
        SEMARAK
      </h1>
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
        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:bg-white/20"
      >
        <component
          :is="menu.icon"
          class="w-6 h-6 group-hover:text-yellow-300 transition-colors"
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
