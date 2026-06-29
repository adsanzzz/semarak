<script setup>
import AppLayout from "@/Layouts/AppLayout.vue"
import { Head, Link } from '@inertiajs/vue3'
import { ref, onMounted } from "vue"

const props = defineProps({
  products: Array
})

/* ================== DATA ULASAN ================== */
const ulasanList = [
  {
    nama: "Maudy",
    alamat: "Jebres, Surakarta",
    komentar: "Produk berkualitas, pengiriman cepat, dan kemasan rapi!",
    image: "/images/woman1.jpg",
  },
  {
    nama: "Almira Jati",
    alamat: "Jebres, Surakarta",
    komentar: "Produk berkualitas, pengiriman cepat, dan kemasan rapi! Senang bisa belanja oleh-oleh khas Karanganyar di SEMARAK. Pasti repeat order!",
    image: "/images/woman2.jpg",
  },
  {
    nama: "Kiky Saputri",
    alamat: "Jebres, Surakarta",
    komentar: "Produk bagus banget, pelayanan ramah dan cepat!",
    image: "/images/woman3.jpg",
  },
]

const currentIndex = ref(1)

const nextUlasan = () => {
  currentIndex.value = (currentIndex.value + 1) % ulasanList.length
}

const prevUlasan = () => {
  currentIndex.value = (currentIndex.value - 1 + ulasanList.length) % ulasanList.length
}

const getPrev = () => {
  return ulasanList[(currentIndex.value - 1 + ulasanList.length) % ulasanList.length]
}

const getNext = () => {
  return ulasanList[(currentIndex.value + 1) % ulasanList.length]
}

onMounted(() => {
  setInterval(() => {
    nextUlasan()
  }, 5000)
})
</script>

