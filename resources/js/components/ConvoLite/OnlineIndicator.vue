<template>
  <span
    class="online-indicator block w-3 h-3 bg-green-500 rounded-full border-2 border-white"
    :class="{ 'animate-pulse': pulse }"
  ></span>
</template>

<script setup>
defineProps({
  pulse: {
    type: Boolean,
    default: false
  }
})
</script>
