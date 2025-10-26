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
                $response = Http::post($this->baseUrl . '/auth', [
                    'username' => 'joaquin.alamiro@ejemplo.com',
                    'password' => '12345'
                ]);

                return $response->json('token');
            });
        } catch (ConnectionException $e) {
            throw new \Exception('Error de conexión con API Amplifica: ' . $e->getMessage());
        } catch (RequestException $e) {
            throw new \Exception('Error en solicitud a API Amplifica: ' . $e->getMessage());
        }
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
            $token = $this->getToken();
            
            $response = Http::withToken($token)
                ->get($this->baseUrl . '/regionalConfig');

            return $response->json();
        } catch (ConnectionException $e) {
            throw new \Exception('Error de conexión con API Amplifica: ' . $e->getMessage());
        } catch (RequestException $e) {
            throw new \Exception('Error en solicitud a API Amplifica: ' . $e->getMessage());
        }
    }

    public function getRate(array $products, string $comuna): array
    {
        try {
            $token = $this->getToken();

            $response = Http::withToken($token)
                ->post($this->baseUrl . '/getRate', [
                    'comuna' => $comuna,
                    'products' => $products
                ]);

            return $response->json();
        } catch (ConnectionException $e) {
            throw new \Exception('Error de conexión con API Amplifica: ' . $e->getMessage());
        } catch (RequestException $e) {
            throw new \Exception('Error en solicitud a API Amplifica: ' . $e->getMessage());
        }
    }
}