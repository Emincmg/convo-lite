import { ref, computed } from 'vue'

export function useConvo(options = {}) {
  const {
    baseUrl = '/api/convo-lite',
    currentUserId = null
  } = options

  const conversations = ref([])
  const messages = ref([])
  const loading = ref(false)
  const error = ref(null)

  const headers = () => ({
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
    'Accept': 'application/json'
  })

  const fetchConversations = async () => {
    loading.value = true
    error.value = null
    try {
      const res = await fetch(`${baseUrl}/conversations`, { headers: headers() })
      if (!res.ok) throw new Error('Failed to fetch conversations')
      conversations.value = await res.json()
      return conversations.value
    } catch (e) {
      error.value = e.message
      throw e
    } finally {
      loading.value = false
    }
  }

  const fetchMessages = async (conversationId, page = 1) => {
    loading.value = true
    error.value = null
    try {
      const res = await fetch(
        `${baseUrl}/conversations/${conversationId}/messages?page=${page}`,
        { headers: headers() }
      )
      if (!res.ok) throw new Error('Failed to fetch messages')
      const data = await res.json()
      messages.value = data.data || data
      return data
    } catch (e) {
      error.value = e.message
      throw e
    } finally {
      loading.value = false
    }
  }

  const sendMessage = async (conversationId, body, files = [], replyToId = null) => {
    error.value = null
    const formData = new FormData()
    formData.append('body', body)

    if (replyToId) {
      formData.append('reply_to_id', replyToId)
    }

    files.forEach((file, i) => {
      formData.append(`files[${i}]`, file)
    })

    try {
      const res = await fetch(`${baseUrl}/conversations/${conversationId}/messages`, {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        }
      })
      if (!res.ok) throw new Error('Failed to send message')
      const message = await res.json()
      messages.value.push(message)
      return message
    } catch (e) {
      error.value = e.message
      throw e
    }
  }

  const createConversation = async (receiverIds, title = null) => {
    error.value = null
    try {
      const res = await fetch(`${baseUrl}/conversations`, {
        method: 'POST',
        headers: headers(),
        body: JSON.stringify({ receiver_ids: receiverIds, title })
      })
      if (!res.ok) throw new Error('Failed to create conversation')
      const conversation = await res.json()
      conversations.value.unshift(conversation)
      return conversation
    } catch (e) {
      error.value = e.message
      throw e
    }
  }

  const addReaction = async (messageId, emoji) => {
    error.value = null
    try {
      const res = await fetch(`${baseUrl}/messages/${messageId}/reactions`, {
        method: 'POST',
        headers: headers(),
        body: JSON.stringify({ emoji })
      })
      if (!res.ok) throw new Error('Failed to add reaction')
      return await res.json()
    } catch (e) {
      error.value = e.message
      throw e
    }
  }

  const removeReaction = async (messageId, emoji) => {
    error.value = null
    try {
      const res = await fetch(`${baseUrl}/messages/${messageId}/reactions`, {
        method: 'DELETE',
        headers: headers(),
        body: JSON.stringify({ emoji })
      })
      if (!res.ok) throw new Error('Failed to remove reaction')
      return true
    } catch (e) {
      error.value = e.message
      throw e
    }
  }

  const markAsRead = async (messageId) => {
    error.value = null
    try {
      const res = await fetch(`${baseUrl}/messages/${messageId}/read`, {
        method: 'POST',
        headers: headers()
      })
      if (!res.ok) throw new Error('Failed to mark as read')
      return await res.json()
    } catch (e) {
      error.value = e.message
      throw e
    }
  }

  const broadcastTyping = async (conversationId, isTyping) => {
    try {
      await fetch(`${baseUrl}/conversations/${conversationId}/typing`, {
        method: 'POST',
        headers: headers(),
        body: JSON.stringify({ is_typing: isTyping })
      })
    } catch (e) {
      // Silent fail for typing
    }
  }

  const deleteMessage = async (messageId) => {
    error.value = null
    try {
      const res = await fetch(`${baseUrl}/messages/${messageId}`, {
        method: 'DELETE',
        headers: headers()
      })
      if (!res.ok) throw new Error('Failed to delete message')
      messages.value = messages.value.filter(m => m.id !== messageId)
      return true
    } catch (e) {
      error.value = e.message
      throw e
    }
  }

  const editMessage = async (messageId, body) => {
    error.value = null
    try {
      const res = await fetch(`${baseUrl}/messages/${messageId}`, {
        method: 'PUT',
        headers: headers(),
        body: JSON.stringify({ body })
      })
      if (!res.ok) throw new Error('Failed to edit message')
      const updated = await res.json()
      const index = messages.value.findIndex(m => m.id === messageId)
      if (index > -1) messages.value[index] = updated
      return updated
    } catch (e) {
      error.value = e.message
      throw e
    }
  }

  return {
    conversations,
    messages,
    loading,
    error,
    fetchConversations,
    fetchMessages,
    sendMessage,
    createConversation,
    addReaction,
    removeReaction,
    markAsRead,
    broadcastTyping,
    deleteMessage,
    editMessage
  }
}
