<template>
  <div>
    <!-- Navbar -->
    <NavbarAuth />

    <!-- Konten utama -->
    <div class="max-w-7xl mx-auto p-6">
      <!-- Judul -->
      <h1 class="text-2xl font-bold mb-6">Keranjang Belanja</h1>

      <!-- Jika keranjang kosong -->
      <div v-if="items.length === 0" class="text-gray-500 text-center py-10">
        Keranjang kamu masih kosong 🛒
      </div>

      <!-- Tabel keranjang -->
      <div v-else class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full border border-gray-200">
          <thead class="bg-gray-100 border-b">
            <tr>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Produk</th>
              <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Jumlah</th>
              <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Harga</th>
              <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Subtotal</th>
              <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(item, index) in items"
              :key="index"
              class="border-b hover:bg-gray-50"
            >
              <td class="px-4 py-3 flex items-center space-x-3">
                <img :src="item.image" alt="produk" class="w-12 h-12 rounded" />
                <span>{{ item.name }}</span>
              </td>
              <td class="px-4 py-3 text-center">
                <input
                  type="number"
                  v-model.number="item.qty"
                  class="w-16 border rounded-md text-center"
                  min="1"
                />
              </td>
              <td class="px-4 py-3 text-center">Rp {{ formatCurrency(item.price) }}</td>
              <td class="px-4 py-3 text-center font-semibold">
                Rp {{ formatCurrency(item.price * item.qty) }}
              </td>
              <td class="px-4 py-3 text-center">
                <button
                  @click="removeItem(index)"
                  class="text-red-500 hover:text-red-700"
                >
                  Hapus
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Total & Checkout -->
      <div v-if="items.length > 0" class="mt-6 flex justify-between items-center">
        <div class="text-lg font-semibold">
          Total: Rp {{ formatCurrency(totalPrice) }}
        </div>
        <button
          class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700"
        >
          Checkout
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue"
import NavbarAuth from "@/Components/NavbarAuth.vue" // ✅ import navbar auth

const items = ref([
  {
    name: "Produk A",
    price: 25000,
    qty: 2,
    image: "/images/sample1.png",
  },
  {
    name: "Produk B",
    price: 40000,
    qty: 1,
    image: "/images/sample2.png",
  },
])

// Hitung total
const totalPrice = computed(() =>
  items.value.reduce((sum, item) => sum + item.price * item.qty, 0)
)

// Hapus item
function removeItem(index) {
  items.value.splice(index, 1)
}

// Format angka jadi Rupiah
function formatCurrency(value) {
  return new Intl.NumberFormat("id-ID").format(value)
}
</script>
