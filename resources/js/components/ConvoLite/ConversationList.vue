<template>
  <div class="convo-list h-full flex flex-col bg-white border-r border-gray-200">
    <div class="p-4 border-b border-gray-200">
      <h2 class="text-lg font-semibold text-gray-800">Messages</h2>
    </div>

    <div class="flex-1 overflow-y-auto">
      <div
        v-for="conversation in conversations"
        :key="conversation.id"
        @click="$emit('select', conversation)"
        class="conversation-item flex items-center gap-3 p-4 cursor-pointer hover:bg-gray-50 transition-colors"
        :class="{ 'bg-blue-50': selectedId === conversation.id }"
      >
        <div class="relative">
          <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-medium">
            {{ getInitials(conversation) }}
          </div>
          <OnlineIndicator
            v-if="isUserOnline(getOtherUser(conversation))"
            class="absolute bottom-0 right-0"
          />
        </div>

        <div class="flex-1 min-w-0">
          <div class="flex items-center justify-between">
            <span class="font-medium text-gray-900 truncate">
              {{ getConversationTitle(conversation) }}
            </span>
            <span class="text-xs text-gray-500">
              {{ formatTime(conversation.last_message?.created_at) }}
            </span>
          </div>

          <div class="flex items-center justify-between mt-1">
            <p class="text-sm text-gray-500 truncate">
              {{ conversation.last_message?.body || 'No messages yet' }}
            </p>
            <span
              v-if="conversation.unread_count > 0"
              class="ml-2 px-2 py-0.5 text-xs font-medium text-white bg-blue-500 rounded-full"
            >
              {{ conversation.unread_count }}
            </span>
          </div>
        </div>
      </div>

      <div v-if="conversations.length === 0" class="p-4 text-center text-gray-500">
        No conversations yet
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import OnlineIndicator from './OnlineIndicator.vue'

const props = defineProps({
  conversations: {
    type: Array,
    default: () => []
  },
  selectedId: {
    type: [Number, null],
    default: null
  },
  currentUserId: {
    type: Number,
    required: true
  },
  onlineUsers: {
    type: Array,
    default: () => []
  }
})

defineEmits(['select'])

const getOtherUser = (conversation) => {
  return conversation.users?.find(u => u.id !== props.currentUserId)
}

const getConversationTitle = (conversation) => {
  if (conversation.title && conversation.title !== 'New Conversation') {
    return conversation.title
  }
  const otherUser = getOtherUser(conversation)
  return otherUser?.name || 'Unknown'
}

const getInitials = (conversation) => {
  const title = getConversationTitle(conversation)
  return title.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase()
}

const isUserOnline = (user) => {
  return user && props.onlineUsers.includes(user.id)
}

const formatTime = (date) => {
  if (!date) return ''
  const d = new Date(date)
  const now = new Date()
  const diff = now - d

  if (diff < 86400000) {
    return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
  }
  if (diff < 604800000) {
    return d.toLocaleDateString([], { weekday: 'short' })
  }
  return d.toLocaleDateString([], { month: 'short', day: 'numeric' })
}
</script>
