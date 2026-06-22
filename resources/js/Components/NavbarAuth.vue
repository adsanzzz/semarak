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
        <Link
          v-for="menu in menus"
          :key="menu.name"
          :href="menu.route"
          class="text-gray-700 hover:text-blue-600"
        >
          {{ menu.name }}
        </Link>
      </nav>

      <!-- Search + Icons -->
      <div class="flex items-center space-x-5">
        <form class="relative" @submit.prevent="submitSearch">
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Cari produk.."
            class="border rounded-md pl-10 pr-4 py-2 w-64 focus:ring-2 focus:ring-blue-500 focus:outline-none"
          />
          <button
            type="submit"
            class="absolute left-3 top-2.5 text-gray-400"
            aria-label="Cari produk"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="w-5 h-5"
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
          </button>
        </form>

        <!-- Ikon -->
        <Link :href="route('keranjang.index')">
  <ShoppingCartIcon class="w-6 h-6 text-gray-600 hover:text-blue-600 cursor-pointer" />
</Link>

        <Link :href="route('user.riwayat-pemesanan')">
          <BellIcon class="w-6 h-6 text-gray-600 hover:text-blue-600 cursor-pointer" />
        </Link>
        <Link :href="route('chat.index')" class="relative">
          <ChatBubbleOvalLeftEllipsisIcon class="w-6 h-6 text-gray-600 hover:text-blue-600 cursor-pointer" />
          <span
            v-if="chatUnreadCount > 0"
            class="absolute -top-2 -right-2 min-w-[18px] h-[18px] px-1 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center"
          >
            {{ chatUnreadCount > 99 ? '99+' : chatUnreadCount }}
          </span>
        </Link>

        <!-- User Dropdown -->
        <div class="relative">
          <button @click="toggleDropdown" class="focus:outline-none">
            <UserIcon class="w-6 h-6 text-gray-600 hover:text-blue-600 cursor-pointer" />
          </button>
          <div v-if="showDropdown" class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border z-50">
            <div class="px-4 py-3 border-b">
              <div class="font-semibold text-gray-800">{{ user.name }}</div>
              <div class="text-sm text-gray-500">{{ user.email }}</div>
            </div>
            <button @click="logout" class="w-full text-left px-4 py-3 text-red-600 hover:bg-gray-100">Logout</button>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, watch } from 'vue';
import { router, usePage, Link } from '@inertiajs/vue3'
import {
  ShoppingCartIcon,
  BellIcon,
  ChatBubbleOvalLeftEllipsisIcon,
  UserIcon,
} from "@heroicons/vue/24/outline"

const menus = [
  { name: "Dashboard", route: "/dashboard" },
  { name: "Produk", route: "/produk/lihat" },
  { name: "Tentang Semarak", route: "/tentang-semarak" },
]

const showDropdown = ref(false)
const page = usePage()
const user = page.props.auth.user
const searchTerm = ref(page.props.search || '')

watch(
  () => page.props.search,
  (nextSearch) => {
    searchTerm.value = nextSearch || ''
  },
  { immediate: true }
)

function logout() {
  router.post(route('logout'))
}
function toggleDropdown() {
  showDropdown.value = !showDropdown.value
}

function submitSearch() {
  const keyword = (searchTerm.value || '').trim()

  if (!keyword) {
    router.get(route('produk.lihat'), {}, { preserveState: false, replace: true })
    return
  }

  router.get(route('produk.lihat'), { q: keyword }, { preserveState: false, replace: true })
}
</script>
