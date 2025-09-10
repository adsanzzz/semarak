<template>
  <header class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
      <!-- Logo -->
      <div class="flex items-center space-x-2">
        <img src="/images/Logomark.png" alt="Semarak" class="h-8 w-8" />
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

        <!-- Ikon -->
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
</template>

<script setup>
import {
  ShoppingCartIcon,
  BellIcon,
  ChatBubbleOvalLeftEllipsisIcon,
  UserIcon,
} from "@heroicons/vue/24/outline"

const menus = [
  { name: "Dashboard", route: "/dashboard" },
  { name: "Produk", route: "/produk" },
  { name: "Transaksi", route: "/transaksi" },
]
</script>
