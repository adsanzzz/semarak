<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, router, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
    users: Object,
    type: String
})

const pageTitle = computed(() => {
    if (props.type === 'admin') return 'Kelola Akun Admin'
    if (props.type === 'penjual') return 'Kelola Akun Penjual'
    if (props.type === 'pembeli') return 'Kelola Akun Pembeli'
    return 'Kelola Akun'
})

function deleteUser(id) {
    if (confirm('Yakin ingin menghapus user ini?')) {
        router.delete(route('admin.users.destroy', id))
    }
}

const deactivateReasons = [
    'Menjual Barang yang Mencurigakan',
    'Melakukan Tindakan Penipuan',
    'Aktivitas Penjualan Tidak Aktif dalam Jangka Waktu Tertentu',
    'Lainnya',
]

const deactivateModalOpen = ref(false)
const selectedSeller = ref(null)
const selectedReason = ref('')
const customReason = ref('')

function openDeactivateModal(user) {
    selectedSeller.value = user
    selectedReason.value = deactivateReasons[0]
    customReason.value = ''
    deactivateModalOpen.value = true
}

function closeDeactivateModal() {
    deactivateModalOpen.value = false
    selectedSeller.value = null
    selectedReason.value = ''
    customReason.value = ''
}

function confirmDeactivate() {
    const reasonText = selectedReason.value === 'Lainnya'
        ? customReason.value.trim()
        : selectedReason.value

    if (!selectedSeller.value || !reasonText) {
        return
    }

    router.post(route('admin.users.deactivate', selectedSeller.value.id), {
        reason: reasonText,
    }, {
        onSuccess: () => {
            closeDeactivateModal()
        },
    })
}

function activateSeller(id) {
    if (confirm('Yakin ingin mengaktifkan kembali akun penjual ini?')) {
        router.post(route('admin.users.activate', id))
    }
}

function isActive(user) {
    return Number(user.is_active) === 1
}

// ========================
// DETAIL MODAL LOGIC
// ========================
const detailModalOpen = ref(false)
const selectedUser = ref(null)

function openDetailModal(user) {
    selectedUser.value = user
    detailModalOpen.value = true
}

function closeDetailModal() {
    detailModalOpen.value = false
    selectedUser.value = null
}

function getInitials(name) {
    if (!name) return '?'
    const parts = name.split(' ')
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase()
    }
    return name.slice(0, 2).toUpperCase()
}
</script>

