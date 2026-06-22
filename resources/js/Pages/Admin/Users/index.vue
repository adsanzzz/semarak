<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    admin: Array,
    penjual: Array,
    pembeli: Array
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
</script>

<template>
    <Head title="Kelola Akun" />

    <AdminLayout>
        <div class="bg-white shadow rounded-lg p-6">

            <h2 class="text-xl font-bold mb-6">
                Kelola Akun
            </h2>

            <!-- ================= ADMIN ================= -->
            <h3 class="text-lg font-bold mb-3">Admin</h3>

            <div v-if="admin.length === 0" class="text-gray-500 mb-4">
                Tidak ada admin
            </div>

            <div v-for="(user, index) in admin" :key="user.id"
                class="flex justify-between items-center border p-3 rounded mb-2">
                <div>
                    <div class="font-semibold">{{ user.name }}</div>
                    <div class="text-sm text-gray-500">{{ user.email }}</div>
                </div>

                <button
                    @click="deleteUser(user.id)"
                    class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm"
                >
                    Hapus
                </button>
            </div>


            <!-- ================= PENJUAL ================= -->
            <h3 class="text-lg font-bold mt-8 mb-3">Penjual</h3>

            <div v-if="penjual.length === 0" class="text-gray-500 mb-4">
                Tidak ada penjual
            </div>

            <div v-for="(user, index) in penjual" :key="user.id"
                class="flex justify-between items-center border p-3 rounded mb-2">
                <div>
                    <div class="font-semibold">{{ user.name }}</div>
                    <div class="text-sm text-gray-500">{{ user.email }}</div>
                    <div
                        class="mt-1 inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                        :class="isActive(user) ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                    >
                        {{ isActive(user) ? 'Aktif' : 'Dinonaktifkan' }}
                    </div>
                </div>

                <div class="flex gap-2">
                    <button
                        v-if="isActive(user)"
                        @click="openDeactivateModal(user)"
                        class="bg-amber-600 text-white px-3 py-1 rounded hover:bg-amber-700 text-sm"
                    >
                        Nonaktifkan
                    </button>
                    <button
                        v-else
                        @click="activateSeller(user.id)"
                        class="bg-emerald-600 text-white px-3 py-1 rounded hover:bg-emerald-700 text-sm"
                    >
                        Aktifkan
                    </button>

                    <button
                        @click="deleteUser(user.id)"
                        class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm"
                    >
                        Hapus
                    </button>
                </div>
            </div>


            <!-- ================= PEMBELI ================= -->
            <h3 class="text-lg font-bold mt-8 mb-3">Pembeli</h3>

            <div v-if="pembeli.length === 0" class="text-gray-500 mb-4">
                Tidak ada pembeli
            </div>

            <div v-for="(user, index) in pembeli" :key="user.id"
                class="flex justify-between items-center border p-3 rounded mb-2">
                <div>
                    <div class="font-semibold">{{ user.name }}</div>
                    <div class="text-sm text-gray-500">{{ user.email }}</div>
                </div>

                <button
                    @click="deleteUser(user.id)"
                    class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm"
                >
                    Hapus
                </button>
            </div>

        </div>

        <div v-if="deactivateModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
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
    </AdminLayout>
</template>