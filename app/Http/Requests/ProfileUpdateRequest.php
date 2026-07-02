<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'nik_penjual' => ['nullable', 'digits:16'],
            'sertifikasi_jenis' => ['nullable', 'in:halal,haki'],
            'sertifikasi_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
            'sertifikasi_status' => ['nullable', 'in:pending,approved,rejected'],
            'sosmed_instagram' => ['nullable', 'url', 'max:255'],
            'sosmed_tiktok' => ['nullable', 'url', 'max:255'],
            'qris_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'location_map' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
