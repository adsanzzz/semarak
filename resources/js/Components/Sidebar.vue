<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

// Import Heroicons
import {
  HomeIcon,
  ShoppingBagIcon,
  ArchiveBoxIcon,
  ShoppingCartIcon,
  Cog6ToothIcon,
  StarIcon,
  TicketIcon,
  ArrowRightOnRectangleIcon,
  ChevronDownIcon,
  ChevronRightIcon,
} from "@heroicons/vue/24/outline";

// Sidebar selalu terbuka, tidak bisa di-minimize
const isOpen = ref(true);
const openSubmenu = ref(null); // track submenu terbuka

// Hanya tampil jika user toko
const page = usePage();
const user = computed(() => page.props.auth?.user || {});
const userRole = computed(() => Number(user.value.role || 0));

// Data menu dinamis sesuai role
const menus = computed(() => {
  if (userRole.value === 3) {
    // Buyer
    return [
      { name: "Pesanan Saya", icon: ShoppingBagIcon, route: route('user.riwayat-pemesanan') },
      { name: "Keranjang Saya", icon: ShoppingCartIcon, route: "/keranjang" },
      { name: "Kelola Pengaduan & Komplain", icon: StarIcon, route: route('pengaduan') },
    ];
  } else if (userRole.value === 1) {
    // Admin
    return [
      { name: "Dashboard", icon: HomeIcon, route: route('admin.dashboard') },
      { name: "Kelola Kategori", icon: TicketIcon, route: route('admin.categories.index') },
      { name: "Kelola Produk", icon: ArchiveBoxIcon, route: route('admin.products') },
      { name: "Kelola Pesanan", icon: ShoppingBagIcon, route: route('admin.orders') },
      { name: "Sertifikasi", icon: TicketIcon, route: route('admin.sertifikasi.index') },
      { name: "Kelola Akun", icon: Cog6ToothIcon, route: route('admin.users.index') },
      { name: "Kelola Pengaduan Komplain", icon: StarIcon, route: route('admin.komplain') },
    ];
    } else {
    // Toko
    return [  
      { name: "Dashboard", icon: HomeIcon, route: "/dashboard" },
      { name: "Kelola Produk", icon: ShoppingBagIcon, route: route('user.products') },
      { name: "Kelola Pesanan", icon: ShoppingBagIcon, route: route('user.orders') },
      { name: "Ulasan", icon: StarIcon, route: route('user.reviews') },
      { name: "Chat", icon: TicketIcon, route: route('chat.index') },
      { name: "Kelola Pengaduan & Komplain", icon: StarIcon, route: route('pengaduan') },
    ];
  }
});

// Deteksi route aktif
const currentUrl = page.url;
const isToko = computed(() => userRole.value === 2);
const chatUnreadCount = computed(() => page.props.chat_unread_count || 0);

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
  <!-- Tombol minimize dihilangkan, sidebar selalu terbuka -->
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
            'relative flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group',
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

          <span
            v-if="menu.name === 'Chat' && chatUnreadCount > 0"
            class="absolute top-2 right-3 min-w-[18px] h-[18px] px-1 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center"
          >
            {{ chatUnreadCount > 99 ? '99+' : chatUnreadCount }}
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
