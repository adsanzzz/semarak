<template>
	<div>
		<NavbarAuth />
		<div class="max-w-3xl mx-auto py-16 px-6">
			<h1 class="text-2xl font-bold mb-4 text-blue-700">Tentang Semarak</h1>
			<div class="bg-white rounded-xl shadow p-6">
				<p class="text-gray-700 mb-2">
					Semarak adalah marketplace UMKM Karanganyar yang bertujuan membantu pelaku usaha lokal memasarkan produk secara online dengan mudah dan aman.
				</p>
				<ul class="list-disc ml-6 text-gray-600 mb-4">
					<li>Memberdayakan UMKM lokal</li>
					<li>Transaksi aman dan transparan</li>
					<li>Beragam produk unggulan Karanganyar</li>
				</ul>
				<div class="text-sm text-gray-400">&copy; 2025 Semarak Karanganyar</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import NavbarAuth from '@/Components/NavbarAuth.vue';
</script>
