<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';


const form = useForm({
    nama_toko: '',
    nik_penjual: '',
    nama_lengkap_penjual: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    alamat_penjual: '',
    provinsi: '',
    kabupaten: '',
    kecamatan: '',
    kelurahan: '',
    kategori_usaha: '',
    modal_usaha: '',
    omset_tahun: '',
    sertifikasi_halal: '',
    sertifikasi_haki: '',
    sosmed: '',
    tautan_marketplace: '',
    informasi_kemitraan: '',
    pelatihan_usaha: '',
    bank_tujuan: '',
    nama_rekening: '',
    norek: '',
});

const submit = () => {
    form.post(route('register.toko'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Registrasi Toko" />
                <div class="flex justify-center items-center min-h-[80vh]">
                    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">
                        <div class="flex flex-col items-center mb-6">
                            <div class="flex items-center space-x-2">
                                <img src="/images/Logomark.png" alt="Logo" class="w-10 h-10" />
                                <span class="text-2xl font-bold text-gray-800">Semarak</span>
                            </div>
                            <h2 class="mt-4 text-xl font-semibold">Registrasi Toko (Penjual)</h2>
                            <p class="text-gray-500 text-sm text-center max-w-md">
                                Daftarkan toko Anda untuk mulai berjualan di marketplace UMKM Karanganyar.
                            </p>
                        </div>
                        <!-- Tombol SSO Sakti -->
                        <div class="mb-6 flex flex-col items-center">
                            <a :href="route('sakti.login')"
                                 class="w-full flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2 px-4 rounded-lg shadow transition mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m4 4h-1v-4h-1m-4 4h-1v-4h-1" /></svg>
                                    Daftar / Login dengan SSO Sakti
                            </a>
                            <span class="text-xs text-gray-400">atau daftar manual:</span>
                        </div>
                    <form @submit.prevent="submit">
            <!-- Informasi Umum -->
            <div>
                <InputLabel for="nama_toko" value="Nama Toko" />
                <TextInput id="nama_toko" type="text" class="mt-1 block w-full" v-model="form.nama_toko" required autofocus />
                <InputError class="mt-2" :message="form.errors.nama_toko" />
            </div>
            <div class="mt-4">
                <InputLabel for="nik_penjual" value="NIK Penjual" />
                <TextInput id="nik_penjual" type="text" class="mt-1 block w-full" v-model="form.nik_penjual" required />
                <InputError class="mt-2" :message="form.errors.nik_penjual" />
            </div>
            <div class="mt-4">
                <InputLabel for="nama_lengkap_penjual" value="Nama Lengkap Penjual" />
                <TextInput id="nama_lengkap_penjual" type="text" class="mt-1 block w-full" v-model="form.nama_lengkap_penjual" required />
                <InputError class="mt-2" :message="form.errors.nama_lengkap_penjual" />
            </div>
            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div class="mt-4">
                <InputLabel for="phone" value="No HP" />
                <TextInput id="phone" type="text" class="mt-1 block w-full" v-model="form.phone" required />
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>
            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>
            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Konfirmasi Password" />
                <TextInput id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <!-- Alamat Usaha -->
            <div class="mt-8 font-semibold text-gray-700">Alamat Usaha</div>
            <div class="mt-2">
                <InputLabel for="alamat_penjual" value="Detail Alamat" />
                <TextInput id="alamat_penjual" type="text" class="mt-1 block w-full" v-model="form.alamat_penjual" required />
                <InputError class="mt-2" :message="form.errors.alamat_penjual" />
            </div>
            <div class="mt-4 grid grid-cols-2 gap-4">
                <div>
                    <InputLabel for="provinsi" value="Provinsi" />
                    <TextInput id="provinsi" type="text" class="mt-1 block w-full" v-model="form.provinsi" required />
                    <InputError class="mt-2" :message="form.errors.provinsi" />
                </div>
                <div>
                    <InputLabel for="kabupaten" value="Kabupaten" />
                    <TextInput id="kabupaten" type="text" class="mt-1 block w-full" v-model="form.kabupaten" required />
                    <InputError class="mt-2" :message="form.errors.kabupaten" />
                </div>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-4">
                <div>
                    <InputLabel for="kecamatan" value="Kecamatan" />
                    <TextInput id="kecamatan" type="text" class="mt-1 block w-full" v-model="form.kecamatan" required />
                    <InputError class="mt-2" :message="form.errors.kecamatan" />
                </div>
                <div>
                    <InputLabel for="kelurahan" value="Kelurahan" />
                    <TextInput id="kelurahan" type="text" class="mt-1 block w-full" v-model="form.kelurahan" required />
                    <InputError class="mt-2" :message="form.errors.kelurahan" />
                </div>
            </div>

            <!-- Detail Usaha -->
            <div class="mt-8 font-semibold text-gray-700">Detail Usaha</div>
            <div class="mt-2">
                <InputLabel for="kategori_usaha" value="Kategori Usaha" />
                <TextInput id="kategori_usaha" type="text" class="mt-1 block w-full" v-model="form.kategori_usaha" required />
                <InputError class="mt-2" :message="form.errors.kategori_usaha" />
            </div>
            <div class="mt-4">
                <InputLabel for="modal_usaha" value="Modal Usaha" />
                <TextInput id="modal_usaha" type="text" class="mt-1 block w-full" v-model="form.modal_usaha" required />
                <InputError class="mt-2" :message="form.errors.modal_usaha" />
            </div>
            <div class="mt-4">
                <InputLabel for="omset_tahun" value="Omset Usaha/Tahun" />
                <TextInput id="omset_tahun" type="text" class="mt-1 block w-full" v-model="form.omset_tahun" required />
                <InputError class="mt-2" :message="form.errors.omset_tahun" />
            </div>

            <!-- Upload & Lainnya -->
            <div class="mt-8 font-semibold text-gray-700">Upload & Lainnya</div>
            <div class="mt-2">
                <InputLabel for="sertifikasi_halal" value="Sertifikasi Halal (opsional)" />
                <TextInput id="sertifikasi_halal" type="text" class="mt-1 block w-full" v-model="form.sertifikasi_halal" />
                <InputError class="mt-2" :message="form.errors.sertifikasi_halal" />
            </div>
            <div class="mt-4">
                <InputLabel for="sertifikasi_haki" value="Sertifikasi HAKI (opsional)" />
                <TextInput id="sertifikasi_haki" type="text" class="mt-1 block w-full" v-model="form.sertifikasi_haki" />
                <InputError class="mt-2" :message="form.errors.sertifikasi_haki" />
            </div>
            <div class="mt-4">
                <InputLabel for="sosmed" value="Sosial Media (opsional)" />
                <TextInput id="sosmed" type="text" class="mt-1 block w-full" v-model="form.sosmed" />
                <InputError class="mt-2" :message="form.errors.sosmed" />
            </div>
            <div class="mt-4">
                <InputLabel for="tautan_marketplace" value="Tautan Marketplace (opsional)" />
                <TextInput id="tautan_marketplace" type="text" class="mt-1 block w-full" v-model="form.tautan_marketplace" />
                <InputError class="mt-2" :message="form.errors.tautan_marketplace" />
            </div>
            <div class="mt-4">
                <InputLabel for="informasi_kemitraan" value="Informasi Kemitraan (opsional)" />
                <TextInput id="informasi_kemitraan" type="text" class="mt-1 block w-full" v-model="form.informasi_kemitraan" />
                <InputError class="mt-2" :message="form.errors.informasi_kemitraan" />
            </div>
            <div class="mt-4">
                <InputLabel for="pelatihan_usaha" value="Pelatihan Usaha (opsional)" />
                <TextInput id="pelatihan_usaha" type="text" class="mt-1 block w-full" v-model="form.pelatihan_usaha" />
                <InputError class="mt-2" :message="form.errors.pelatihan_usaha" />
            </div>

            <!-- Informasi Rekening -->
            <div class="mt-8 font-semibold text-gray-700">Informasi Rekening</div>
            <div class="mt-2">
                <InputLabel for="bank_tujuan" value="Bank Tujuan" />
                <TextInput id="bank_tujuan" type="text" class="mt-1 block w-full" v-model="form.bank_tujuan" required />
                <InputError class="mt-2" :message="form.errors.bank_tujuan" />
            </div>
            <div class="mt-4">
                <InputLabel for="nama_rekening" value="Nama Rekening" />
                <TextInput id="nama_rekening" type="text" class="mt-1 block w-full" v-model="form.nama_rekening" required />
                <InputError class="mt-2" :message="form.errors.nama_rekening" />
            </div>
            <div class="mt-4">
                <InputLabel for="norek" value="No. Rekening" />
                <TextInput id="norek" type="text" class="mt-1 block w-full" v-model="form.norek" required />
                <InputError class="mt-2" :message="form.errors.norek" />
            </div>

            <div class="mt-6">
                <PrimaryButton class="w-full justify-center bg-yellow-500 hover:bg-yellow-600" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Daftar Toko
                </PrimaryButton>
            </div>
                </form>
            </div>
        </div>
        <p class="text-center text-sm text-gray-600 mt-6">
            Sudah punya akun?
            <Link :href="route('login')" class="font-semibold text-blue-600 hover:underline">
                Log in
            </Link>
        </p>
    </GuestLayout>
</template>
