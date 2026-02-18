<template>
  <div class="typing-indicator flex items-center gap-2 text-gray-500 text-sm">
    <div class="flex gap-1">
      <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
      <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
      <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
    </div>
    <span v-if="user">{{ user }} is typing...</span>
  </div>
</template>

<script setup>
defineProps({
  user: {
    type: String,
    default: null
  }
})
</script>

<style scoped>
@keyframes bounce {
  0%, 60%, 100% {
    transform: translateY(0);
  }
  30% {
    transform: translateY(-4px);
  }
}

.animate-bounce {
  animation: bounce 1s infinite;
}
</style>
