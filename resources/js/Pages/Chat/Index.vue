<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <!-- Header with Mode Selector -->
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-white">AI Чат Асистент</h1>
                                <p class="text-indigo-100 text-sm">Виберіть режим роботи</p>
                            </div>
                        </div>
                        <button
                            v-if="localMessages.length > 0"
                            @click="clearChat"
                            class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg transition-colors duration-200 flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Очистити
                        </button>
                    </div>

                    <!-- Mode Selector -->
                    <div class="flex gap-3">
                        <button
                            v-for="mode in modes"
                            :key="mode.value"
                            @click="selectedMode = mode.value"
                            class="flex-1 px-4 py-3 rounded-lg font-medium transition-all duration-200"
                            :class="selectedMode === mode.value
                                ? 'bg-white text-indigo-600 shadow-lg'
                                : 'bg-white/10 text-white hover:bg-white/20'"
                        >
                            <div class="flex items-center justify-center gap-2">
                                <component :is="mode.icon" />
                                <span>{{ mode.label }}</span>
                            </div>
                        </button>
                    </div>

                    <!-- Mode Description -->
                    <div class="mt-4 p-4 bg-white/10 rounded-lg">
                        <p class="text-white text-sm">
                            {{ getModeDescription() }}
                        </p>
                    </div>
                </div>

                <!-- Messages Area -->
                <div
                    ref="messagesContainer"
                    class="h-[500px] overflow-y-auto p-6 space-y-4 bg-gradient-to-b from-gray-50 to-white"
                >
                    <div v-if="localMessages.length === 0" class="h-full flex items-center justify-center">
                        <div class="text-center">
                            <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center">
                                <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Почніть розмову</h3>
                            <p class="text-gray-500 max-w-sm">Виберіть режим та напишіть ваше повідомлення</p>
                        </div>
                    </div>

                    <!-- Message Bubbles -->
                    <div v-for="(message, index) in localMessages" :key="index" class="animate-fade-in">
                        <!-- User Message -->
                        <div v-if="message.type === 'user'" class="flex justify-end mb-4">
                            <div class="max-w-[80%]">
                                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-2xl rounded-tr-sm px-5 py-3 shadow-lg">
                                    <p class="whitespace-pre-wrap break-words">{{ message.content }}</p>
                                </div>
                                <p class="text-xs text-gray-400 mt-1 text-right">{{ formatTime(message.created_at) }}</p>
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
                                <p class="text-xs text-gray-400 mt-1">{{ formatTime(message.created_at) }}</p>
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
                                :placeholder="getPlaceholder()"
                                rows="2"
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
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, nextTick, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'
import MarkdownIt from 'markdown-it'
import hljs from 'highlight.js'

const props = defineProps({
    messages: Array
})

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
const localMessages = ref([...props.messages])
const isLoading = ref(false)
const messagesContainer = ref(null)
const selectedMode = ref('general')

const modes = [
    {
        value: 'general',
        label: 'Спілкування',
        icon: 'ChatIcon',
        description: 'Звичайна розмова з AI асистентом'
    },
    {
        value: 'biography',
        label: 'Біографії',
        icon: 'UserIcon',
        description: 'Створення біографій видатних осіб з автоматичним збереженням'
    },
    {
        value: 'news',
        label: 'Новини',
        icon: 'NewsIcon',
        description: 'Створення новин з автоматичним збереженням'
    }
]

const ChatIcon = {
    template: `
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
    `
}

const UserIcon = {
    template: `
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
    `
}

const NewsIcon = {
    template: `
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
        </svg>
    `
}

const renderMarkdown = (text) => {
    return md.render(text)
}

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
        }
    })
}

const formatTime = (timestamp) => {
    return new Date(timestamp).toLocaleTimeString('uk-UA', { hour: '2-digit', minute: '2-digit' })
}

const getModeDescription = () => {
    return modes.find(m => m.value === selectedMode.value)?.description || ''
}

const getPlaceholder = () => {
    switch (selectedMode.value) {
        case 'biography':
            return 'Напр: Створи біографію Тараса Шевченка...'
        case 'news':
            return 'Напр: Створи новину про нові технології...'
        default:
            return 'Напишіть ваше повідомлення... (Enter для відправки)'
    }
}

const clearChat = async () => {
    if (confirm('Ви впевнені, що хочете очистити історію чату?')) {
        try {
            await window.axios.post('/chat/clear')
            localMessages.value = []
        } catch (error) {
            console.error('Error clearing chat:', error)
        }
    }
}

const sendPrompt = async () => {
    if (!prompt.value.trim() || isLoading.value) return

    const userMessage = {
        type: 'user',
        content: prompt.value,
        mode: selectedMode.value,
        created_at: new Date().toISOString()
    }

    localMessages.value.push(userMessage)
    const userPrompt = prompt.value
    prompt.value = ''
    isLoading.value = true

    scrollToBottom()

    try {
        const response = await window.axios.post('/chat/send', {
            prompt: userPrompt,
            mode: selectedMode.value
        })

        if (response.data.success) {
            const aiMessage = response.data.message || {
                type: 'ai',
                content: response.data.response,
                mode: selectedMode.value,
                created_at: new Date().toISOString()
            }

            localMessages.value.push(aiMessage)

            // Якщо створено біографію або новину, показуємо повідомлення
            if (selectedMode.value === 'biography' || selectedMode.value === 'news') {
                setTimeout(() => {
                    alert(`${selectedMode.value === 'biography' ? 'Біографія' : 'Новина'} успішно створена!`)
                }, 500)
            }
        } else {
            localMessages.value.push({
                type: 'ai',
                content: response.data.response || 'Виникла помилка',
                mode: selectedMode.value,
                created_at: new Date().toISOString()
            })
        }
    } catch (error) {
        console.error('Error sending message:', error)
        localMessages.value.push({
            type: 'ai',
            content: 'Помилка з\'єднання з AI сервісом. Переконайтесь, що сервіс запущено.',
            mode: selectedMode.value,
            created_at: new Date().toISOString()
        })
    } finally {
        isLoading.value = false
        scrollToBottom()
    }
}
</script>

<style>
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
</style>

