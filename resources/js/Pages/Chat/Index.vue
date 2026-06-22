<script setup>
import { ref, computed, watch } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
  conversations: Array,
  selectedConversation: Object,
  composerPrefill: {
    type: String,
    default: ''
  }
})

const page = usePage()
const selectedConversationId = ref(props.selectedConversation?.id || null)
const newMessage = ref('')
const menuOpen = ref(false)
const selectionMode = ref(false)
const selectedMessageIds = ref([])
const replyTarget = ref(null)

const userConversations = computed(() => {
  return props.conversations || []
})

const selectedConversation = computed(() => {
  if (props.selectedConversation?.id === selectedConversationId.value) {
    return props.selectedConversation
  }

  if (selectedConversationId.value) {
    return userConversations.value.find((conversation) => conversation.id === selectedConversationId.value) || null
  }

  return props.selectedConversation || null
})

const selectedMessages = computed(() => {
  const messages = selectedConversation.value?.messages || []

  return [...messages].sort((a, b) => {
    const timeDiff = new Date(a.created_at).getTime() - new Date(b.created_at).getTime()
    if (timeDiff !== 0) return timeDiff
    return (a.id || 0) - (b.id || 0)
  })
})

watch(
  () => props.selectedConversation,
  (nextConversation) => {
    if (nextConversation?.id) {
      selectedConversationId.value = nextConversation.id
    }

    selectedMessageIds.value = []
    selectionMode.value = false
    replyTarget.value = null
    menuOpen.value = false
  },
  { immediate: true }
)

watch(
  () => props.composerPrefill,
  (prefill) => {
    if (typeof prefill === 'string' && prefill.trim() !== '') {
      newMessage.value = prefill.trim()
    }
  },
  { immediate: true }
)

// Select a conversation
function selectConversation(conversation) {
  if (!conversation?.id) return

  if (selectionMode.value) return

  selectedConversationId.value = conversation.id
  router.get(route('chat.show', conversation.id), {}, {
    preserveScroll: true,
    preserveState: false,
    replace: true
  })
}

// Send message
function sendMessage() {
  if (!newMessage.value.trim() || !selectedConversation.value) return

  router.post(route('chat.send', selectedConversation.value.id), {
    message: newMessage.value,
    reply_to_message_id: replyTarget.value?.id || null
  }, {
    preserveScroll: true,
    onSuccess: () => {
      newMessage.value = ''
      replyTarget.value = null
    }
  })
}

function toggleMenu() {
  if (!selectedConversation.value) return
  menuOpen.value = !menuOpen.value
}

function enableSelectionMode() {
  selectionMode.value = true
  selectedMessageIds.value = []
  menuOpen.value = false
}

function cancelSelectionMode() {
  selectionMode.value = false
  selectedMessageIds.value = []
}

function toggleMessageSelection(messageId) {
  if (!selectionMode.value) return

  const index = selectedMessageIds.value.indexOf(messageId)
  if (index >= 0) {
    selectedMessageIds.value.splice(index, 1)
  } else {
    selectedMessageIds.value.push(messageId)
  }
}

function isMessageSelected(messageId) {
  return selectedMessageIds.value.includes(messageId)
}

function deleteAllMessages() {
  if (!selectedConversation.value) return

  const confirmed = window.confirm('Hapus semua chat di room ini?')
  if (!confirmed) return

  router.delete(route('chat.messages.deleteAll', selectedConversation.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      menuOpen.value = false
      cancelSelectionMode()
      replyTarget.value = null
    }
  })
}

function deleteSelectedMessages() {
  if (!selectedConversation.value || selectedMessageIds.value.length === 0) return

  const confirmed = window.confirm(`Hapus ${selectedMessageIds.value.length} chat yang dipilih?`)
  if (!confirmed) return

  router.delete(route('chat.messages.deleteSelected', selectedConversation.value.id), {
    data: {
      message_ids: selectedMessageIds.value
    },
    preserveScroll: true,
    onSuccess: () => {
      cancelSelectionMode()
      replyTarget.value = null
    }
  })
}

function setReplyTarget(message) {
  replyTarget.value = message
  selectionMode.value = false
  selectedMessageIds.value = []
}

