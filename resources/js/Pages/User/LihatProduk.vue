<script setup>
import { ref, computed } from "vue";
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

const page = usePage()
const isAuthenticated = computed(() => !!page.props.auth?.user)

// Produk dari props Inertia
const produkList = page.props.produkList || [];

/* 🔔 Notifikasi kecil */
const notif = ref(null)
function showNotif(message, type = 'success') {
  notif.value = { message, type }
  setTimeout(() => (notif.value = null), 2500)
}

function tambahKeKeranjang(produkId) {
  if (!isAuthenticated.value) {
    router.get(route('login'))
    return
  }

  router.post('/keranjang', { product_id: produkId }, {
    preserveScroll: true,
    onSuccess: () => {
      showNotif('Ditambahkan ke Keranjang', 'success')
    },
    onError: () => {
      showNotif('Gagal menambahkan ke keranjang', 'error')
    }
  });
}

function viewProduk(id) {
  if (!isAuthenticated.value) {
    router.get(route('login'))
    return
  }
  router.get('/produk/' + id)
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
        </aside>

        <!-- Main Content -->
        <main class="flex-1">
          <!-- Notifikasi -->
<transition name="fade">
  <div
    v-if="notif"
    class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow"
  >
    {{ notif.message }}
  </div>
</transition>
          <!-- Grid Produk -->
          <div class="grid md:grid-cols-3 gap-6">
            <div
  v-for="produk in produkList"
  :key="produk.id"
  @click="viewProduk(produk.id)"
  class="bg-white rounded-xl shadow hover:shadow-lg transition p-4 cursor-pointer"
>
              <img
                :src="produk.image"
                alt="produk.nama"
                class="w-full h-40 object-cover rounded-md mb-3"
              />

              <div class="space-y-1">
                <div class="flex items-center text-yellow-400 text-sm">
                  ★ {{ produk.rating }}
                  <span class="text-gray-500 ml-2 text-xs">
                    {{ produk.terjual }} Terjual
                  </span>
                </div>

                <h3 class="font-semibold text-gray-800 text-sm line-clamp-2">
                  {{ produk.nama }}
                </h3>

                <p class="text-xs text-gray-500">Toko {{ produk.toko }}</p>
                <p class="text-xs text-gray-400">📍 {{ produk.jarak }}</p>

                <span
                  class="inline-block text-xs font-semibold px-3 py-1 bg-gray-100 text-gray-600 rounded-full"
                >
                  {{ produk.kategori }}
                </span>

                <div>
                  <p class="text-xs text-gray-400 line-through">
                    {{ produk.hargaCoret }}
                  </p>
                  <p class="text-blue-600 font-bold">{{ produk.harga }}</p>
                </div>
              </div>

              <button
  class="mt-3 w-full bg-yellow-400 hover:bg-yellow-500 text-white font-medium py-2 rounded flex items-center justify-center gap-2"
  @click.stop="tambahKeKeranjang(produk.id)"
>
  Tambah ke Keranjang
  <i class="fas fa-shopping-cart"></i>
</button>
            </div>
          </div>

          <!-- Info jumlah produk (opsional kalau mau nanti) -->
          <!--
          <div class="mt-6 text-sm text-gray-500 text-center">
            Menampilkan {{ produkList.length }} produk
          </div>
          -->
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
