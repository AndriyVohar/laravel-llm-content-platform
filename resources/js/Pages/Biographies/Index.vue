<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Біографії</h1>
                <p class="text-gray-600">Перегляньте біографії видатних осіб</p>
            </div>

            <!-- Empty State -->
            <div v-if="biographies.length === 0" class="text-center py-12">
                <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Біографій поки немає</h3>
                <p class="text-gray-500 mb-4">Створіть першу біографію через чат</p>
                <Link href="/chat" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg font-medium shadow-lg hover:shadow-xl transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    Перейти до чату
                </Link>
            </div>

            <!-- Biographies Grid -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="biography in biographies"
                    :key="biography.id"
                    class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-shadow duration-200"
                >
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">
                                    {{ biography.full_name }}
                                </h3>
                                <p class="text-sm text-gray-500">
                                    {{ formatYears(biography.birth_year, biography.death_year) }}
                                </p>
                            </div>
                            <button
                                @click="deleteBiography(biography.id)"
                                class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                title="Видалити"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="text-gray-600 line-clamp-3 mb-4">
                            {{ getPlainText(biography.description) }}
                        </div>

                        <Link
                            :href="`/biographies/${biography.id}`"
                            class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-700 font-medium"
                        >
                            Читати далі
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

defineProps({
    biographies: Array
})

const formatYears = (birth, death) => {
    if (birth && death) {
        return `${birth} - ${death}`
    } else if (birth) {
        return `${birth} - наш час`
    } else {
        return 'Дати невідомі'
    }
}

const getPlainText = (markdown) => {
    // Прості видалення markdown синтаксису
    return markdown
        .replace(/#{1,6}\s/g, '')
        .replace(/\*\*/g, '')
        .replace(/\*/g, '')
        .replace(/\[([^\]]+)\]\([^)]+\)/g, '$1')
        .substring(0, 150) + '...'
}

const deleteBiography = (id) => {
    if (confirm('Ви впевнені, що хочете видалити цю біографію?')) {
        router.delete(`/biographies/${id}`)
    }
}
</script>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

