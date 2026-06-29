<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    keywords: Array
})

const form = useForm({
    keyword: ''
})

const notif = ref(null)

function showNotif(message, type = 'success') {
    notif.value = { message, type }
    setTimeout(() => (notif.value = null), 3000)
}

function addKeyword() {
    if (!form.keyword.trim()) return
    
    form.post(route('admin.moderation.store'), {
        onSuccess: () => {
            form.reset()
            showNotif('Keyword filter berhasil ditambahkan')
        },
        onError: (errors) => {
            if (errors.keyword) {
                showNotif(errors.keyword, 'error')
            }
        }
    })
}

function removeKeyword(id) {
    if (confirm('Apakah Anda yakin ingin menghapus keyword filter ini?')) {
        router.delete(route('admin.moderation.destroy', id), {
            onSuccess: () => {
                showNotif('Keyword filter berhasil dihapus')
            }
        })
    }
}
</script>

<template>
    <Head title="Filter Peninjauan - Admin" />

    <AdminLayout>
        <!-- 🔔 NOTIFICATION -->
        <transition name="fade">
            <div
                v-if="notif"
                class="fixed top-5 right-5 z-50 border px-4 py-3 rounded-xl shadow-lg flex items-center gap-2 text-sm"
                :class="notif.type === 'error' ? 'bg-red-50 border-red-200 text-red-800' : 'bg-emerald-50 border-emerald-200 text-emerald-800'"
            >
                <svg v-if="notif.type === 'success'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-600">
                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9 9-9s9 3.615 9 9-4.365 9-9 9-9-3.615-9-9Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.74-5.24Z" clip-rule="evenodd" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-red-600">
                    <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd" />
                </svg>
                <span class="font-medium">{{ notif.message }}</span>
            </div>
        </transition>

        <div class="space-y-6">
            <!-- Header -->
            <div>
                <h2 class="text-2xl font-extrabold text-[#0A3551] tracking-tight">
                    Filter Peninjauan Produk
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Kelola daftar keyword/kata kunci berbahaya atau mencurigakan untuk memantau katalog produk toko.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                
                <!-- 📥 ADD KEYWORD CARD -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">Tambah Keyword Baru</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Semua kata kunci akan diproses dalam huruf kecil.</p>
                    </div>

                    <form @submit.prevent="addKeyword" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Kata Kunci</label>
                            <input 
                                v-model="form.keyword" 
                                type="text"
                                placeholder="Contoh: sabu, bom, racun"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                required
                            />
                            <p v-if="form.errors.keyword" class="text-xs text-red-500 mt-1">{{ form.errors.keyword }}</p>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full inline-flex justify-center items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white font-bold px-4 py-2.5 rounded-xl text-sm shadow-sm transition duration-150 cursor-pointer"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Tambah Keyword
                        </button>
                    </form>
                </div>

                <!-- 📋 KEYWORDS LIST CARD -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:col-span-2 space-y-6">
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">Keyword Terdaftar</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Total keyword: {{ keywords.length }} kata kunci</p>
                    </div>

                    <!-- Empty State -->
                    <div v-if="keywords.length === 0" class="py-12 text-center text-gray-400 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto text-gray-300 mb-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        Belum ada keyword filter yang ditambahkan.
                    </div>

                    <!-- Grid of Keywords -->
                    <div v-else class="flex flex-wrap gap-2.5">
                        <div 
                            v-for="kw in keywords" 
                            :key="kw.id" 
                            class="inline-flex items-center gap-1.5 bg-red-50 text-red-700 border border-red-100 rounded-lg px-3 py-1.5 text-xs font-bold transition hover:bg-red-100/50"
                        >
                            <span>{{ kw.keyword }}</span>
                            <button 
                                @click="removeKeyword(kw.id)" 
                                class="text-red-400 hover:text-red-700 transition cursor-pointer"
                                title="Hapus keyword"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                    <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>
