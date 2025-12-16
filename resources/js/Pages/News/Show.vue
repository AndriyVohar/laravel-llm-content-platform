<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto px-6 py-8">
            <!-- Back Button -->
            <Link
                href="/news"
                class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-6"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Назад до новин
            </Link>

            <!-- News Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="p-8 border-b border-gray-200">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ news.title }}</h1>
                    <div class="flex items-center gap-4 text-sm text-gray-500">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ formatDate(news.created_at) }}
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <div class="prose prose-lg max-w-none prose-p:text-gray-700">
                        <p class="whitespace-pre-wrap">{{ news.description }}</p>
                    </div>
                </div>

                <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 flex justify-end">
                    <button
                        @click="deleteNews"
                        class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors font-medium flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Видалити
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
    news: Object
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('uk-UA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const deleteNews = () => {
    if (confirm('Ви впевнені, що хочете видалити цю новину?')) {
        router.delete(`/news/${props.news.id}`)
    }
}
</script>

