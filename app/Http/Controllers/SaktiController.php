<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SaktiController extends Controller
{
    private $clientId = 'c08a3cf8-4090-4bd7-b757-30394206feb7';
    private $clientSecret = '8wuKlELbkEXFd8uzBc6rN48R5xZofMUTWP9BvE5M';
    private $authorizeURL = 'https://sakti.karanganyarkab.go.id/login/oauth/authorize';
    private $tokenURL = 'https://sakti.karanganyarkab.go.id/login/oauth/access_token';
    private $apiURLBase = 'https://sakti.karanganyarkab.go.id/api/';

    public function redirectToSakti(Request $request)
    {
        $state = bin2hex(random_bytes(16));
        Session::put('sakti_state', $state);
        $params = [
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => route('sakti.callback'),
            'scopes' => 'user',
            'state' => $state,
        ];
        $url = $this->authorizeURL . '?' . http_build_query($params);
        return redirect($url);
    }

    public function handleCallback(Request $request)
    {
        $state = $request->input('state');
        $code = $request->input('code');
        if (!$state || $state !== Session::get('sakti_state')) {
            return redirect()->route('login')->withErrors(['sakti' => 'Invalid state']);
        }
        $params = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => route('sakti.callback'),
            'code' => $code,
        ];
        $token = $this->apiRequest($this->tokenURL, $params);
        if (!isset($token['access_token'])) {
            return redirect()->route('login')->withErrors(['sakti' => 'Failed to get access token']);
        }
        Session::put('sakti_access_token', $token['access_token']);
        // Example: get user info
        $user = $this->apiRequest($this->apiURLBase . 'user', [], $token['access_token']);
        // TODO: Integrate user info with your app's user system
        // For now, just show user info
        return response()->json($user);
    }

    private function apiRequest($url, $post = [], $accessToken = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        if ($post) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        $headers = [
            'Accept: application/json',
            'User-Agent: https://example-app.com/'
        ];
        if ($accessToken) {
            $headers[] = 'Authorization: Bearer ' . $accessToken;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}