function clearReplyTarget() {
  replyTarget.value = null
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
            selectedConversationId === conversation.id ? 'bg-green-50 border-l-4 border-l-green-600' : ''
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
                <div class="flex items-center gap-2">
                  <span class="text-xs text-gray-500">
                    {{ conversation.last_message ? formatTime(conversation.last_message.created_at) : '' }}
                  </span>
                  <span
                    v-if="conversation.unread_count > 0"
                    class="min-w-[18px] h-[18px] px-1 rounded-full bg-green-600 text-white text-[10px] font-bold flex items-center justify-center"
                  >
                    {{ conversation.unread_count > 99 ? '99+' : conversation.unread_count }}
                  </span>
                </div>
              </div>
              <p class="text-sm text-gray-600 truncate">
                {{ conversation.last_message?.message || 'Belum ada pesan' }}
              </p>
              <p v-if="conversation.order_id" class="text-xs text-gray-400 mt-0.5">
                Pesanan #{{ conversation.order_id }}
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
        <div class="bg-white border-b p-4 flex items-center justify-between gap-3">
          <div class="flex items-center space-x-3 min-w-0">
            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
              <span class="text-gray-600 font-semibold">
                {{ selectedConversation.other_user?.name?.charAt(0)?.toUpperCase() || '?' }}
              </span>
            </div>
            <div>
              <h3 class="font-semibold text-gray-900">
                {{ selectedConversation.other_user?.name || 'Unknown User' }}
              </h3>
              <p class="text-sm text-gray-500">
                <span v-if="selectedConversation.order_id">Pesanan #{{ selectedConversation.order_id }}</span>
                <span v-else>Online</span>
              </p>
            </div>
          </div>

          <div class="relative flex items-center gap-2">
            <button
              v-if="selectionMode"
              type="button"
              @click="deleteSelectedMessages"
              :disabled="selectedMessageIds.length === 0"
              class="px-3 py-1.5 rounded-md bg-red-600 text-white text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Hapus ({{ selectedMessageIds.length }})
            </button>
            <button
              v-if="selectionMode"
              type="button"
              @click="cancelSelectionMode"
              class="px-3 py-1.5 rounded-md border text-sm text-gray-700"
            >
              Batal
            </button>

            <button
              v-if="!selectionMode"
              type="button"
              @click="toggleMenu"
              class="h-9 w-9 rounded-full hover:bg-gray-100 flex items-center justify-center"
              aria-label="Menu chat"
            >
              <svg class="h-5 w-5 text-gray-700" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12 7a2 2 0 110-4 2 2 0 010 4zm0 7a2 2 0 110-4 2 2 0 010 4zm0 7a2 2 0 110-4 2 2 0 010 4z" />
              </svg>
            </button>

            <div v-if="menuOpen && !selectionMode" class="absolute right-0 top-11 z-10 w-48 rounded-lg border bg-white shadow">
              <button
                type="button"
                @click="deleteAllMessages"
                class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50"
              >
                Hapus semua chat
              </button>
              <button
                type="button"
                @click="enableSelectionMode"
                class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50"
              >
                Pilih chat
              </button>
            </div>
          </div>
        </div>

        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
          <div
            v-for="message in selectedMessages"
            :key="message.id"
            :class="[
              'flex',
              message.sender_id === page.props.auth?.user?.id ? 'justify-end' : 'justify-start'
            ]"
            @click="toggleMessageSelection(message.id)"
          >
            <div
              :class="[
                'max-w-xs px-4 py-2 rounded-lg transition-colors',
                message.sender_id === page.props.auth?.user?.id
                  ? 'bg-green-600 text-white'
                  : 'bg-white text-gray-900 border',
                selectionMode && isMessageSelected(message.id) ? 'ring-2 ring-blue-400' : '',
                selectionMode ? 'cursor-pointer' : ''
              ]"
            >
              <div
                v-if="message.replied_message"
                class="mb-2 rounded border-l-4 border-gray-300 bg-black/10 px-2 py-1 text-xs"
              >
                <p class="font-semibold truncate">
                  {{ message.replied_message.sender?.name || 'User' }}
                </p>
                <p class="truncate">{{ message.replied_message.message }}</p>
              </div>

              <p>{{ message.message }}</p>
              <div class="mt-1 flex items-center justify-between gap-3">
                <p class="text-xs opacity-70">
                  {{ formatTime(message.created_at) }}
                </p>
                <button
                  v-if="!selectionMode"
                  type="button"
                  @click.stop="setReplyTarget(message)"
                  class="text-[11px] font-semibold opacity-80 hover:opacity-100"
                >
                  Balas
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Message Input -->
        <div class="bg-white border-t p-4">
          <div
            v-if="replyTarget"
            class="mb-3 rounded-lg border border-green-200 bg-green-50 px-3 py-2"
          >
            <div class="flex items-start justify-between gap-2">
              <div class="min-w-0">
                <p class="text-xs font-semibold text-green-700">
                  Membalas {{ replyTarget.sender?.name || 'User' }}
                </p>
                <p class="text-sm text-gray-700 truncate">{{ replyTarget.message }}</p>
              </div>
              <button
                type="button"
                @click="clearReplyTarget"
                class="text-gray-500 hover:text-gray-700"
                aria-label="Batal balas"
              >
                x
              </button>
            </div>
          </div>

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