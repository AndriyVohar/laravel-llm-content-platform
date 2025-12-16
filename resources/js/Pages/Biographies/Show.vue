<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto px-6 py-8">
            <!-- Back Button -->
            <Link
                href="/biographies"
                class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-6"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Назад до біографій
            </Link>

            <!-- Biography Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-8 text-white">
                    <h1 class="text-4xl font-bold mb-2">{{ biography.full_name }}</h1>
                    <p class="text-indigo-100 text-lg">
                        {{ formatYears(biography.birth_year, biography.death_year) }}
                    </p>
                </div>

                <div class="p-8">
                    <div
                        class="prose prose-lg max-w-none prose-headings:text-gray-900 prose-p:text-gray-700 prose-strong:text-gray-900 prose-ul:text-gray-700"
                        v-html="renderMarkdown(biography.description)"
                    ></div>
                </div>

                <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                    <p class="text-sm text-gray-500">
                        Створено: {{ formatDate(biography.created_at) }}
                    </p>
                    <button
                        @click="deleteBiography"
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
import MarkdownIt from 'markdown-it'

const props = defineProps({
    biography: Object
})

const md = new MarkdownIt({
    html: true,
    linkify: true,
    typographer: true,
    breaks: true
})

const renderMarkdown = (text) => {
    return md.render(text)
}

const formatYears = (birth, death) => {
    if (birth && death) {
        return `${birth} - ${death}`
    } else if (birth) {
        return `${birth} - наш час`
    } else {
        return 'Дати невідомі'
    }
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('uk-UA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const deleteBiography = () => {
    if (confirm('Ви впевнені, що хочете видалити цю біографію?')) {
        router.delete(`/biographies/${props.biography.id}`)
    }
}
</script>

