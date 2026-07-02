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
              <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Pilih</th>
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
              class="border-b hover:bg-gray-50 animate-fade-in"
            >
              <td class="px-4 py-3 text-center">
                <input type="checkbox" v-model="item.selected" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4 cursor-pointer" />
              </td>
              <td class="px-4 py-3 flex items-center space-x-3">
                <img :src="item.image" alt="produk" class="w-12 h-12 rounded-lg object-cover border border-gray-200" />
                <div class="text-left">
                  <span class="font-bold text-gray-800 block">{{ item.name }}</span>
                  <!-- Variations Display -->
                  <div v-if="item.variations && Object.keys(item.variations).length > 0" class="flex flex-wrap gap-1 mt-1">
                    <span 
                      v-for="(val, key) in item.variations" 
                      :key="key"
                      class="inline-block bg-gray-50 text-gray-600 text-[10px] font-bold px-2 py-0.5 rounded-lg border border-gray-200 shadow-3xs"
                    >
                      {{ key }}: {{ val }}
                    </span>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3 text-center">
                <div class="flex items-center justify-center gap-1">
                  <!-- Minus Button -->
                  <button 
                    type="button" 
                    @click="changeQty(item, -1)" 
                    :disabled="item.qty <= 1"
                    class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:bg-gray-100 disabled:cursor-not-allowed transition select-none cursor-pointer text-sm font-bold"
                  >
                    &minus;
                  </button>
                  
                  <!-- Input -->
                  <input
                    type="number"
                    v-model.number="item.qty"
                    @input="updateQtyInput(item)"
                    class="w-12 h-8 border border-gray-200 rounded-lg text-center text-sm font-bold focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    min="1"
                  />

                  <!-- Plus Button -->
                  <button 
                    type="button" 
                    @click="changeQty(item, 1)" 
                    :disabled="item.qty >= (item.stok ?? 9999)"
                    class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:bg-gray-100 disabled:cursor-not-allowed transition select-none cursor-pointer text-sm font-bold"
                  >
                    &plus;
                  </button>
                </div>
              </td>
              <td class="px-4 py-3 text-center text-gray-600">
                Rp {{ formatCurrency(item.price) }}
              </td>
              <td class="px-4 py-3 text-center font-extrabold text-gray-900">
                Rp {{ formatCurrency(item.price * item.qty) }}
              </td>
              <td class="px-4 py-3 text-center">
                <button
                  @click="removeItem(item)"
                  class="text-red-500 hover:text-red-700 font-semibold text-xs cursor-pointer transition"
                >
                  Hapus
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Total & Checkout -->
      <div v-if="items.length > 0" class="mt-6 flex justify-between items-center bg-gray-50 border border-gray-100 p-5 rounded-2xl">
        <div class="text-lg font-bold text-gray-800">
          Total: <span class="text-2xl text-indigo-600 font-extrabold ml-1">Rp {{ formatCurrency(totalPrice) }}</span>
        </div>
        <button
          @click="proceedCheckout"
          class="px-6 py-2.5 rounded-xl flex items-center gap-2 font-bold text-sm shadow-sm transition"
          :class="selectedItems.length ? 'bg-indigo-600 text-white hover:bg-indigo-700 cursor-pointer' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
          :disabled="!selectedItems.length"
        >
          Checkout ({{ selectedItems.length }})
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from "vue"
import { usePage, router } from "@inertiajs/vue3"
import NavbarAuth from "@/Components/NavbarAuth.vue"

const page = usePage()
const items = ref([])

const syncItems = () => {
  const rawItems = page.props.items || []
  items.value = rawItems.map(raw => {
    const existing = items.value.find(i => i.id === raw.id && JSON.stringify(i.variations) === JSON.stringify(raw.variations))
    return {
      ...raw,
      selected: existing ? existing.selected : true
    }
  })
}

// Initial Sync
syncItems()

// Watch for props updates
watch(() => page.props.items, () => {
  syncItems()
}, { deep: true })

// Items selected for checkout
const selectedItems = computed(() => items.value.filter(i => i.selected))

// Hitung total harga (selected only)
const totalPrice = computed(() =>
  selectedItems.value.reduce((sum, item) => sum + item.price * item.qty, 0)
)

function changeQty(item, amount) {
  const newQty = item.qty + amount
  if (newQty < 1) return
  if (newQty > (item.stok ?? 9999)) {
    alert('Jumlah melebihi stok yang tersedia')
    return
  }

  router.post(route('keranjang.update'), {
    product_id: item.id,
    variations: item.variations,
    qty: newQty
  }, {
    preserveScroll: true,
    onSuccess: () => {
      item.qty = newQty
    }
  })
}

function updateQtyInput(item) {
  if (item.qty < 1) {
    item.qty = 1
  } else if (item.qty > (item.stok ?? 9999)) {
    item.qty = item.stok ?? 9999
    alert('Jumlah melebihi stok yang tersedia')
  }

  router.post(route('keranjang.update'), {
    product_id: item.id,
    variations: item.variations,
    qty: item.qty
  }, {
    preserveScroll: true
  })
}

function removeItem(item) {
  if (confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?')) {
    router.delete(route('keranjang.destroy'), {
      data: {
        product_id: item.id,
        variations: item.variations
      },
      preserveScroll: true
    })
  }
}

function proceedCheckout() {
  if (selectedItems.value.length === 0) return

  router.post(route('checkout.prepare.cart'), {
    items: selectedItems.value.map((item) => ({
      product_id: item.id,
      qty: item.qty,
      variations: item.variations
    })),
  })
}

// Format angka jadi Rupiah
function formatCurrency(value) {
  return new Intl.NumberFormat("id-ID").format(value)
}
</script>
