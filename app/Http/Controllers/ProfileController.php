<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $locationMap = $validated['location_map'] ?? null;
        if ($locationMap) {
            // Resolve short links if present
            if (str_contains($locationMap, 'maps.app.goo.gl') || str_contains($locationMap, 'goo.gl')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $locationMap);
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($ch);
                curl_close($ch);

                if (preg_match('/^Location:\s*(.*)$/mi', $response, $matches)) {
                    $locationMap = trim($matches[1]);
                }
            }

            $lat = null;
            $lng = null;
            if (preg_match('/(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/', $locationMap, $match)) {
                $lat = (float) $match[1];
                $lng = (float) $match[2];
            }

            if ($lat !== null && $lng !== null) {
                $validated['latitude'] = $lat;
                $validated['longitude'] = $lng;
            }
        }

        if ($request->hasFile('sertifikasi_file')) {
            if ($request->user()->sertifikasi_file) {
                Storage::disk('public')->delete($request->user()->sertifikasi_file);
            }

            $validated['sertifikasi_file'] = $request->file('sertifikasi_file')->store('sertifikasi', 'public');
            $validated['sertifikasi_status'] = 'pending';
        }

        if ($request->hasFile('qris_image')) {
            if ($request->user()->qris_image) {
                Storage::disk('public')->delete($request->user()->qris_image);
            }

            $validated['qris_image'] = $request->file('qris_image')->store('qris', 'public');
        }

        $request->user()->fill($validated);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
