<template>
    <div class="min-h-screen bg-gradient-to-br from-indigo-100 via-purple-50 to-pink-100">
        <!-- Header -->
        <div class="bg-white/80 backdrop-blur-sm shadow-sm border-b border-gray-200">
            <div class="max-w-5xl mx-auto px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                AI Chat Assistant
                            </h1>
                            <p class="text-sm text-gray-500">Powered by Ollama</p>
                        </div>
                    </div>
                    <button
                        v-if="messages.length > 0"
                        @click="clearChat"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200 flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Очистити чат
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="max-w-5xl mx-auto px-6 py-8">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <!-- Messages Area -->
                <div
                    ref="messagesContainer"
                    class="h-[600px] overflow-y-auto p-6 space-y-4 bg-gradient-to-b from-gray-50 to-white"
                >
                    <div v-if="messages.length === 0" class="h-full flex items-center justify-center">
                        <div class="text-center">
                            <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center">
                                <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Почніть розмову</h3>
                            <p class="text-gray-500 max-w-sm">Напишіть ваше запитання або повідомлення в поле нижче</p>
                        </div>
                    </div>

                    <!-- Message Bubbles -->
                    <div v-for="(message, index) in messages" :key="index" class="animate-fade-in">
                        <!-- User Message -->
                        <div v-if="message.type === 'user'" class="flex justify-end mb-4">
                            <div class="max-w-[80%]">
                                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-2xl rounded-tr-sm px-5 py-3 shadow-lg">
                                    <p class="whitespace-pre-wrap break-words">{{ message.content }}</p>
                                </div>
                                <p class="text-xs text-gray-400 mt-1 text-right">{{ message.time }}</p>
                            </div>
                        </div>

                        <!-- AI Message -->
                        <div v-else class="flex justify-start mb-4">
                            <div class="max-w-[80%]">
                                <div class="bg-white border border-gray-200 rounded-2xl rounded-tl-sm px-5 py-3 shadow-md">
                                    <div
                                        class="text-gray-800 prose prose-sm max-w-none prose-pre:bg-gray-900 prose-pre:text-gray-100"
                                        v-html="renderMarkdown(message.content)"
                                    ></div>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">{{ message.time }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Loading Indicator -->
                    <div v-if="isLoading" class="flex justify-start mb-4 animate-fade-in">
                        <div class="bg-white border border-gray-200 rounded-2xl rounded-tl-sm px-5 py-3 shadow-md">
                            <div class="flex items-center gap-2">
                                <div class="flex gap-1">
                                    <span class="w-2 h-2 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                                    <span class="w-2 h-2 bg-purple-500 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                                    <span class="w-2 h-2 bg-pink-500 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                                </div>
                                <span class="text-sm text-gray-500">AI думає...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Input Area -->
                <div class="border-t border-gray-200 bg-white p-4">
                    <form @submit.prevent="sendPrompt" class="flex gap-3">
                        <div class="flex-1">
                            <textarea
                                v-model="prompt"
                                @keydown.enter.exact.prevent="sendPrompt"
                                placeholder="Напишіть ваше повідомлення... (Enter для відправки)"
                                rows="1"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none transition-all duration-200"
                                :disabled="isLoading"
                            ></textarea>
                        </div>
                        <button
                            type="submit"
                            :disabled="!prompt.trim() || isLoading"
                            class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl font-medium shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 flex items-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            <span>Відправити</span>
                        </button>
                    </form>
                    <p class="text-xs text-gray-400 mt-2 text-center">
                        Переконайтесь, що Ollama запущено (localhost:11434)
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, nextTick } from 'vue'
import MarkdownIt from 'markdown-it'
import hljs from 'highlight.js'

// Initialize markdown-it with syntax highlighting
const md = new MarkdownIt({
    html: true,
    linkify: true,
    typographer: true,
    breaks: true,
    highlight: function (str, lang) {
        if (lang && hljs.getLanguage(lang)) {
            try {
                return '<pre class="hljs"><code>' +
                    hljs.highlight(str, { language: lang, ignoreIllegals: true }).value +
                    '</code></pre>'
            } catch (__) {}
        }
        return '<pre class="hljs"><code>' + md.utils.escapeHtml(str) + '</code></pre>'
    }
})

const prompt = ref('')
const messages = ref([])
const isLoading = ref(false)
const messagesContainer = ref(null)

// Render markdown to HTML
const renderMarkdown = (text) => {
    return md.render(text)
}

// Auto-scroll to bottom
const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
        }
    })
}

// Get current time
const getCurrentTime = () => {
    const now = new Date()
    return now.toLocaleTimeString('uk-UA', { hour: '2-digit', minute: '2-digit' })
}

// Clear chat history
const clearChat = () => {
    if (confirm('Ви впевнені, що хочете очистити історію чату?')) {
        messages.value = []
    }
}

