/**
 * Convo Lite - Vue Components
 *
 * Usage:
 *
 * import { ConvoChat, useConvo } from './convo-lite'
 *
 * // Register globally
 * app.component('ConvoChat', ConvoChat)
 *
 * // Or use locally in a component
 * <ConvoChat :current-user-id="userId" />
 */

// Components
export { default as ConvoChat } from './components/ConvoLite/ConvoChat.vue'
export { default as ConversationList } from './components/ConvoLite/ConversationList.vue'
export { default as ChatWindow } from './components/ConvoLite/ChatWindow.vue'
export { default as MessageItem } from './components/ConvoLite/MessageItem.vue'
export { default as MessageInput } from './components/ConvoLite/MessageInput.vue'
export { default as TypingIndicator } from './components/ConvoLite/TypingIndicator.vue'
export { default as OnlineIndicator } from './components/ConvoLite/OnlineIndicator.vue'

// Composables
export { useConvo } from './composables/useConvo'

// Plugin
export default {
  install(app, options = {}) {
    const { prefix = 'Convo' } = options

    app.component(`${prefix}Chat`, () => import('./components/ConvoLite/ConvoChat.vue'))
    app.component(`${prefix}ConversationList`, () => import('./components/ConvoLite/ConversationList.vue'))
    app.component(`${prefix}ChatWindow`, () => import('./components/ConvoLite/ChatWindow.vue'))
    app.component(`${prefix}MessageItem`, () => import('./components/ConvoLite/MessageItem.vue'))
    app.component(`${prefix}MessageInput`, () => import('./components/ConvoLite/MessageInput.vue'))
    app.component(`${prefix}TypingIndicator`, () => import('./components/ConvoLite/TypingIndicator.vue'))
    app.component(`${prefix}OnlineIndicator`, () => import('./components/ConvoLite/OnlineIndicator.vue'))
  }
}
