<template>
  <div class="convo-chat h-full flex bg-white rounded-lg shadow-lg overflow-hidden">
    <!-- Conversation List (sidebar) -->
    <div
      class="conversation-sidebar w-full md:w-80 flex-shrink-0"
      :class="{ 'hidden md:block': selectedConversation }"
    >
      <ConversationList
        :conversations="conversations"
        :selected-id="selectedConversation?.id"
        :current-user-id="currentUserId"
        :online-users="onlineUsers"
        @select="selectConversation"
      />
    </div>

    <!-- Chat Window -->
    <div
      class="chat-area flex-1"
      :class="{ 'hidden md:flex': !selectedConversation }"
    >
      <ChatWindow
        v-if="selectedConversation"
        :conversation="selectedConversation"
        :messages="messages"
        :current-user-id="currentUserId"
        :typing-user="typingUser"
        :is-online="isOtherUserOnline"
        :loading="loading"
        :replying-to="replyingTo"
        @send="sendMessage"
        @typing="handleTyping"
        @attach="handleAttach"
        @react="handleReaction"
        @reply="setReplyTo"
        @cancel-reply="replyingTo = null"
        @back="selectedConversation = null"
      />

      <!-- Empty State -->
      <div
        v-else
        class="hidden md:flex flex-1 items-center justify-center bg-gray-50"
      >
        <div class="text-center text-gray-500">
          <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
          </svg>
          <p class="text-lg font-medium">Select a conversation</p>
          <p class="text-sm">Choose a conversation from the list to start messaging</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import ConversationList from './ConversationList.vue'
import ChatWindow from './ChatWindow.vue'

const props = defineProps({
  currentUserId: {
    type: Number,
    required: true
  },
  apiBaseUrl: {
    type: String,
    default: '/api/convo-lite'
  },
  echoChannel: {
    type: String,
    default: 'user'
  }
})

const emit = defineEmits(['error', 'message-sent', 'conversation-selected'])

const conversations = ref([])
const selectedConversation = ref(null)
const messages = ref([])
const loading = ref(false)
const onlineUsers = ref([])
const typingUsers = ref({})
const replyingTo = ref(null)
const pendingFiles = ref([])

const typingUser = computed(() => {
  if (!selectedConversation.value) return null
  const typing = typingUsers.value[selectedConversation.value.id]
  return typing || null
})

const isOtherUserOnline = computed(() => {
  if (!selectedConversation.value) return false
  const otherUser = selectedConversation.value.users?.find(u => u.id !== props.currentUserId)
  return otherUser && onlineUsers.value.includes(otherUser.id)
})

const fetchConversations = async () => {
  try {
    const res = await fetch(`${props.apiBaseUrl}/conversations`)
    const data = await res.json()
    conversations.value = data
  } catch (e) {
    emit('error', e)
  }
}

const fetchMessages = async (conversationId) => {
  loading.value = true
  try {
    const res = await fetch(`${props.apiBaseUrl}/conversations/${conversationId}/messages`)
    const data = await res.json()
    messages.value = data.data || data
  } catch (e) {
    emit('error', e)
  } finally {
    loading.value = false
  }
}

const selectConversation = async (conversation) => {
  selectedConversation.value = conversation
  replyingTo.value = null
  await fetchMessages(conversation.id)
  emit('conversation-selected', conversation)
}

const sendMessage = async ({ body, files }) => {
  if (!selectedConversation.value) return

  const formData = new FormData()
  formData.append('body', body)

  if (replyingTo.value) {
    formData.append('reply_to_id', replyingTo.value.id)
  }

  files.forEach((file, i) => {
    formData.append(`files[${i}]`, file)
  })

  try {
    const res = await fetch(
      `${props.apiBaseUrl}/conversations/${selectedConversation.value.id}/messages`,
      {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        }
      }
    )

    const message = await res.json()
    messages.value.push(message)
    replyingTo.value = null
    emit('message-sent', message)

    // Update conversation list
    const convIndex = conversations.value.findIndex(c => c.id === selectedConversation.value.id)
    if (convIndex > -1) {
      conversations.value[convIndex].last_message = message
      // Move to top
      const conv = conversations.value.splice(convIndex, 1)[0]
      conversations.value.unshift(conv)
    }
  } catch (e) {
    emit('error', e)
  }
}

const handleTyping = async (isTyping) => {
  if (!selectedConversation.value) return

  try {
    await fetch(
      `${props.apiBaseUrl}/conversations/${selectedConversation.value.id}/typing`,
      {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        },
        body: JSON.stringify({ is_typing: isTyping })
      }
    )
  } catch (e) {
    // Silent fail for typing indicator
  }
}

const handleAttach = (files) => {
  pendingFiles.value = files
}

const handleReaction = async (message, emoji) => {
  try {
    const res = await fetch(
      `${props.apiBaseUrl}/messages/${message.id}/reactions`,
      {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        },
        body: JSON.stringify({ emoji })
      }
    )

    const reaction = await res.json()

    // Update local message reactions
    const msgIndex = messages.value.findIndex(m => m.id === message.id)
    if (msgIndex > -1) {
      if (!messages.value[msgIndex].reactions) {
        messages.value[msgIndex].reactions = []
      }

      const existingIndex = messages.value[msgIndex].reactions.findIndex(
        r => r.user_id === props.currentUserId && r.emoji === emoji
      )

      if (existingIndex > -1) {
        messages.value[msgIndex].reactions.splice(existingIndex, 1)
      } else {
        messages.value[msgIndex].reactions.push(reaction)
      }
    }
  } catch (e) {
    emit('error', e)
  }
}

const setReplyTo = (message) => {
  replyingTo.value = message
}

const setupEcho = () => {
  if (typeof window.Echo === 'undefined') return

  window.Echo.private(`${props.echoChannel}.${props.currentUserId}`)
    .listen('MessageSent', (e) => {
      if (selectedConversation.value?.id === e.message.conversation_id) {
        messages.value.push(e.message)
      }
      fetchConversations()
    })
    .listen('UserTyping', (e) => {
      if (e.is_typing) {
        typingUsers.value[e.conversation_id] = e.user_name
      } else {
        delete typingUsers.value[e.conversation_id]
      }
    })
    .listen('ConversationCreated', () => {
      fetchConversations()
    })

  window.Echo.join('convo-lite.online')
    .here((users) => {
      onlineUsers.value = users.map(u => u.id)
    })
    .joining((user) => {
      if (!onlineUsers.value.includes(user.id)) {
        onlineUsers.value.push(user.id)
      }
    })
    .leaving((user) => {
      onlineUsers.value = onlineUsers.value.filter(id => id !== user.id)
    })
}

onMounted(() => {
  fetchConversations()
  setupEcho()
})

onUnmounted(() => {
  if (typeof window.Echo !== 'undefined') {
    window.Echo.leave(`${props.echoChannel}.${props.currentUserId}`)
    window.Echo.leave('convo-lite.online')
  }
})

defineExpose({
  fetchConversations,
  selectConversation,
  conversations,
  selectedConversation
})
</script>

<style scoped>
.convo-chat {
  min-height: 500px;
}
</style>
