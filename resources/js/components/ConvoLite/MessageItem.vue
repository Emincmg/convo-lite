<template>
  <div
    class="message-item flex gap-2"
    :class="isOwn ? 'flex-row-reverse' : 'flex-row'"
  >
    <div
      v-if="!isOwn"
      class="w-8 h-8 rounded-full bg-gray-300 flex-shrink-0 flex items-center justify-center text-xs font-medium text-gray-600"
    >
      {{ initials }}
    </div>

    <div class="max-w-[70%] space-y-1">
      <!-- Reply Reference -->
      <div
        v-if="message.reply_to"
        class="text-xs px-3 py-1 rounded-lg bg-gray-200 text-gray-600 cursor-pointer hover:bg-gray-300"
        @click="scrollToMessage(message.reply_to.id)"
      >
        <span class="font-medium">{{ message.reply_to.user?.name }}</span>
        <p class="truncate">{{ message.reply_to.body }}</p>
      </div>

      <!-- Message Bubble -->
      <div
        class="relative group px-4 py-2 rounded-2xl"
        :class="isOwn
          ? 'bg-blue-500 text-white rounded-br-md'
          : 'bg-white text-gray-900 rounded-bl-md shadow-sm'"
      >
        <p class="whitespace-pre-wrap break-words">{{ message.body }}</p>

        <!-- Attachments -->
        <div v-if="message.attachments?.length" class="mt-2 space-y-2">
          <a
            v-for="attachment in message.attachments"
            :key="attachment.id"
            :href="attachment.public_path"
            target="_blank"
            class="flex items-center gap-2 text-sm underline"
            :class="isOwn ? 'text-blue-100' : 'text-blue-500'"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
            </svg>
            {{ attachment.name }}
          </a>
        </div>

        <!-- Actions -->
        <div
          class="absolute top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity"
          :class="isOwn ? '-left-16' : '-right-16'"
        >
          <div class="flex items-center gap-1 bg-white rounded-full shadow-lg p-1">
            <button
              @click="$emit('react', '👍')"
              class="p-1 hover:bg-gray-100 rounded-full text-sm"
              title="Like"
            >👍</button>
            <button
              @click="$emit('react', '❤️')"
              class="p-1 hover:bg-gray-100 rounded-full text-sm"
              title="Love"
            >❤️</button>
            <button
              @click="$emit('reply')"
              class="p-1 hover:bg-gray-100 rounded-full"
              title="Reply"
            >
              <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Reactions -->
      <div v-if="hasReactions" class="flex flex-wrap gap-1">
        <span
          v-for="(users, emoji) in groupedReactions"
          :key="emoji"
          class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 rounded-full text-sm cursor-pointer hover:bg-gray-200"
          :title="users.map(u => u.name).join(', ')"
          @click="$emit('react', emoji)"
        >
          {{ emoji }} {{ users.length }}
        </span>
      </div>

      <!-- Time & Status -->
      <div
        class="flex items-center gap-1 text-xs"
        :class="isOwn ? 'justify-end' : 'justify-start'"
      >
        <span class="text-gray-400">{{ formatTime(message.created_at) }}</span>
        <span v-if="isOwn && message.read_by?.length > 1" class="text-blue-500">✓✓</span>
        <span v-else-if="isOwn" class="text-gray-400">✓</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  message: {
    type: Object,
    required: true
  },
  isOwn: {
    type: Boolean,
    default: false
  }
})

defineEmits(['react', 'reply'])

const initials = computed(() => {
  const name = props.message.user?.name || 'U'
  return name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase()
})

const hasReactions = computed(() => {
  return props.message.reactions?.length > 0
})

const groupedReactions = computed(() => {
  if (!props.message.reactions) return {}
  return props.message.reactions.reduce((acc, r) => {
    if (!acc[r.emoji]) acc[r.emoji] = []
    acc[r.emoji].push(r.user)
    return acc
  }, {})
})

const formatTime = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

const scrollToMessage = (messageId) => {
  const el = document.querySelector(`[data-message-id="${messageId}"]`)
  if (el) {
    el.scrollIntoView({ behavior: 'smooth', block: 'center' })
    el.classList.add('bg-yellow-100')
    setTimeout(() => el.classList.remove('bg-yellow-100'), 2000)
  }
}
</script>
