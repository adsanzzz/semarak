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

// Hanya tampil jika user toko
const page = usePage();
const openSubmenu = ref(
  page.url.startsWith('/admin/products') 
    ? "Kelola Produk" 
    : (page.url.startsWith('/admin/categories') || page.url.startsWith('/admin/satuans')
      ? "Kelola Kategori"
      : (page.url.startsWith('/admin/komplain')
        ? "Kelola Pengaduan"
        : (page.url.startsWith('/admin/users')
          ? "Kelola Akun"
          : (page.url.startsWith('/admin/appeals')
            ? "Pengajuan Banding"
            : null))))
); // track submenu terbuka
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
      {
        name: "Kelola Kategori",
        icon: TicketIcon,
        route: "/admin/categories",
        children: [
          { name: "Category - Sub Category", route: route('admin.categories.index') },
          { name: "Satuan Produk", route: route('admin.satuans.index') },
        ]
      },
      {
        name: "Kelola Produk",
        icon: ArchiveBoxIcon,
        route: "/admin/products",
        children: [
          { name: "Daftar Produk", route: route('admin.products') },
          { name: "Laporan Produk", route: route('admin.products.reports') },
        ]
      },
      { name: "Kelola Pesanan", icon: ShoppingBagIcon, route: route('admin.orders') },
      { name: "Filter Peninjauan", icon: TicketIcon, route: route('admin.moderation.index') },
      {
        name: "Kelola Akun",
        icon: Cog6ToothIcon,
        route: "/admin/users",
        children: [
          { name: "Daftar Akun", route: route('admin.users.index') },
          { name: "Laporan Akun", route: route('admin.users.reported') },
        ]
      },
      {
        name: "Kelola Pengaduan",
        icon: StarIcon,
        route: "/admin/komplain",
        children: [
          { name: "Komplain Pembeli", route: route('admin.komplain.buyer') },
          { name: "Komplain Penjual", route: route('admin.komplain.seller') },
        ]
      },
      {
        name: "Pengajuan Banding",
        icon: ArchiveBoxIcon,
        route: "/admin/appeals",
        children: [
          { name: "Banding Produk", route: route('admin.appeals.product') },
          { name: "Banding Akun", route: route('admin.appeals.account') },
        ]
      },
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
const pendingAppealsCount = computed(() => page.props.pending_appeals_count || 0);
const newComplaintsCount = computed(() => page.props.new_complaints_count || 0);
const reportedAccountsCount = computed(() => page.props.reported_accounts_count || 0);

const pendingProductAppealsCount = computed(() => page.props.pending_product_appeals_count || 0);
const pendingAccountAppealsCount = computed(() => page.props.pending_account_appeals_count || 0);
const newBuyerComplaintsCount = computed(() => page.props.new_buyer_complaints_count || 0);
const newSellerComplaintsCount = computed(() => page.props.new_seller_complaints_count || 0);

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
            <span
              v-if="menu.name === 'Kelola Pengaduan' && newComplaintsCount > 0"
              class="min-w-[18px] h-[18px] px-1.5 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center ml-2"
            >
              {{ newComplaintsCount }}
            </span>
            <span
              v-if="menu.name === 'Pengajuan Banding' && pendingAppealsCount > 0"
              class="min-w-[18px] h-[18px] px-1.5 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center ml-2"
            >
              {{ pendingAppealsCount }}
            </span>
            <span
              v-if="menu.name === 'Kelola Akun' && reportedAccountsCount > 0"
              class="min-w-[18px] h-[18px] px-1.5 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center ml-2"
            >
              {{ reportedAccountsCount }}
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
                  'flex items-center justify-between px-3 py-2 rounded-lg text-sm transition',
                currentUrl.startsWith(child.route)
                    ? 'bg-yellow-200 text-black font-bold'
                    : 'text-gray-600 hover:bg-yellow-100'
              ]"
            >
              <span>{{ child.name }}</span>
              <span
                v-if="child.name === 'Komplain Pembeli' && newBuyerComplaintsCount > 0"
                class="min-w-[16px] h-[16px] px-1 rounded-full bg-red-500 text-white text-[9px] font-bold flex items-center justify-center"
              >
                {{ newBuyerComplaintsCount }}
              </span>
              <span
                v-if="child.name === 'Komplain Penjual' && newSellerComplaintsCount > 0"
                class="min-w-[16px] h-[16px] px-1 rounded-full bg-red-500 text-white text-[9px] font-bold flex items-center justify-center"
              >
                {{ newSellerComplaintsCount }}
              </span>
              <span
                v-if="child.name === 'Banding Produk' && pendingProductAppealsCount > 0"
                class="min-w-[16px] h-[16px] px-1 rounded-full bg-red-500 text-white text-[9px] font-bold flex items-center justify-center"
              >
                {{ pendingProductAppealsCount }}
              </span>
              <span
                v-if="child.name === 'Banding Akun' && pendingAccountAppealsCount > 0"
                class="min-w-[16px] h-[16px] px-1 rounded-full bg-red-500 text-white text-[9px] font-bold flex items-center justify-center"
              >
                {{ pendingAccountAppealsCount }}
              </span>
              <span
                v-if="child.name === 'Laporan Akun' && reportedAccountsCount > 0"
                class="min-w-[16px] h-[16px] px-1 rounded-full bg-red-500 text-white text-[9px] font-bold flex items-center justify-center"
              >
                {{ reportedAccountsCount }}
              </span>
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
