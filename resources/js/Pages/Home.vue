<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">LLM AI Chat</h1>
        <textarea v-model="form.prompt" placeholder="Напиши запит..." class="border p-2 w-full mb-2"></textarea>
        <button @click="sendPrompt" class="bg-blue-500 text-white px-4 py-2 rounded">Відправити</button>
        <div v-if="response" class="mt-4 p-2 border bg-gray-100">{{ response }}</div>
    </div>
</template>

<script>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

export default {
    setup() {
        const form = useForm({
            prompt: ''
        })

        const response = ref('')

        const sendPrompt = () => {
            form.post('/ai-chat', {
                onSuccess: page => response.value = page.props.response
            })
        }

        return {
            form,
            response,
            sendPrompt
        }
    }
}
</script>
