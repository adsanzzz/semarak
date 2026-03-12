<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    conversation: Object
})

const form = useForm({
    message: ''
})

function submit() {
    form.post(route('chat.send', props.conversation.id), {
        onSuccess: () => form.reset()
    })
}
</script>

<template>
    <div class="max-w-2xl mx-auto p-6">

        <div class="border p-4 h-96 overflow-y-auto mb-4">
            <div v-for="msg in conversation.messages" :key="msg.id" class="mb-2">
                <strong>{{ msg.sender.name }}:</strong>
                {{ msg.message }}
            </div>
        </div>

        <form @submit.prevent="submit">
            <input
                v-model="form.message"
                class="border w-full p-2"
                placeholder="Tulis pesan..."
            />
        </form>

    </div>
</template>