<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;
const isSeller = Number(user.role) === 2;

const form = useForm({
    _method: 'PATCH',
    name: user.name,
    email: user.email,
    nik_penjual: user.nik_penjual,
    phone: user.phone,
    sertifikasi_jenis: user.sertifikasi_jenis || '',
    sertifikasi_file: null,
    sosmed_instagram: user.sosmed_instagram || '',
    sosmed_tiktok: user.sosmed_tiktok || '',
    qris_image: null,
    location_map: user.location_map || '',
});

function useCurrentStoreLocation() {
    if (!navigator.geolocation) return

    navigator.geolocation.getCurrentPosition((position) => {
        const { latitude, longitude } = position.coords
        form.location_map = `https://maps.google.com/?q=${latitude},${longitude}`
    })
}

watch(() => form.location_map, async (newVal) => {
    if (!newVal) return
    if (newVal.includes('maps.app.goo.gl') || newVal.includes('goo.gl')) {
        try {
            const res = await fetch(route('resolve-maps-link'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ url: newVal })
            })
            const data = await res.json()
            if (data.success) {
                form.location_map = `${data.lat},${data.lng}`
            }
        } catch (err) {
            console.error(err)
        }
    }
})

const sertifikasiPreview = ref(user.sertifikasi_file ? `/storage/${user.sertifikasi_file}` : null);
const qrisPreview = ref(user.qris_image ? `/storage/${user.qris_image}` : null);
const sertifikasiFileName = ref(user.sertifikasi_file ? user.sertifikasi_file.split('/').pop() : '')

function sertifikasiStatusLabel(status) {
    if (status === 'approved') return 'Disetujui';
    if (status === 'rejected') return 'Ditolak';
    if (status === 'pending') return 'Menunggu Persetujuan';
    return 'Belum Diupload';
}

function sertifikasiStatusClass(status) {
    if (status === 'approved') return 'bg-green-100 text-green-700 border-green-200';
    if (status === 'rejected') return 'bg-red-100 text-red-700 border-red-200';
    if (status === 'pending') return 'bg-yellow-100 text-yellow-800 border-yellow-200';
    return 'bg-gray-100 text-gray-700 border-gray-200';
}

function onQrisFileChange(event) {
    const file = event.target.files?.[0] || null;
    form.qris_image = file;

    if (file) {
        qrisPreview.value = URL.createObjectURL(file);
    }
}

function onSertifikasiFileChange(event) {
    const file = event.target.files?.[0] || null;
    form.sertifikasi_file = file;
    sertifikasiFileName.value = file ? file.name : '';

    if (file) {
        sertifikasiPreview.value = URL.createObjectURL(file);
    }
}

function sanitizeNik(event) {
    const digitsOnly = (event.target.value || '').replace(/\D/g, '').slice(0, 16);
    form.nik_penjual = digitsOnly;
}

const showNotification = ref(false)

function submitProfile() {
    form.post(route('profile.update'), {
        forceFormData: true,
        onSuccess: () => {
            showNotification.value = true
            setTimeout(() => {
                showNotification.value = false
            }, 3000)
        }
    });
}
</script>