// Send prompt
const sendPrompt = async () => {
    if (!prompt.value.trim() || isLoading.value) return

    const userMessage = {
        type: 'user',
        content: prompt.value,
        time: getCurrentTime()
    }

    messages.value.push(userMessage)
    const userPrompt = prompt.value
    prompt.value = ''
    isLoading.value = true

    scrollToBottom()

    try {
        const response = await window.axios.post('/ai-chat', {
            prompt: userPrompt
        })

        messages.value.push({
            type: 'ai',
            content: response.data.response || 'Немає відповіді',
            time: getCurrentTime()
        })
    } catch (error) {
        const errorMessage = error.response?.data?.response
            || error.response?.data?.message
            || error.message
            || 'Помилка: Щось пішло не так'

        messages.value.push({
            type: 'ai',
            content: errorMessage,
            time: getCurrentTime()
        })
    } finally {
        isLoading.value = false
        scrollToBottom()
    }
}
</script>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #6366f1, #9333ea);
    border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #4f46e5, #7c3aed);
}

/* Markdown styles */
:deep(.prose) {
    color: #1f2937;
}

:deep(.prose h1) {
    font-size: 1.5em;
    font-weight: 700;
    margin-top: 0.5em;
    margin-bottom: 0.5em;
    color: #111827;
}

:deep(.prose h2) {
    font-size: 1.3em;
    font-weight: 600;
    margin-top: 0.5em;
    margin-bottom: 0.4em;
    color: #1f2937;
}

:deep(.prose h3) {
    font-size: 1.15em;
    font-weight: 600;
    margin-top: 0.4em;
    margin-bottom: 0.3em;
    color: #374151;
}

:deep(.prose p) {
    margin-top: 0.5em;
    margin-bottom: 0.5em;
    line-height: 1.6;
}

:deep(.prose ul),
:deep(.prose ol) {
    margin-top: 0.5em;
    margin-bottom: 0.5em;
    padding-left: 1.5em;
}

:deep(.prose li) {
    margin-top: 0.25em;
    margin-bottom: 0.25em;
}

:deep(.prose strong) {
    font-weight: 600;
    color: #111827;
}

:deep(.prose em) {
    font-style: italic;
}

:deep(.prose code) {
    background: #f3f4f6;
    padding: 0.15em 0.4em;
    border-radius: 0.25rem;
    font-size: 0.9em;
    color: #dc2626;
    font-family: 'Courier New', monospace;
}

:deep(.prose pre) {
    background: #1e293b !important;
    color: #e2e8f0 !important;
    padding: 1em;
    border-radius: 0.5rem;
    overflow-x: auto;
    margin-top: 0.75em;
    margin-bottom: 0.75em;
    font-size: 0.875em;
    line-height: 1.5;
}

:deep(.prose pre code) {
    background: transparent;
    padding: 0;
    color: inherit;
    font-size: inherit;
    border-radius: 0;
}

:deep(.prose blockquote) {
    border-left: 4px solid #6366f1;
    padding-left: 1em;
    margin-left: 0;
    margin-top: 0.75em;
    margin-bottom: 0.75em;
    color: #4b5563;
    font-style: italic;
}

:deep(.prose a) {
    color: #6366f1;
    text-decoration: underline;
}

:deep(.prose a:hover) {
    color: #4f46e5;
}

:deep(.prose table) {
    width: 100%;
    margin-top: 0.75em;
    margin-bottom: 0.75em;
    border-collapse: collapse;
}

:deep(.prose th) {
    background: #f3f4f6;
    padding: 0.5em;
    border: 1px solid #e5e7eb;
    font-weight: 600;
    text-align: left;
}

:deep(.prose td) {
    padding: 0.5em;
    border: 1px solid #e5e7eb;
}

:deep(.prose hr) {
    border: none;
    border-top: 1px solid #e5e7eb;
    margin-top: 1em;
    margin-bottom: 1em;
}

/* Syntax highlighting for code blocks */
:deep(.hljs) {
    display: block;
    overflow-x: auto;
    padding: 1em;
    background: #1e293b;
    color: #e2e8f0;
    border-radius: 0.5rem;
}

:deep(.hljs-comment),
:deep(.hljs-quote) {
    color: #64748b;
    font-style: italic;
}

:deep(.hljs-keyword),
:deep(.hljs-selector-tag),
:deep(.hljs-subst) {
    color: #c084fc;
    font-weight: bold;
}

:deep(.hljs-number),
:deep(.hljs-literal),
:deep(.hljs-variable),
:deep(.hljs-template-variable),
:deep(.hljs-tag .hljs-attr) {
    color: #fb7185;
}

:deep(.hljs-string),
:deep(.hljs-doctag) {
    color: #86efac;
}

:deep(.hljs-title),
:deep(.hljs-section),
:deep(.hljs-selector-id) {
    color: #60a5fa;
    font-weight: bold;
}

:deep(.hljs-type),
:deep(.hljs-class .hljs-title) {
    color: #fbbf24;
}

:deep(.hljs-tag),
:deep(.hljs-name),
:deep(.hljs-attribute) {
    color: #a78bfa;
    font-weight: normal;
}

:deep(.hljs-regexp),
:deep(.hljs-link) {
    color: #34d399;
}

:deep(.hljs-symbol),
:deep(.hljs-bullet) {
    color: #fb923c;
}

:deep(.hljs-built_in),
:deep(.hljs-builtin-name) {
    color: #67e8f9;
}

:deep(.hljs-meta) {
    color: #9ca3af;
}

:deep(.hljs-deletion) {
    background: #fecaca;
}

:deep(.hljs-addition) {
    background: #d1fae5;
}

:deep(.hljs-emphasis) {
    font-style: italic;
}

:deep(.hljs-strong) {
    font-weight: bold;
}
</style>

