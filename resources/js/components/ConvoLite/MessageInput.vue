<template>
  <div class="message-input p-4 bg-white border-t border-gray-200">
    <form @submit.prevent="send" class="flex items-end gap-2">
      <!-- Attachment Button -->
      <label class="p-2 hover:bg-gray-100 rounded-full cursor-pointer transition-colors">
        <input
          type="file"
          multiple
          class="hidden"
          @change="handleFiles"
          :disabled="disabled"
        />
        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
        </svg>
      </label>

      <!-- Text Input -->
      <div class="flex-1 relative">
        <!-- File Previews -->
        <div v-if="files.length" class="flex flex-wrap gap-2 mb-2">
          <div
            v-for="(file, index) in files"
            :key="index"
            class="flex items-center gap-1 px-2 py-1 bg-gray-100 rounded-lg text-sm"
          >
            <span class="truncate max-w-[150px]">{{ file.name }}</span>
            <button
              type="button"
              @click="removeFile(index)"
              class="p-0.5 hover:bg-gray-200 rounded-full"
            >
              <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <textarea
          ref="inputRef"
          v-model="message"
          @input="handleInput"
          @keydown.enter.exact.prevent="send"
          :disabled="disabled"
          rows="1"
          placeholder="Type a message..."
          class="w-full px-4 py-2 bg-gray-100 rounded-2xl resize-none focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
        ></textarea>
      </div>

      <!-- Send Button -->
      <button
        type="submit"
        :disabled="disabled || (!message.trim() && !files.length)"
        class="p-2 bg-blue-500 text-white rounded-full hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
        </svg>
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  disabled: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['send', 'typing', 'attach'])

const message = ref('')
const files = ref([])
const inputRef = ref(null)
const typingTimeout = ref(null)

const handleInput = () => {
  autoResize()

  if (typingTimeout.value) {
    clearTimeout(typingTimeout.value)
  }

  emit('typing', true)

  typingTimeout.value = setTimeout(() => {
    emit('typing', false)
  }, 2000)
}

const autoResize = () => {
  if (inputRef.value) {
    inputRef.value.style.height = 'auto'
    inputRef.value.style.height = Math.min(inputRef.value.scrollHeight, 150) + 'px'
  }
}

const handleFiles = (e) => {
  const newFiles = Array.from(e.target.files)
  files.value.push(...newFiles)
  emit('attach', files.value)
  e.target.value = ''
}

const removeFile = (index) => {
  files.value.splice(index, 1)
  emit('attach', files.value)
}

const send = () => {
  if (props.disabled) return
  if (!message.value.trim() && !files.value.length) return

  emit('send', {
    body: message.value.trim(),
    files: files.value
  })

  message.value = ''
  files.value = []

  if (inputRef.value) {
    inputRef.value.style.height = 'auto'
  }

  if (typingTimeout.value) {
    clearTimeout(typingTimeout.value)
    emit('typing', false)
  }
}

const focus = () => {
  inputRef.value?.focus()
}

defineExpose({ focus })
</script>
