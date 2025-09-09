<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

// Import Heroicons
import {
  HomeIcon,
  ShoppingBagIcon,
  Cog6ToothIcon,
  StarIcon,
  TicketIcon,
  ArrowRightOnRectangleIcon,
  ChevronDownIcon,
  ChevronRightIcon,
} from "@heroicons/vue/24/outline";

const isOpen = ref(true);
const openSubmenu = ref(null); // track submenu terbuka

// Hanya tampil jika user toko
const page = usePage();

// Data menu dinamis sesuai role
const menus = computed(() => {
  if (user.value.role === 3) {
    // Buyer
    return [
      { name: "Dashboard", icon: HomeIcon, route: "/dashboard" },
      { name: "Keranjang Saya", icon: ShoppingBagIcon, route: "/keranjang" },
      { name: "Pesanan Saya", icon: ShoppingBagIcon, route: "/user.pesanan" },
      {
        name: "Pengaduan & Komplain",
        icon: StarIcon,
        route: "/pengaduan",
        children: [
          { name: "Daftar Pengaduan", route: "/pengaduan/submenu" },
          { name: "Live Chat", route: "/pengaduan/chat" },
          { name: "Daftar Komplain", route: "/pengaduan/komplain" },
        ],
      },
      // Tidak ada Kelola Promo
      { name: "Pengaturan", icon: Cog6ToothIcon, route: "/settings" },
    ];
  } else {
    // Toko/admin
    return [
      { name: "Dashboard", icon: HomeIcon, route: "/dashboard" },
      { name: "Kelola Produk", icon: ShoppingBagIcon, route: route('user.products') },
      { name: "Kelola Pesanan", icon: ShoppingBagIcon, route: "/pesanan" },
      {
        name: "Pengaduan & Komplain",
        icon: StarIcon,
        route: "/pengaduan",
        children: [
          { name: "Daftar Pengaduan", route: "/pengaduan/submenu" },
          { name: "Live Chat", route: "/pengaduan/chat" },
          { name: "Daftar Komplain", route: "/pengaduan/komplain" },
        ],
      },
      { name: "Kelola Promo", icon: TicketIcon, route: "/promo" },
      { name: "Pengaturan", icon: Cog6ToothIcon, route: "/settings" },
    ];
  }
});

// Deteksi route aktif
const currentUrl = page.url;
const user = computed(() => page.props.auth?.user || {});
const isToko = computed(() => user.value.role === 2);

// Toggle submenu
const toggleSubmenu = (name) => {
  openSubmenu.value = openSubmenu.value === name ? null : name;
};
</script>

<template>
  <aside
    :class="[
      'h-screen bg-white text-black flex flex-col transition-all duration-300 shadow-2xl',
      isOpen ? 'w-64' : 'w-20'
    ]"
  >
    <!-- Header -->
    <div class="flex items-center justify-between px-4 py-5 border-b border-white/20">
      <div v-show="isOpen" class="flex items-center space-x-2">
        <img
          src="/images/Logomark.png"
          alt="Logo Semarak"
          class="w-8 h-8 object-contain"
        />
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
  <div v-for="menu in menus" :key="menu.name">
        <!-- Jika punya submenu -->
        <div
          v-if="menu.children"
          @click="toggleSubmenu(menu.name)"
          :class="[
            'flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer group',
            currentUrl.startsWith(menu.route)
              ? 'bg-yellow-100 text-black'
              : 'hover:bg-yellow-50'
          ]"
        >
          <div class="flex items-center gap-3">
            <component :is="menu.icon" class="w-6 h-6 text-gray-600" />
            <span v-show="isOpen" class="text-sm font-medium">
              {{ menu.name }}
            </span>
          </div>
          <ChevronDownIcon
            v-if="isOpen && openSubmenu === menu.name"
            class="w-5 h-5 text-gray-600"
          />
          <ChevronRightIcon
            v-else-if="isOpen"
            class="w-5 h-5 text-gray-600"
          />
        </div>

        <!-- Submenu -->
        <transition name="fade">
          <div
            v-if="menu.children && openSubmenu === menu.name"
            class="ml-10 mt-1 space-y-1"
          >
            <Link
              v-for="child in menu.children"
              :key="child.name"
              :href="child.route"
              :class="[
                'block px-3 py-2 rounded-lg text-sm transition',
                currentUrl.startsWith(child.route)
                  ? 'bg-yellow-200 text-black'
                  : 'text-gray-600 hover:bg-yellow-100'
              ]"
            >
              {{ child.name }}
            </Link>
          </div>
        </transition>

        <!-- Jika menu biasa -->
        <Link
          v-if="!menu.children"
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
            :class="currentUrl.startsWith(menu.route)
              ? 'text-white'
              : 'text-black group-hover:text-yellow-600'"
          />
          <span v-show="isOpen" class="text-sm font-medium">
            {{ menu.name }}
          </span>
        </Link>
      </div>
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
