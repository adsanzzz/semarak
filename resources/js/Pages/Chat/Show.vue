<script setup>
import { ref, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
  conversation: Object
})

const page = usePage()
const newMessage = ref('')

// Get the other user in the conversation
const otherUser = computed(() => {
  const userId = page.props.auth?.user?.id
  return props.conversation.buyer_id === userId
    ? props.conversation.seller
    : props.conversation.buyer
})

// Send message
function sendMessage() {
  if (!newMessage.value.trim()) return

  router.post(route('chat.send', props.conversation.id), {
    message: newMessage.value
  }, {
    preserveScroll: true,
    onSuccess: () => {
      newMessage.value = ''
    }
  })
}

// Go back to chat index
function goBack() {
  router.get(route('chat.index'))
}

// Format time
function formatTime(dateString) {
  const date = new Date(dateString)
  const now = new Date()
  const diff = now - date

  if (diff < 60000) return 'Baru saja'
  if (diff < 3600000) return `${Math.floor(diff / 60000)}m`
  if (diff < 86400000) return `${Math.floor(diff / 3600000)}j`
  return date.toLocaleDateString()
}
</script>

<template>
  <Head title="Chat" />

  <div class="h-screen flex bg-gray-100">
    <!-- Sidebar Conversations -->
    <div class="w-1/3 bg-white border-r border-gray-300 flex flex-col">
      <!-- Header -->
      <div class="bg-green-600 text-white p-4 flex items-center space-x-3">
        <button @click="goBack" class="hover:bg-green-700 p-1 rounded">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
        </button>
        <h1 class="text-xl font-semibold">Chat</h1>
      </div>

      <!-- Current Conversation -->
      <div class="p-4 border-b bg-green-50">
        <div class="flex items-center space-x-3">
          <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
            <span class="text-gray-600 font-semibold">
              {{ otherUser?.name?.charAt(0)?.toUpperCase() || '?' }}
            </span>
          </div>
          <div class="flex-1">
            <h3 class="font-semibold text-gray-900">
              {{ otherUser?.name || 'Unknown User' }}
            </h3>
            <p class="text-sm text-gray-500">Sedang chat</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Chat Area -->
    <div class="flex-1 flex flex-col">
      <!-- Chat Header -->
      <div class="bg-white border-b p-4 flex items-center space-x-3">
        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
          <span class="text-gray-600 font-semibold">
            {{ otherUser?.name?.charAt(0)?.toUpperCase() || '?' }}
          </span>
        </div>
        <div>
          <h3 class="font-semibold text-gray-900">
            {{ otherUser?.name || 'Unknown User' }}
          </h3>
          <p class="text-sm text-gray-500">Online</p>
        </div>
      </div>

      <!-- Messages -->
      <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
        <div
          v-for="message in conversation.messages || []"
          :key="message.id"
          :class="[
            'flex',
            message.sender_id === page.props.auth?.user?.id ? 'justify-end' : 'justify-start'
          ]"
        >
          <div
            :class="[
              'max-w-xs px-4 py-2 rounded-lg',
              message.sender_id === page.props.auth?.user?.id
                ? 'bg-green-600 text-white'
                : 'bg-white text-gray-900 border'
            ]"
          >
            <p>{{ message.message }}</p>
            <p class="text-xs mt-1 opacity-70">
              {{ formatTime(message.created_at) }}
            </p>
          </div>
        </div>
      </div>

      <!-- Message Input -->
      <div class="bg-white border-t p-4">
        <form @submit.prevent="sendMessage" class="flex space-x-3">
          <input
            v-model="newMessage"
            type="text"
            placeholder="Ketik pesan..."
            class="flex-1 px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-green-500"
          />
          <button
            type="submit"
            :disabled="!newMessage.trim()"
            class="bg-green-600 text-white px-6 py-2 rounded-full hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
            </svg>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>