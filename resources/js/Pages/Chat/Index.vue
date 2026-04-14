<script setup>
import { ref, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
  conversations: Array,
  selectedConversation: Object
})

const page = usePage()
const selectedConversation = ref(props.selectedConversation || null)
const newMessage = ref('')

// Filter conversations based on user role
const userConversations = computed(() => {
  const userId = page.props.auth?.user?.id
  return props.conversations || []
})

// Select a conversation
function selectConversation(conversation) {
  selectedConversation.value = conversation
  router.get(route('chat.show', conversation.id), {}, {
    preserveState: true,
    replace: true
  })
}

// Send message
function sendMessage() {
  if (!newMessage.value.trim() || !selectedConversation.value) return

  router.post(route('chat.send', selectedConversation.value.id), {
    message: newMessage.value
  }, {
    preserveScroll: true,
    onSuccess: () => {
      newMessage.value = ''
    }
  })
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
      <div class="bg-green-600 text-white p-4">
        <h1 class="text-xl font-semibold">Chat</h1>
      </div>

      <!-- Search -->
      <div class="p-3 border-b">
        <input
          type="text"
          placeholder="Cari chat..."
          class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
        />
      </div>

      <!-- Conversations List -->
      <div class="flex-1 overflow-y-auto">
        <div
          v-for="conversation in userConversations"
          :key="conversation.id"
          @click="selectConversation(conversation)"
          :class="[
            'p-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50',
            selectedConversation?.id === conversation.id ? 'bg-green-50 border-l-4 border-l-green-600' : ''
          ]"
        >
          <div class="flex items-center space-x-3">
            <!-- Avatar -->
            <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
              <span class="text-gray-600 font-semibold">
                {{ conversation.other_user?.name?.charAt(0)?.toUpperCase() || '?' }}
              </span>
            </div>

            <!-- Chat Info -->
            <div class="flex-1 min-w-0">
              <div class="flex justify-between items-center">
                <h3 class="font-semibold text-gray-900 truncate">
                  {{ conversation.other_user?.name || 'Unknown User' }}
                </h3>
                <span class="text-xs text-gray-500">
                  {{ conversation.last_message ? formatTime(conversation.last_message.created_at) : '' }}
                </span>
              </div>
              <p class="text-sm text-gray-600 truncate">
                {{ conversation.last_message?.message || 'Belum ada pesan' }}
              </p>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="userConversations.length === 0" class="p-8 text-center text-gray-500">
          <div class="text-4xl mb-4">💬</div>
          <p>Belum ada percakapan</p>
          <p class="text-sm">Mulai chat dengan penjual dari halaman produk</p>
        </div>
      </div>
    </div>

    <!-- Chat Area -->
    <div class="flex-1 flex flex-col">
      <div v-if="!selectedConversation" class="flex-1 flex items-center justify-center bg-gray-50">
        <div class="text-center">
          <div class="text-6xl mb-4">💬</div>
          <h2 class="text-2xl font-semibold text-gray-700 mb-2">Pilih percakapan</h2>
          <p class="text-gray-500">Pilih chat dari daftar di sebelah kiri untuk mulai mengobrol</p>
        </div>
      </div>

      <div v-else class="flex-1 flex flex-col">
        <!-- Chat Header -->
        <div class="bg-white border-b p-4 flex items-center space-x-3">
          <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
            <span class="text-gray-600 font-semibold">
              {{ selectedConversation.other_user?.name?.charAt(0)?.toUpperCase() || '?' }}
            </span>
          </div>
          <div>
            <h3 class="font-semibold text-gray-900">
              {{ selectedConversation.other_user?.name || 'Unknown User' }}
            </h3>
            <p class="text-sm text-gray-500">Online</p>
          </div>
        </div>

        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
          <div
            v-for="message in selectedConversation.messages || []"
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