<template>
    <section class="relative">
        <!-- 🔔 NOTIFICATION -->
        <transition name="fade">
            <div
                v-if="showNotification"
                class="fixed top-5 right-5 z-50 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-lg flex items-center gap-2 animate-fade-in"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-600">
                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9 9-9s9 3.615 9 9-4.365 9-9 9-9-3.615-9-9Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.74-5.24Z" clip-rule="evenodd" />
                </svg>
                <span class="font-bold text-xs uppercase tracking-wider text-emerald-700">Berhasil</span>
                <span class="text-xs text-gray-500 font-medium border-l border-gray-200 pl-2">Profil berhasil diperbarui.</span>
            </div>
        </transition>

        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Profile Information
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Update your account's profile information and email address.
            </p>
        </header>

        <form
            @submit.prevent="submitProfile"
            class="mt-6 space-y-6"
        >

            <div>
                <InputLabel for="name" value="Nama Akun" />
                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus autocomplete="name" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div class="mt-4">
                <InputLabel for="phone" value="No HP" />
                <TextInput id="phone" type="text" class="mt-1 block w-full" v-model="form.phone" />
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <div v-if="isSeller" class="space-y-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Informasi Toko</h3>
                    <p class="mt-1 text-sm text-gray-600">Bagian ini hanya untuk akun penjual.</p>
                </div>

                <div>
                    <InputLabel for="nik_penjual" value="NIK Penjual" />
                    <TextInput
                        id="nik_penjual"
                        type="text"
                        inputmode="numeric"
                        maxlength="16"
                        class="mt-1 block w-full"
                        v-model="form.nik_penjual"
                        @input="sanitizeNik"
                    />
                    <p class="mt-1 text-xs text-gray-500">Hanya 16 digit angka.</p>
                    <InputError class="mt-2" :message="form.errors.nik_penjual" />
                </div>
                <div>
                    <InputLabel for="sertifikasi_jenis" value="Sertifikasi" />
                    <select
                        id="sertifikasi_jenis"
                        v-model="form.sertifikasi_jenis"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">Pilih Sertifikasi</option>
                        <option value="halal">Sertifikasi Halal</option>
                        <option value="haki">Sertifikasi HAKI</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.sertifikasi_jenis" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">Status Sertifikasi</p>
                    <span
                        class="mt-2 inline-flex rounded-full border px-3 py-1 text-xs font-semibold"
                        :class="sertifikasiStatusClass(user.sertifikasi_status)"
                    >
                        {{ sertifikasiStatusLabel(user.sertifikasi_status) }}
                    </span>
                </div>
                <div>
                    <InputLabel for="sertifikasi_file" value="Upload Sertifikasi" />
                    <input
                        id="sertifikasi_file"
                        type="file"
                        accept="image/png,image/jpeg,image/jpg,image/webp,application/pdf"
                        class="mt-1 block w-full rounded border border-gray-300 px-3 py-2 text-sm"
                        @change="onSertifikasiFileChange"
                    />
                    <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, WEBP, PDF. Maks 4MB.</p>
                    <InputError class="mt-2" :message="form.errors.sertifikasi_file" />

                    <div v-if="sertifikasiPreview" class="mt-3 space-y-2">
                        <p class="text-xs text-gray-600">File sertifikasi:</p>
                        <a
                            :href="sertifikasiPreview"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-sm text-indigo-600 hover:underline"
                        >
                            {{ sertifikasiFileName || 'Lihat file sertifikasi' }}
                        </a>
                    </div>
                </div>

                <div>
                    <InputLabel for="sosmed_instagram" value="Link Instagram" />
                    <TextInput id="sosmed_instagram" type="url" class="mt-1 block w-full" v-model="form.sosmed_instagram" placeholder="https://instagram.com/..." />
                    <InputError class="mt-2" :message="form.errors.sosmed_instagram" />
                </div>
                <div>
                    <InputLabel for="sosmed_tiktok" value="Link TikTok" />
                    <TextInput id="sosmed_tiktok" type="url" class="mt-1 block w-full" v-model="form.sosmed_tiktok" placeholder="https://tiktok.com/..." />
                    <InputError class="mt-2" :message="form.errors.sosmed_tiktok" />
                </div>

                <div>
                    <InputLabel for="qris_image" value="Upload QR QRIS" />
                    <input
                        id="qris_image"
                        type="file"
                        accept="image/png,image/jpeg,image/jpg,image/webp"
                        class="mt-1 block w-full rounded border border-gray-300 px-3 py-2 text-sm"
                        @change="onQrisFileChange"
                    />
                    <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, WEBP. Maks 2MB.</p>
                    <InputError class="mt-2" :message="form.errors.qris_image" />

                    <div v-if="qrisPreview" class="mt-3">
                        <p class="mb-2 text-xs text-gray-600">Preview QRIS:</p>
                        <img :src="qrisPreview" alt="QRIS Preview" class="h-40 w-40 rounded border object-contain bg-white p-2" />
                    </div>
                </div>

                <div class="border-t pt-4 mt-4 space-y-4">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-800">Lokasi Toko (Link Google Maps / Koordinat)</h4>
                        <p class="text-xs text-gray-500">Gunakan lokasi saat ini atau paste link Google Maps untuk membatasi jarak pengantar kurir maksimal 5km.</p>
                    </div>

                    <div>
                        <InputLabel for="location_map" value="Link Google Maps / Koordinat" />
                        <TextInput
                            id="location_map"
                            type="text"
                            class="mt-1 block w-full bg-white"
                            v-model="form.location_map"
                            placeholder="Contoh: https://maps.google.com/?q=-7.5612,110.8491 atau -7.5612,110.8491"
                        />
                        <InputError class="mt-2" :message="form.errors.location_map" />
                    </div>

                    <div>
                        <button
                            type="button"
                            @click="useCurrentStoreLocation"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition cursor-pointer"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            Gunakan Lokasi Saat Ini
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
