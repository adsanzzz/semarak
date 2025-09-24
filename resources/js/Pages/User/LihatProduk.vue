<script setup>
import { ref } from "vue";
import { usePage, router } from '@inertiajs/vue3';
import NavbarAuth from "@/Components/NavbarAuth.vue";

// Filter
const kategori = ref([
  "Makanan & Minuman",
  "Fashion",
  "Kerajinan",
  "Aksesoris",
  "Makeup & Skincare",
  "Lainnya",
]);

const selectedKategori = ref([]);
const jarak = ref(10);
const harga = ref(300000);

// Produk dari props Inertia
const produkList = usePage().props.produkList || [];

function tambahKeKeranjang(produkId) {
  router.post('/keranjang', { product_id: produkId }, {
    preserveScroll: true,
    onSuccess: () => {
      // opsional: tampilkan notifikasi
    }
  });
}
</script>

<template>
  <div>
    <NavbarAuth />
    <div class="max-w-7xl mx-auto px-6 py-10 font-sans">
    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-500 mb-6">
      <span>Beranda</span> <span class="mx-2">›</span>
      <span class="font-medium text-gray-700">Semua Produk</span>
    </nav>

    <div class="flex gap-8">
      <!-- Sidebar Filter -->
      <aside class="w-64 space-y-6">
        <div>
          <h3 class="font-semibold text-gray-800 mb-2">Kategori</h3>
          <div class="space-y-2">
            <label
              v-for="item in kategori"
              :key="item"
              class="flex items-center space-x-2 text-gray-600"
            >
              <input type="checkbox" v-model="selectedKategori" :value="item" />
              <span>{{ item }}</span>
            </label>
          </div>
        </div>

        <div>
          <h3 class="font-semibold text-gray-800 mb-2">Batas Jarak</h3>
          <input
            type="range"
            min="1"
            max="50"
            step="1"
            v-model="jarak"
            class="w-full"
          />
          <p class="text-sm text-gray-600 mt-1">{{ jarak }} KM</p>
        </div>

        <div>
          <h3 class="font-semibold text-gray-800 mb-2">Batas Harga</h3>
          <input
            type="range"
            min="1000"
            max="1000000"
            step="1000"
            v-model="harga"
            class="w-full"
          />
          <p class="text-sm text-gray-600 mt-1">Rp {{ harga.toLocaleString() }}</p>
        </div>
      </aside>

      <!-- Main Content -->
      <main class="flex-1">
        <div class="flex justify-between items-center mb-6">
          <div class="text-gray-600 text-sm">
            Menampilkan 1-{{ produkList.length }} dari 36 Hasil
          </div>
          <div>
            <button class="text-gray-700 text-sm flex items-center space-x-1">
              <span>Urutkan Berdasarkan</span>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 9l-7 7-7-7"
                />
              </svg>
            </button>
          </div>
        </div>

        <!-- Grid Produk -->
        <div class="grid md:grid-cols-3 gap-6">
          <div
            v-for="produk in produkList"
            :key="produk.id"
            class="bg-white rounded-xl shadow hover:shadow-lg transition p-4 relative"
          >
            <img
              :src="produk.image"
              alt="produk.nama"
              class="w-full h-40 object-cover rounded-md mb-3"
            />
            <!-- Favorit -->
            <button
              class="absolute top-3 right-3 bg-white rounded-full p-2 shadow"
            >
              ♥
            </button>
            <div class="space-y-1">
              <div class="flex items-center text-yellow-400 text-sm">
                ★ {{ produk.rating }}
                <span class="text-gray-500 ml-2 text-xs"
                  >{{ produk.terjual }} Terjual</span
                >
              </div>
              <h3 class="font-semibold text-gray-800 text-sm line-clamp-2">
                {{ produk.nama }}
              </h3>
              <p class="text-xs text-gray-500">Toko {{ produk.toko }}</p>
              <p class="text-xs text-gray-400">📍 {{ produk.jarak }}</p>

              <span
                class="inline-block text-xs font-semibold px-3 py-1 bg-gray-100 text-gray-600 rounded-full"
                >{{ produk.kategori }}</span
              >

              <div>
                <p class="text-xs text-gray-400 line-through">
                  {{ produk.hargaCoret }}
                </p>
                <p class="text-blue-600 font-bold">{{ produk.harga }}</p>
              </div>
            </div>
            <button
              class="mt-3 w-full bg-yellow-400 hover:bg-yellow-500 text-white font-medium py-2 rounded flex items-center justify-center gap-2"
              @click="tambahKeKeranjang(produk.id)"
            >
              Tambah ke Keranjang
              <i class="fas fa-shopping-cart"></i>
            </button>
          </div>
        </div>
      </main>
    </div>
    </div>
  </div>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
