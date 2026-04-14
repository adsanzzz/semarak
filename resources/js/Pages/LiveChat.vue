<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Sidebar from '@/Components/Sidebar.vue'
import { ref } from 'vue'

const selected = ref(null)

const chats = [
  { id: 1, name: 'Pembeli A' }
]

const messages = [
  { from: 'buyer', text: 'Halo kak' },
  { from: 'seller', text: 'Halo' }
]
</script>

<template>
<AuthenticatedLayout>

<div class="flex">

<Sidebar class="fixed left-0 top-0 h-screen"/>

<div class="flex-1 bg-gray-100 min-h-screen p-8">

<h2 class="text-xl font-semibold mb-4">Live Chat</h2>

<div class="flex h-[500px] bg-white rounded shadow">

<!-- LIST -->
<div class="w-1/3 border-r">
  <div v-for="c in chats" @click="selected=c" class="p-3 cursor-pointer hover:bg-gray-200">
    {{ c.name }}
  </div>
</div>

<!-- CHAT -->
<div class="flex-1 p-3">
  <div v-if="!selected">Pilih chat</div>

  <div v-else>
    <div v-for="msg in messages" :class="msg.from==='seller'?'text-right':'text-left'">
      <span class="px-3 py-2 rounded"
        :class="msg.from==='seller'?'bg-green-300':'bg-gray-300'">
        {{ msg.text }}
      </span>
    </div>
  </div>
</div>

</div>

</div>
</div>

</AuthenticatedLayout>
</template>