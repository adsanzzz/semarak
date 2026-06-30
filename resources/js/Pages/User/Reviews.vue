<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'
import { ref } from 'vue'

const props = defineProps({
  reviews: Array,
})

const replyForms = ref({})

function openReplyForm(orderId, current) {
  replyForms.value[orderId] = replyForms.value[orderId] || { seller_reply: current || '' }
}

function submitReply(orderId) {
  const payload = replyForms.value[orderId] || { seller_reply: '' }
  router.post(route('user.reviews.reply', { order: orderId }), payload, {
    preserveScroll: true,
    onSuccess: () => {
      router.reload()
    }
  })
}
</script>

<template>
  <Head title="Ulasan" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold">Ulasan Pembeli</h2>
    </template>

    <div class="flex">
      <Sidebar class="fixed left-0 top-0 h-screen" />
      <div class="flex-1 p-8 bg-gray-100 min-h-screen">
        <div class="bg-white rounded-2xl shadow p-6">
          <h3 class="text-lg font-semibold mb-4">Semua Ulasan untuk Toko Anda</h3>

          <div class="space-y-4">
            <div v-if="!reviews || reviews.length === 0" class="text-gray-500">Belum ada ulasan.</div>
            <div v-else>
              <div v-for="(r, idx) in reviews" :key="r.id ?? idx" class="border rounded-lg p-4 bg-white mb-4">
                <div class="flex justify-between items-start">
                  <div>
                    <p class="font-semibold">Produk: {{ r.product.nama }}</p>
                    <p class="text-sm text-gray-600">Dari: {{ r.buyer?.name || '-' }} · {{ r.reviewed_at ? new Date(r.reviewed_at).toLocaleString('id-ID') : '' }}</p>
                  </div>
                  <div class="text-yellow-500 font-semibold text-lg">★ {{ r.rating }}</div>
                </div>

                <div class="mt-3">
                  <p class="text-gray-800">{{ r.review_text }}</p>
                  <img v-if="r.review_image" :src="r.review_image" class="mt-2 max-h-36 rounded-lg object-contain border bg-gray-50 p-1" />
                </div>

                <div class="mt-3">
                  <p class="font-medium">Balasan Penjual:</p>
                  <div v-if="r.seller_reply" class="mt-2 text-gray-700">{{ r.seller_reply }}</div>
                  <div v-else class="mt-2">
                    <textarea v-model="(replyForms[r.id] = replyForms[r.id] || { seller_reply: '' }).seller_reply" class="w-full border rounded-md p-2" rows="3" placeholder="Tulis balasan..."></textarea>
                    <div class="mt-2 flex gap-2 justify-end">
                      <button @click="submitReply(r.id)" class="bg-blue-600 text-white px-3 py-1 rounded">Kirim Balasan</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
