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

        <form v-if="showSearch" class="relative" @submit.prevent="submitSearch">
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Cari produk.."
            class="border rounded-md pl-10 pr-4 py-2 w-64 focus:ring-2 focus:ring-blue-500 focus:outline-none"
          />
          <button type="submit" class="absolute left-3 top-2.5 text-gray-400" aria-label="Cari produk">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
            </svg>
          </button>
        </form>

        <a :href="route('tentang.semarak')" class="hover:text-primary font-medium">
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
import { computed, ref, watch } from "vue"
import { router, usePage } from '@inertiajs/vue3'

const cartCount = ref(0)
const page = usePage()
const searchTerm = ref(page.props.search || '')
const showSearch = computed(() => route().current('produk.lihat'))

watch(
  () => page.props.search,
  (nextSearch) => {
    searchTerm.value = nextSearch || ''
  },
  { immediate: true }
)

function submitSearch() {
  const keyword = (searchTerm.value || '').trim()

  if (!keyword) {
    router.get(route('produk.lihat'), {}, { preserveState: false, replace: true })
    return
  }

  router.get(route('produk.lihat'), { q: keyword }, { preserveState: false, replace: true })
}
</script>
