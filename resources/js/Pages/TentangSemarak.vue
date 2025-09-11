<template>
	<div>
		<NavbarAuth />
		<div class="bg-white text-gray-800">
    <!-- Breadcrumb -->
    <div class="bg-gray-50 border-b">
      <div class="max-w-6xl mx-auto px-4 py-4">
        <nav class="text-sm text-gray-500">
          <ol class="flex items-center space-x-2">
            <li>
              <a href="/" class="hover:text-gray-700">Beranda</a>
            </li>
            <li>/</li>
            <li class="text-gray-700 font-medium">Tentang Semarak</li>
          </ol>
        </nav>
        <h1 class="text-xl font-semibold text-gray-800 mt-2">Tentang Semarak</h1>
      </div>
    </div>

    <!-- Content -->
    <div class="max-w-6xl mx-auto px-4 py-12 space-y-16">
      <!-- Apa Itu Semarak -->
      <div class="grid md:grid-cols-2 gap-10 items-center">
        <!-- Illustration -->
        <div class="flex justify-center">
          <img
            src="images/yellow1.png"
            alt="Apa Itu Semarak"
            class="w-80 h-auto"
          />
        </div>
        <!-- Text -->
        <div>
          <h2 class="text-2xl font-bold text-gray-900 mb-4">Apa Itu SEMARAK?</h2>
          <p class="text-gray-600 leading-relaxed">
            SEMARAK adalah marketplace berbasis web yang didedikasikan khusus untuk produk-produk UMKM lokal Kabupaten Karanganyar.
            Dikembangkan oleh Dinas Komunikasi dan Informatika (Diskominfo) Karanganyar, SEMARAK hadir sebagai solusi digital
            untuk meningkatkan daya saing pelaku usaha serta mempermudah masyarakat dalam menemukan dan membeli produk unggulan daerah.
          </p>
        </div>
      </div>

      <!-- Kenapa Harus Semarak -->
      <div class="grid md:grid-cols-2 gap-10 items-center">
        <!-- Text -->
        <div>
          <h2 class="text-2xl font-bold text-gray-900 mb-4">Kenapa Harus SEMARAK?</h2>
          <p class="text-gray-600 leading-relaxed mb-4">
            Dengan SEMARAK, UMKM Karanganyar mendapatkan akses lebih luas ke pasar digital, sementara masyarakat bisa berbelanja produk lokal berkualitas dengan mudah, cepat, dan aman.
          </p>
          <p class="text-yellow-600 font-medium flex items-center gap-2">
            💡 Dukung produk lokal, majukan ekonomi daerah!
          </p>
        </div>
        <!-- Illustration -->
        <div class="flex justify-center">
          <img
            src="images/yellow2.png"
            alt="Kenapa Harus Semarak"
            class="w-80 h-auto"
          />
        </div>
      </div>
    </div>
  </div>
	</div>
</template>

<script setup>
import NavbarAuth from '@/Components/NavbarAuth.vue';
</script>
