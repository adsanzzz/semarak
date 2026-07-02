<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    satuans: {
        type: Array,
        default: () => []
    }
})

const notif = ref({
    show: false,
    type: 'success',
    message: ''
})

const form = useForm({
    nama_satuan: '',
})

const submit = () => {
    form.post(route('admin.satuans.store'), {
        onSuccess: () => {
            form.reset()
            notif.value = {
                show: true,
                type: 'success',
                message: 'Satuan berhasil ditambahkan'
            }
            setTimeout(() => {
                notif.value.show = false
            }, 3000)
        }
    })
}

const deleteSatuan = (id) => {
    if (confirm('Yakin ingin menghapus satuan produk ini?')) {
        router.delete(route('admin.satuans.destroy', id), {
            onSuccess: () => {
                notif.value = {
                    show: true,
                    type: 'success',
                    message: 'Satuan berhasil dihapus'
                }
                setTimeout(() => {
                    notif.value.show = false
                }, 3000)
            }
        })
    }
}
</script>

<template>
    <Head title="Kelola Satuan Produk" />

    <AdminLayout>
        <!-- NOTIF -->
        <div v-if="notif.show" class="fixed right-6 top-6 z-50 animate-in fade-in slide-in-from-top-4 duration-200">
            <div
                :class="notif.type === 'success' ? 'bg-emerald-600' : 'bg-rose-600'"
                class="text-white px-5 py-3 rounded-2xl shadow-xl font-medium text-sm flex items-center gap-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                {{ notif.message }}
            </div>
        </div>

        <div class="bg-white shadow rounded-2xl border border-gray-100 p-6">
            <!-- HEADER -->
            <div class="mb-6 pb-4 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-800">
                    Kelola Satuan Produk
                </h2>
                <p class="text-xs text-gray-400 mt-1">
                    Daftar satuan pengemasan atau penjualan produk
                </p>
            </div>

            <!-- TWO COLUMN LAYOUT -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- LEFT COLUMN: FORM -->
                <div class="lg:col-span-1 bg-gray-50/50 p-5 rounded-2xl border border-gray-100 h-fit">
                    <h3 class="text-sm font-bold text-gray-800 mb-4">Tambah Satuan Baru</h3>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-600 block mb-1.5">
                                Nama Satuan
                            </label>
                            <input
                                type="text"
                                v-model="form.nama_satuan"
                                placeholder="Contoh: Pcs, Kg, Box, Liter"
                                required
                                class="border border-gray-300 rounded-xl w-full px-3.5 py-2 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 focus:outline-none text-sm text-gray-900 transition-all font-medium bg-white"
                            />
                            <div v-if="form.errors.nama_satuan" class="text-rose-600 text-xs mt-1.5 font-medium">
                                {{ form.errors.nama_satuan }}
                            </div>
                        </div>

                        <button
                            type="submit"
                            class="w-full bg-[#0A3551] text-white px-4 py-2.5 rounded-xl hover:bg-[#07263c] font-bold text-xs shadow-md transition-all duration-200"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Tambah Satuan' }}
                        </button>
                    </form>
                </div>

                <!-- RIGHT COLUMN: TABLE -->
                <div class="lg:col-span-2">
                    <div class="overflow-hidden border border-gray-100 rounded-2xl shadow-xs">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 font-bold text-gray-700 w-16">No</th>
                                        <th scope="col" class="px-4 py-3 font-bold text-gray-700">Nama Satuan</th>
                                        <th scope="col" class="px-4 py-3 font-bold text-gray-700 text-center w-24">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    <tr v-for="(satuan, index) in props.satuans" :key="satuan.id" class="hover:bg-gray-50/50 transition">
                                        <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
                                            {{ index + 1 }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap font-bold text-gray-800">
                                            {{ satuan.nama_satuan }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <button
                                                @click.prevent="deleteSatuan(satuan.id)"
                                                class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white font-bold px-3 py-1.5 rounded-xl text-xs transition cursor-pointer shadow-sm hover:shadow"
                                            >
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="props.satuans.length === 0">
                                        <td colspan="3" class="px-4 py-8 text-center text-gray-400 font-medium">
                                            Belum ada data satuan produk.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
