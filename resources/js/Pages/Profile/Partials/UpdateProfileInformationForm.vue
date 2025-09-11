<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    nama_toko: user.nama_toko,
    nik_penjual: user.nik_penjual,
    nama_lengkap_penjual: user.nama_lengkap_penjual,
    phone: user.phone,
    alamat_penjual: user.alamat_penjual,
    provinsi: user.provinsi,
    kabupaten: user.kabupaten,
    kecamatan: user.kecamatan,
    kelurahan: user.kelurahan,
    kategori_usaha: user.kategori_usaha,
    modal_usaha: user.modal_usaha,
    omset_tahun: user.omset_tahun,
    sertifikasi_halal: user.sertifikasi_halal,
    sertifikasi_haki: user.sertifikasi_haki,
    sosmed: user.sosmed,
    tautan_marketplace: user.tautan_marketplace,
    informasi_kemitraan: user.informasi_kemitraan,
    pelatihan_usaha: user.pelatihan_usaha,
    bank_tujuan: user.bank_tujuan,
    nama_rekening: user.nama_rekening,
    norek: user.norek,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Profile Information
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Update your account's profile information and email address.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
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
                <InputLabel for="nama_toko" value="Nama Toko" />
                <TextInput id="nama_toko" type="text" class="mt-1 block w-full" v-model="form.nama_toko" />
                <InputError class="mt-2" :message="form.errors.nama_toko" />
            </div>
            <div class="mt-4">
                <InputLabel for="nik_penjual" value="NIK Penjual" />
                <TextInput id="nik_penjual" type="text" class="mt-1 block w-full" v-model="form.nik_penjual" />
                <InputError class="mt-2" :message="form.errors.nik_penjual" />
            </div>
            <div class="mt-4">
                <InputLabel for="nama_lengkap_penjual" value="Nama Lengkap Penjual" />
                <TextInput id="nama_lengkap_penjual" type="text" class="mt-1 block w-full" v-model="form.nama_lengkap_penjual" />
                <InputError class="mt-2" :message="form.errors.nama_lengkap_penjual" />
            </div>
            <div class="mt-4">
                <InputLabel for="phone" value="No HP" />
                <TextInput id="phone" type="text" class="mt-1 block w-full" v-model="form.phone" />
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>
            <div class="mt-4">
                <InputLabel for="alamat_penjual" value="Alamat Penjual" />
                <TextInput id="alamat_penjual" type="text" class="mt-1 block w-full" v-model="form.alamat_penjual" />
                <InputError class="mt-2" :message="form.errors.alamat_penjual" />
            </div>
            <div class="mt-4 grid grid-cols-2 gap-4">
                <div>
                    <InputLabel for="provinsi" value="Provinsi" />
                    <TextInput id="provinsi" type="text" class="mt-1 block w-full" v-model="form.provinsi" />
                    <InputError class="mt-2" :message="form.errors.provinsi" />
                </div>
                <div>
                    <InputLabel for="kabupaten" value="Kabupaten" />
                    <TextInput id="kabupaten" type="text" class="mt-1 block w-full" v-model="form.kabupaten" />
                    <InputError class="mt-2" :message="form.errors.kabupaten" />
                </div>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-4">
                <div>
                    <InputLabel for="kecamatan" value="Kecamatan" />
                    <TextInput id="kecamatan" type="text" class="mt-1 block w-full" v-model="form.kecamatan" />
                    <InputError class="mt-2" :message="form.errors.kecamatan" />
                </div>
                <div>
                    <InputLabel for="kelurahan" value="Kelurahan" />
                    <TextInput id="kelurahan" type="text" class="mt-1 block w-full" v-model="form.kelurahan" />
                    <InputError class="mt-2" :message="form.errors.kelurahan" />
                </div>
            </div>
            <div class="mt-4">
                <InputLabel for="kategori_usaha" value="Kategori Usaha" />
                <TextInput id="kategori_usaha" type="text" class="mt-1 block w-full" v-model="form.kategori_usaha" />
                <InputError class="mt-2" :message="form.errors.kategori_usaha" />
            </div>
            <div class="mt-4">
                <InputLabel for="modal_usaha" value="Modal Usaha" />
                <TextInput id="modal_usaha" type="text" class="mt-1 block w-full" v-model="form.modal_usaha" />
                <InputError class="mt-2" :message="form.errors.modal_usaha" />
            </div>
            <div class="mt-4">
                <InputLabel for="omset_tahun" value="Omset Usaha/Tahun" />
                <TextInput id="omset_tahun" type="text" class="mt-1 block w-full" v-model="form.omset_tahun" />
                <InputError class="mt-2" :message="form.errors.omset_tahun" />
            </div>
            <div class="mt-4">
                <InputLabel for="sertifikasi_halal" value="Sertifikasi Halal" />
                <TextInput id="sertifikasi_halal" type="text" class="mt-1 block w-full" v-model="form.sertifikasi_halal" />
                <InputError class="mt-2" :message="form.errors.sertifikasi_halal" />
            </div>
            <div class="mt-4">
                <InputLabel for="sertifikasi_haki" value="Sertifikasi HAKI" />
                <TextInput id="sertifikasi_haki" type="text" class="mt-1 block w-full" v-model="form.sertifikasi_haki" />
                <InputError class="mt-2" :message="form.errors.sertifikasi_haki" />
            </div>
            <div class="mt-4">
                <InputLabel for="sosmed" value="Sosial Media" />
                <TextInput id="sosmed" type="text" class="mt-1 block w-full" v-model="form.sosmed" />
                <InputError class="mt-2" :message="form.errors.sosmed" />
            </div>
            <div class="mt-4">
                <InputLabel for="tautan_marketplace" value="Tautan Marketplace" />
                <TextInput id="tautan_marketplace" type="text" class="mt-1 block w-full" v-model="form.tautan_marketplace" />
                <InputError class="mt-2" :message="form.errors.tautan_marketplace" />
            </div>
            <div class="mt-4">
                <InputLabel for="informasi_kemitraan" value="Informasi Kemitraan" />
                <TextInput id="informasi_kemitraan" type="text" class="mt-1 block w-full" v-model="form.informasi_kemitraan" />
                <InputError class="mt-2" :message="form.errors.informasi_kemitraan" />
            </div>
            <div class="mt-4">
                <InputLabel for="pelatihan_usaha" value="Pelatihan Usaha" />
                <TextInput id="pelatihan_usaha" type="text" class="mt-1 block w-full" v-model="form.pelatihan_usaha" />
                <InputError class="mt-2" :message="form.errors.pelatihan_usaha" />
            </div>
            <div class="mt-4">
                <InputLabel for="bank_tujuan" value="Bank Tujuan" />
                <TextInput id="bank_tujuan" type="text" class="mt-1 block w-full" v-model="form.bank_tujuan" />
                <InputError class="mt-2" :message="form.errors.bank_tujuan" />
            </div>
            <div class="mt-4">
                <InputLabel for="nama_rekening" value="Nama Rekening" />
                <TextInput id="nama_rekening" type="text" class="mt-1 block w-full" v-model="form.nama_rekening" />
                <InputError class="mt-2" :message="form.errors.nama_rekening" />
            </div>
            <div class="mt-4">
                <InputLabel for="norek" value="No. Rekening" />
                <TextInput id="norek" type="text" class="mt-1 block w-full" v-model="form.norek" />
                <InputError class="mt-2" :message="form.errors.norek" />
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
