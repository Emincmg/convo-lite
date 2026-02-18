<template>
  <div class="chat-window h-full flex flex-col bg-gray-50">
    <!-- Header -->
    <div class="flex items-center gap-3 p-4 bg-white border-b border-gray-200">
      <button
        v-if="showBackButton"
        @click="$emit('back')"
        class="p-2 hover:bg-gray-100 rounded-full transition-colors md:hidden"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
      </button>

      <div class="relative">
        <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-medium">
          {{ initials }}
        </div>
        <OnlineIndicator v-if="isOnline" class="absolute bottom-0 right-0" />
      </div>

      <div class="flex-1">
        <h3 class="font-semibold text-gray-900">{{ title }}</h3>
        <p class="text-sm text-gray-500">
          <span v-if="isOnline">Online</span>
          <span v-else-if="typingUser">{{ typingUser }} is typing...</span>
          <span v-else>Offline</span>
        </p>
      </div>
    </div>

    <!-- Messages -->
    <div
      ref="messagesContainer"
      class="flex-1 overflow-y-auto p-4 space-y-4"
    >
      <div v-if="loading" class="flex justify-center py-4">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
      </div>

      <MessageItem
        v-for="message in messages"
        :key="message.id"
        :message="message"
        :is-own="message.user_id === currentUserId"
        @react="(emoji) => $emit('react', message, emoji)"
        @reply="$emit('reply', message)"
      />

      <TypingIndicator v-if="typingUser" :user="typingUser" />
    </div>

    <!-- Reply Preview -->
    <div
      v-if="replyingTo"
      class="px-4 py-2 bg-gray-100 border-t border-gray-200 flex items-center gap-2"
    >
      <div class="flex-1 text-sm">
        <span class="text-gray-500">Replying to </span>
        <span class="font-medium">{{ replyingTo.user?.name }}</span>
        <p class="text-gray-600 truncate">{{ replyingTo.body }}</p>
      </div>
      <button
        @click="$emit('cancel-reply')"
        class="p-1 hover:bg-gray-200 rounded-full"
      >
        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <!-- Input -->
    <MessageInput
      :disabled="!conversation"
      @send="$emit('send', $event)"
      @typing="$emit('typing', $event)"
      @attach="$emit('attach', $event)"
    />
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import MessageItem from './MessageItem.vue'
import MessageInput from './MessageInput.vue'
import TypingIndicator from './TypingIndicator.vue'
import OnlineIndicator from './OnlineIndicator.vue'

const props = defineProps({
  conversation: {
    type: Object,
    default: null
  },
  messages: {
    type: Array,
    default: () => []
  },
  currentUserId: {
    type: Number,
    required: true
  },
  typingUser: {
    type: String,
    default: null
  },
  isOnline: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  },
  replyingTo: {
    type: Object,
    default: null
  },
  showBackButton: {
    type: Boolean,
    default: true
  }
})

defineEmits(['send', 'typing', 'attach', 'react', 'reply', 'cancel-reply', 'back'])

const messagesContainer = ref(null)

const title = computed(() => {
  if (!props.conversation) return ''
  if (props.conversation.title && props.conversation.title !== 'New Conversation') {
    return props.conversation.title
  }
  const otherUser = props.conversation.users?.find(u => u.id !== props.currentUserId)
  return otherUser?.name || 'Chat'
})

const initials = computed(() => {
  return title.value.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase()
})

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}

watch(() => props.messages.length, scrollToBottom)
watch(() => props.conversation?.id, scrollToBottom)
</script>
