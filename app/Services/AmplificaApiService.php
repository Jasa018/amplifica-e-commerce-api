<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class AmplificaApiService
{
    private string $baseUrl = 'https://postulaciones.amplifica.io';

    public function getToken(): string
    {
        try {
            return Cache::remember('amplifica_token', 55, function () {
                return $this->requestNewToken();
            });
        } catch (ConnectionException $e) {
            throw new \Exception('Error de conexión con API Amplifica: ' . $e->getMessage());
        } catch (RequestException $e) {
            throw new \Exception('Error en solicitud a API Amplifica: ' . $e->getMessage());
        }
    }

    private function requestNewToken(): string
    {
        $response = Http::post($this->baseUrl . '/auth', [
            'username' => 'joaquin.alamiro@ejemplo.com',
            'password' => '12345'
        ]);

        if (!$response->successful()) {
            throw new \Exception('Error al obtener token: ' . $response->body());
        }

        return $response->json('token');
    }

    private function makeAuthenticatedRequest(string $method, string $endpoint, array $data = []): array
    {
        $token = $this->getToken();
        
        $response = Http::withToken($token)->{$method}($this->baseUrl . $endpoint, $data);
        
        // Si el token expiró (401), renovar y reintentar
        if ($response->status() === 401) {
            Cache::forget('amplifica_token');
            $token = $this->getToken();
            $response = Http::withToken($token)->{$method}($this->baseUrl . $endpoint, $data);
        }
        
        if (!$response->successful()) {
            throw new \Exception('Error en API: ' . $response->body());
        }
        
        return $response->json();
    }

    /**
     * Obtain a token using provided credentials (no caching).
     *
     * @param string $email
     * @param string $password
     * @return string
     * @throws \Exception
     */
    public function getTokenWithCredentials(string $email, string $password): string
    {
        try {
            $response = Http::post($this->baseUrl . '/auth', [
                'username' => $email,
                'password' => $password,
            ]);

            if ($response->successful()) {
                return $response->json('token');
            }

            // If API returned an error status, throw with body message when available
            $body = $response->json();
            $message = is_array($body) && isset($body['message']) ? $body['message'] : $response->body();
            throw new \Exception('Amplifica auth error: ' . $message);
        } catch (ConnectionException $e) {
            throw new \Exception('Error de conexión con API Amplifica: ' . $e->getMessage());
        } catch (RequestException $e) {
            throw new \Exception('Error en solicitud a API Amplifica: ' . $e->getMessage());
        }
    }

    public function getRegionalConfig(): array
    {
        try {
            return $this->makeAuthenticatedRequest('get', '/regionalConfig');
        } catch (ConnectionException $e) {
            throw new \Exception('Error de conexión con API Amplifica: ' . $e->getMessage());
        } catch (RequestException $e) {
            throw new \Exception('Error en solicitud a API Amplifica: ' . $e->getMessage());
        }
    }

    public function getRate(array $products, string $comuna): array
    {
        try {
            return $this->makeAuthenticatedRequest('post', '/getRate', [
                'comuna' => $comuna,
                'products' => $products
            ]);
        } catch (ConnectionException $e) {
            throw new \Exception('Error de conexión con API Amplifica: ' . $e->getMessage());
        } catch (RequestException $e) {
            throw new \Exception('Error en solicitud a API Amplifica: ' . $e->getMessage());
        }
    }
}