<template>
    <Head :title="pageTitle" />

    <AdminLayout>
        <div class="bg-gray-50/50 rounded-2xl border border-gray-100 p-6">

            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-[#0A3551] tracking-tight">
                        Kelola Akun Pengguna
                    </h2>
                    <p class="text-xs text-gray-400 mt-1">
                        Total terdaftar: {{ users.total }} akun
                    </p>
                </div>
            </div>

            <!-- Tabulasi (Tabs) -->
            <div class="flex border-b border-gray-200 mb-6 gap-2">
                <Link 
                    :href="route('admin.users.index', { type: 'admin' })"
                    class="px-5 py-3 text-sm font-bold border-b-2 transition duration-200 cursor-pointer"
                    :class="type === 'admin' 
                        ? 'border-indigo-600 text-indigo-600' 
                        : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300'"
                >
                    Akun Admin
                </Link>
                <Link 
                    :href="route('admin.users.index', { type: 'penjual' })"
                    class="px-5 py-3 text-sm font-bold border-b-2 transition duration-200 cursor-pointer"
                    :class="type === 'penjual' 
                        ? 'border-indigo-600 text-indigo-600' 
                        : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300'"
                >
                    Akun Penjual
                </Link>
                <Link 
                    :href="route('admin.users.index', { type: 'pembeli' })"
                    class="px-5 py-3 text-sm font-bold border-b-2 transition duration-200 cursor-pointer"
                    :class="type === 'pembeli' 
                        ? 'border-indigo-600 text-indigo-600' 
                        : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300'"
                >
                    Akun Pembeli
                </Link>
            </div>

            <!-- Empty State -->
            <div v-if="users.data.length === 0" class="py-16 text-center text-gray-400 text-sm bg-white rounded-xl border border-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto text-gray-300 mb-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                Tidak ada data akun untuk kategori ini.
            </div>

            <!-- Users Table -->
            <div v-else class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-bold text-gray-700">No</th>
                                <th scope="col" class="px-6 py-4 font-bold text-gray-700">Pengguna</th>
                                <th scope="col" v-if="type === 'penjual'" class="px-6 py-4 font-bold text-gray-700">Nama Toko</th>
                                <th scope="col" class="px-6 py-4 font-bold text-gray-700">Status</th>
                                <th scope="col" class="px-6 py-4 font-bold text-gray-700 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="(user, index) in users.data" :key="user.id" class="bg-white hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 text-gray-700 whitespace-nowrap">
                                    {{ (users.current_page - 1) * users.per_page + index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 flex items-center justify-center font-extrabold text-sm flex-shrink-0">
                                            {{ getInitials(user.name) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-800 text-sm">{{ user.name }}</div>
                                            <div class="text-xs text-gray-400 font-normal">{{ user.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td v-if="type === 'penjual'" class="px-6 py-4 text-gray-700 whitespace-nowrap font-medium">
                                    {{ user.nama_toko || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span 
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border"
                                        :class="[
                                            type === 'penjual' 
                                                ? (isActive(user) ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-700 border-red-100')
                                                : 'bg-blue-50 text-blue-700 border-blue-100'
                                        ]"
                                    >
                                        {{ type === 'penjual' ? (isActive(user) ? 'Toko Aktif' : 'Toko Dinonaktifkan') : 'Aktif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button 
                                            @click="openDetailModal(user)"
                                            class="inline-flex items-center gap-1 border border-indigo-100 text-indigo-600 hover:bg-indigo-50 font-bold px-3 py-1.5 rounded-xl text-xs transition cursor-pointer"
                                        >
                                            Detail
                                        </button>

                                        <!-- Penjual Buttons -->
                                        <template v-if="type === 'penjual'">
                                            <button
                                                v-if="isActive(user)"
                                                @click="openDeactivateModal(user)"
                                                class="inline-flex items-center gap-1 bg-amber-500 hover:bg-amber-600 text-white font-bold px-3 py-1.5 rounded-xl text-xs transition cursor-pointer"
                                            >
                                                Nonaktifkan
                                            </button>
                                            <button
                                                v-else
                                                @click="activateSeller(user.id)"
                                                class="inline-flex items-center gap-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3 py-1.5 rounded-xl text-xs transition cursor-pointer"
                                            >
                                                Aktifkan
                                            </button>
                                        </template>

                                        <!-- Hapus Button -->
                                        <button
                                            v-if="user.id !== $page.props.auth.user.id"
                                            @click="deleteUser(user.id)"
                                            class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white font-bold px-3 py-1.5 rounded-xl text-xs transition cursor-pointer"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 📄 PAGINATION -->
            <div v-if="users.links && users.links.length > 3" class="flex items-center justify-between border-t border-gray-100 bg-white px-4 py-4 sm:px-6 mt-6 rounded-xl">
                <div class="flex flex-1 justify-between sm:hidden">
                    <Link
                        :href="users.prev_page_url || '#'"
                        :class="['relative inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition', !users.prev_page_url ? 'opacity-50 cursor-not-allowed pointer-events-none' : '']"
                    >
                        Previous
                    </Link>
                    <Link
                        :href="users.next_page_url || '#'"
                        :class="['relative ml-3 inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition', !users.next_page_url ? 'opacity-50 cursor-not-allowed pointer-events-none' : '']"
                    >
                        Next
                    </Link>
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs text-gray-500">
                            Menampilkan <span class="font-bold text-gray-800">{{ users.from || 0 }}</span> sampai <span class="font-bold text-gray-800">{{ users.to || 0 }}</span> dari <span class="font-bold text-gray-800">{{ users.total }}</span> akun
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-xl shadow-xs" aria-label="Pagination">
                            <template v-for="(link, i) in users.links" :key="i">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    v-html="link.label"
                                    :class="[
                                        'relative inline-flex items-center px-3.5 py-2 text-xs font-semibold focus:z-20 transition-all duration-150',
                                        link.active 
                                            ? 'z-10 bg-[#0A3551] text-white rounded-xl mx-0.5 shadow-sm' 
                                            : 'text-gray-600 hover:bg-gray-50 rounded-xl mx-0.5 border border-transparent hover:border-gray-200',
                                    ]"
                                />
                                <span
                                    v-else
                                    v-html="link.label"
                                    class="relative inline-flex items-center px-3.5 py-2 text-xs font-semibold text-gray-300 rounded-xl mx-0.5 cursor-not-allowed"
                                />
                            </template>
                        </nav>
                    </div>
                </div>
            </div>

        </div>

        <!-- ℹ️ DETAIL ACCOUNT MODAL -->
        <transition name="fade">
            <div v-if="detailModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 transition-all duration-300">
                    <!-- Header -->
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-base font-bold text-gray-800">Detail Akun</h3>
                        <button @click="closeDetailModal" class="text-gray-400 hover:text-gray-600 transition cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Body -->
                    <div class="p-6 space-y-5">
                        <!-- Profile Header -->
                        <div class="flex items-center gap-4 border-b border-gray-50 pb-4">
                            <div class="w-14 h-14 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 flex items-center justify-center font-extrabold text-xl">
                                {{ getInitials(selectedUser.name) }}
                            </div>
                            <div>
                                <h4 class="font-extrabold text-gray-800 text-sm">{{ selectedUser.name }}</h4>
                                <p class="text-xs text-gray-400 mt-0.5">{{ selectedUser.email }}</p>
                            </div>
                        </div>
                        
                        <!-- Account Details Grid -->
                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="text-gray-400 block mb-0.5">Nama Toko</span>
                                <span class="font-bold text-gray-800">{{ selectedUser.nama_toko || '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block mb-0.5">Status Akun/Toko</span>
                                <div>
                                    <span 
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border"
                                        :class="[
                                            selectedUser.role === 2
                                                ? (isActive(selectedUser) ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-700 border-red-100')
                                                : 'bg-blue-50 text-blue-700 border-blue-100'
                                        ]"
                                    >
                                        {{ selectedUser.role === 2 ? (isActive(selectedUser) ? 'Toko Aktif' : 'Toko Dinonaktifkan') : 'Aktif' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Grid Cards -->
                        <div class="grid grid-cols-3 gap-3 border-t border-gray-50 pt-4">
                            <div class="bg-gray-50 rounded-xl p-3 text-center">
                                <span class="block text-[10px] text-gray-400 font-semibold mb-1">Jumlah Produk</span>
                                <span class="block text-lg font-extrabold text-gray-800">{{ selectedUser.products_count }}</span>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-3 text-center">
                                <span class="block text-[10px] text-gray-400 font-semibold mb-1">Jumlah Pesanan</span>
                                <span class="block text-lg font-extrabold text-gray-800">{{ selectedUser.orders_count }}</span>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-3 text-center">
                                <span class="block text-[10px] text-gray-400 font-semibold mb-1 text-red-500">Laporan Akun</span>
                                <span class="block text-lg font-extrabold text-red-600">{{ selectedUser.complaints_count }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                        <button 
                            @click="closeDetailModal" 
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-2.5 rounded-xl text-xs transition cursor-pointer shadow-sm hover:shadow"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </transition>

        <!-- 🚨 NONAKTIFKAN MODAL -->
        <transition name="fade">
            <div v-if="deactivateModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
                <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Pilih alasan nonaktifkan akun</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Pilih alasan yang sesuai sebelum akun penjual dinonaktifkan.
                    </p>

                    <div class="mt-5 space-y-3">
                        <label
                            v-for="reason in deactivateReasons"
                            :key="reason"
                            class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 hover:bg-gray-50"
                        >
                            <input
                                v-model="selectedReason"
                                type="radio"
                                name="deactivate-reason"
                                :value="reason"
                                class="h-4 w-4 border-gray-300 text-amber-600 focus:ring-amber-500"
                            />
                            <span class="text-sm text-gray-700">{{ reason }}</span>
                        </label>
                    </div>

                    <div v-if="selectedReason === 'Lainnya'" class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tulis alasan lainnya
                        </label>
                        <textarea
                            v-model="customReason"
                            rows="4"
                            placeholder="Tulis alasan nonaktifkan akun"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-100 resize-none"
                        ></textarea>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button
                            type="button"
                            @click="closeDeactivateModal"
                            class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="confirmDeactivate"
                            class="rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700"
                        >
                            Nonaktifkan
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </AdminLayout>
</template>