<template>
  <Head title="Beranda Belanja - Semarak" />

  <AppLayout>
    <div class="bg-gray-50/50 text-gray-800 font-sans min-h-screen">
      
      <!-- 🚀 Hero Section -->
      <section class="relative bg-gradient-to-br from-[#0A3551] via-indigo-950 to-indigo-900 text-white py-16 md:py-20 overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_left,rgba(99,102,241,0.12),transparent)]"></div>
        <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-12 gap-10 items-center relative z-10">
          
          <!-- Text CTA -->
          <div class="md:col-span-7 space-y-6 text-left">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
              👋 Selamat datang kembali di Semarak!
            </span>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight leading-tight">
              Belanja Mudah, <br />
              <span class="text-yellow-400">Dukung UMKM Lokal!</span>
            </h1>
            <p class="text-xs md:text-sm text-gray-300 leading-relaxed max-w-xl">
              Cari produk lokal terbaik Surakarta & Karanganyar dengan mudah dari gadget Anda. Dukung kelangsungan ekonomi kreatif lokal langsung melalui transaksi Anda hari ini.
            </p>
            <div class="flex flex-wrap gap-4 pt-2">
              <Link
                :href="route('produk.lihat')"
                class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold px-6 py-3 rounded-xl text-xs shadow-md transition-all hover:scale-[1.02] cursor-pointer"
              >
                Eksplorasi Katalog Produk →
              </Link>
            </div>
          </div>

          <!-- Image/Illustration -->
          <div class="md:col-span-5 flex justify-center">
            <div class="relative w-full max-w-[300px] aspect-square rounded-3xl overflow-hidden bg-white/5 border border-white/10 p-4 shadow-xl backdrop-blur-xs">
              <img
                src="/images/marketplace.png"
                alt="Semarak Marketplace"
                class="w-full h-full object-contain animate-float"
              />
            </div>
          </div>

        </div>
      </section>

      <!-- 🛡️ Value Propositions (Why Section) -->
      <section class="py-16 bg-white border-y border-gray-100">
        <div class="max-w-6xl mx-auto px-6 text-center space-y-12">
          <div class="space-y-2">
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">
              Kenapa Belanja di Semarak?
            </h2>
            <p class="text-xs text-gray-500 max-w-lg mx-auto leading-relaxed">
              Semarak hadir untuk memudahkan Anda menemukan produk terbaik dari UMKM Karanganyar secara aman dan efisien.
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            
            <div class="bg-gray-50/50 rounded-2xl p-6 border border-gray-100 text-left space-y-3 hover:shadow-xs transition duration-300">
              <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg">
                🚚
              </div>
              <h3 class="font-bold text-sm text-gray-800">Bebas Ongkir</h3>
              <p class="text-xs text-gray-500 leading-relaxed">
                Nikmati belanja hemat tanpa cemas ongkos kirim. Tersedia potongan dan promo pengiriman khusus wilayah Karanganyar & sekitarnya.
              </p>
            </div>

            <div class="bg-gray-50/50 rounded-2xl p-6 border border-gray-100 text-left space-y-3 hover:shadow-xs transition duration-300">
              <div class="w-10 h-10 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center font-bold text-lg">
                💰
              </div>
              <h3 class="font-bold text-sm text-gray-800">Harga Terbaik</h3>
              <p class="text-xs text-gray-500 leading-relaxed">
                Harga bersahabat dari tangan pertama pengrajin langsung. Kualitas premium dengan jaminan keaslian bahan lokal terpilih.
              </p>
            </div>

            <div class="bg-gray-50/50 rounded-2xl p-6 border border-gray-100 text-left space-y-3 hover:shadow-xs transition duration-300">
              <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                🛍️
              </div>
              <h3 class="font-bold text-sm text-gray-800">Dukungan Lokal</h3>
              <p class="text-xs text-gray-500 leading-relaxed">
                Setiap rupiah yang Anda belanjakan berdampak nyata dalam mendukung keberlangsungan hidup dan kreativitas para pelaku UMKM.
              </p>
            </div>

            <div class="bg-gray-50/50 rounded-2xl p-6 border border-gray-100 text-left space-y-3 hover:shadow-xs transition duration-300">
              <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-lg">
                ✅
              </div>
              <h3 class="font-bold text-sm text-gray-800">Transaksi Aman</h3>
              <p class="text-xs text-gray-500 leading-relaxed">
                Platform pembayaran terverifikasi dengan tim admin responsif yang siap menjamin keamanan berbelanja Anda kapan saja.
              </p>
            </div>

          </div>
        </div>
      </section>

      <!-- 🛍️ Active Products Grid -->
      <section class="py-16 bg-gray-50/50">
        <div class="max-w-6xl mx-auto px-6 text-center space-y-12">
          
          <div class="space-y-2">
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">
              Katalog Produk Pilihan UMKM
            </h2>
            <p class="text-xs text-gray-500 max-w-xl mx-auto leading-relaxed">
              Jelajahi produk-produk UMKM aktif hasil karya lokal yang siap dikirim langsung ke rumah Anda.
            </p>
          </div>

          <div v-if="products.length > 0" class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <Link 
              v-for="p in products" 
              :key="p.id" 
              :href="route('produk.show', p.id)"
              class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-xs hover:shadow-md hover:-translate-y-1 transition duration-300 group flex flex-col justify-between"
            >
              <!-- Thumbnail -->
              <div class="h-44 bg-gray-100 overflow-hidden relative">
                <img 
                  :src="p.image || 'https://via.placeholder.com/300'" 
                  class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                />
                <span class="absolute top-2.5 left-2.5 px-2 py-0.5 bg-indigo-50/90 text-indigo-700 font-bold text-[9px] rounded-md backdrop-blur-xs">
                  {{ p.kategori }}
                </span>
              </div>

              <!-- Product Info -->
              <div class="p-4 space-y-2 flex-1 flex flex-col justify-between text-left">
                <div class="space-y-1">
                  <h4 class="font-bold text-gray-800 text-xs line-clamp-2 leading-relaxed" :title="p.nama">
                    {{ p.nama }}
                  </h4>
                  <p class="text-[10px] text-gray-400">Toko: <span class="font-semibold text-gray-600">{{ p.toko }}</span></p>

                  <div class="flex items-center gap-1 text-[10px] text-gray-400">
                    <span v-if="p.rating" class="text-yellow-500 font-bold">★ {{ p.rating }}</span>
                    <span v-else>Belum diulas</span>
                    <span>•</span>
                    <span>{{ p.terjual }} Terjual</span>
                  </div>
                </div>

                <div class="flex items-end justify-between pt-2">
                  <div class="font-extrabold text-sm text-indigo-600">
                    Rp{{ Number(p.harga).toLocaleString('id-ID') }}
                  </div>
                </div>
              </div>
            </Link>
          </div>

          <div v-else class="py-16 bg-white rounded-3xl border border-gray-100 text-gray-400 text-sm">
            Tidak ada produk UMKM aktif saat ini.
          </div>

          <div class="pt-4">
            <Link 
              :href="route('produk.lihat')"
              class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-3.5 rounded-xl text-xs shadow-sm transition hover:scale-[1.02] cursor-pointer"
            >
              Lihat Seluruh Katalog
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.22 5.08a.75.75 0 1 1 1.06-1.06l5.5 5.5a.75.75 0 0 1 0 1.06l-5.5 5.5a.75.75 0 1 1-1.06-1.06l4.168-4.17H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
              </svg>
            </Link>
          </div>

        </div>
      </section>

      <!-- 💬 Ulasan Pembeli Carousel Section -->
      <section class="py-16 bg-white border-y border-gray-100">
        <div class="max-w-6xl mx-auto px-6 text-center space-y-12">
          
          <div class="space-y-2">
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">
              Apa Kata Pembeli?
            </h2>
            <p class="text-xs text-gray-500 max-w-md mx-auto leading-relaxed">
              Kepuasan Anda adalah kebahagiaan kami. Berikut ulasan nyata dari pelanggan setia Semarak.
            </p>
          </div>

          <!-- Carousel Container -->
          <div class="flex items-center justify-center gap-6 relative">
            
            <!-- LEFT (Prev) -->
            <div class="hidden md:block w-72 bg-gray-50/50 rounded-2xl border border-gray-100 p-5 opacity-40 scale-90 transition duration-500 text-left space-y-3">
              <p class="text-xs text-gray-500 italic leading-relaxed">"{{ getPrev().komentar }}"</p>
              <div class="flex items-center gap-3">
                <img :src="getPrev().image" class="w-9 h-9 rounded-full object-cover border border-gray-100" />
                <div>
                  <h4 class="font-bold text-xs text-gray-800">{{ getPrev().nama }}</h4>
                  <p class="text-[10px] text-gray-400">{{ getPrev().alamat }}</p>
                </div>
              </div>
            </div>

            <!-- ACTIVE (Center) -->
            <div class="w-full max-w-md bg-indigo-50/40 rounded-3xl border border-indigo-100 p-6 md:p-8 transition duration-500 text-left space-y-4 shadow-xs relative">
              <div class="text-yellow-500 text-lg font-bold">★★★★★</div>
              <p class="text-xs md:text-sm text-gray-700 leading-relaxed italic">
                "{{ ulasanList[currentIndex].komentar }}"
              </p>
              
              <div class="flex items-center gap-4 pt-2 border-t border-indigo-100/50">
                <img 
                  :src="ulasanList[currentIndex].image" 
                  class="w-12 h-12 rounded-full object-cover border border-indigo-200" 
                />
                <div>
                  <h4 class="font-bold text-sm text-gray-900">{{ ulasanList[currentIndex].nama }}</h4>
                  <p class="text-xs text-gray-500">{{ ulasanList[currentIndex].alamat }}</p>
                </div>
              </div>
            </div>

            <!-- RIGHT (Next) -->
            <div class="hidden md:block w-72 bg-gray-50/50 rounded-2xl border border-gray-100 p-5 opacity-40 scale-90 transition duration-500 text-left space-y-3">
              <p class="text-xs text-gray-500 italic leading-relaxed">"{{ getNext().komentar }}"</p>
              <div class="flex items-center gap-3">
                <img :src="getNext().image" class="w-9 h-9 rounded-full object-cover border border-gray-100" />
                <div>
                  <h4 class="font-bold text-xs text-gray-800">{{ getNext().nama }}</h4>
                  <p class="text-[10px] text-gray-400">{{ getNext().alamat }}</p>
                </div>
              </div>
            </div>

          </div>

          <!-- Carousel Controls -->
          <div class="flex justify-center gap-3">
            <button 
              @click="prevUlasan" 
              class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-50 text-gray-600 transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
              </svg>
            </button>
            <button 
              @click="nextUlasan" 
              class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-50 text-gray-600 transition cursor-pointer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
              </svg>
            </button>
          </div>

        </div>
      </section>

      <!-- 🌾 Karanganyar Heritage & Culture Panel -->
      <section class="py-16 bg-gray-50/50">
        <div class="max-w-6xl mx-auto px-6">
          <div class="grid md:grid-cols-2 items-stretch gap-10 rounded-3xl overflow-hidden shadow-xs border border-gray-100 bg-white">
            
            <div class="bg-indigo-950 text-white p-8 md:p-12 flex flex-col justify-center text-left space-y-6">
              <span class="text-xs font-bold text-yellow-400 uppercase tracking-widest">Kekayaan Seni Budaya</span>
              <h2 class="text-2xl md:text-3xl font-black leading-tight">
                Keindahan Warisan Lokal, <br />
                Sentuhan Khas Karanganyar
              </h2>
              <p class="text-xs md:text-sm text-gray-300 leading-relaxed">
                Dibuat dengan cinta oleh para pengrajin berbakat, setiap produk kerajinan khas Karanganyar menghadirkan keunikan dan nilai budaya tinggi yang diwariskan turun-temurun. Dari batik tulis nan elegan hingga anyaman rotan yang kokoh dan estetis.
              </p>
              <p class="text-xs md:text-sm text-gray-300 leading-relaxed">
                Mari bersama memajukan ekosistem ekonomi mandiri daerah dengan bangga menggunakan karya asli lokal buatan pengrajin tanah air kita sendiri.
              </p>
            </div>

            <div class="h-64 md:h-auto min-h-[300px]">
              <img 
                src="/images/batik.jpg" 
                alt="Batik Karanganyar" 
                class="w-full h-full object-cover"
              />
            </div>

          </div>
        </div>
      </section>

    </div>
  </AppLayout>
</template>

<style scoped>
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}
.animate-float {
  animation: float 4s ease-in-out infinite;
}
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
