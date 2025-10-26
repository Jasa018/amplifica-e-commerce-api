<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

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
        try {
            $response = Http::timeout(30)->post($this->baseUrl . '/auth', [
                'username' => 'joaquin.alamiro@ejemplo.com',
                'password' => '12345'
            ]);

            if (!$response->successful()) {
                $this->logError('Token request failed', $response);
                $this->handleApiError($response);
            }

            $token = $response->json('token');
            if (!$token) {
                throw new \Exception('Token no recibido en la respuesta de autenticación');
            }

            return $token;
        } catch (ConnectionException $e) {
            Log::error('Connection error getting token', ['error' => $e->getMessage()]);
            throw new \Exception('Error de conexión al obtener token: Verifique su conexión a internet');
        }
    }

    private function makeAuthenticatedRequest(string $method, string $endpoint, array $data = []): array
    {
        $maxRetries = 2;
        $attempt = 0;
        
        while ($attempt < $maxRetries) {
            try {
                $token = $this->getToken();
                $response = Http::timeout(30)->withToken($token)->{$method}($this->baseUrl . $endpoint, $data);
                
                // Si el token expiró (401), renovar y reintentar
                if ($response->status() === 401 && $attempt === 0) {
                    Log::info('Token expired, refreshing...');
                    Cache::forget('amplifica_token');
                    $attempt++;
                    continue;
                }
                
                if (!$response->successful()) {
                    $this->logError("API request failed: {$method} {$endpoint}", $response);
                    $this->handleApiError($response);
                }
                
                return $response->json() ?? [];
                
            } catch (ConnectionException $e) {
                Log::error('Connection error in API request', [
                    'method' => $method,
                    'endpoint' => $endpoint,
                    'error' => $e->getMessage()
                ]);
                throw new \Exception('Error de conexión con la API: Verifique su conexión a internet');
            }
        }
        
        throw new \Exception('Error de autenticación: No se pudo renovar el token');
    }
    
    private function handleApiError(Response $response): void
    {
        $status = $response->status();
        $body = $response->json();
        
        switch ($status) {
            case 400:
                throw new \Exception('Solicitud inválida: ' . ($body['message'] ?? 'Datos incorretos'));
            case 401:
                throw new \Exception('Error de autenticación: Credenciales inválidas');
            case 403:
                throw new \Exception('Acceso denegado: Sin permisos para esta operación');
            case 404:
                throw new \Exception('Recurso no encontrado');
            case 422:
                throw new \Exception('Datos de validación incorrectos: ' . ($body['message'] ?? 'Verifique los datos enviados'));
            case 429:
                throw new \Exception('Demasiadas solicitudes: Intente nuevamente en unos minutos');
            case 500:
                throw new \Exception('Error interno del servidor: Intente nuevamente más tarde');
            case 503:
                throw new \Exception('Servicio no disponible: La API está temporalmente fuera de servicio');
            default:
                throw new \Exception('Error en API (' . $status . '): ' . ($body['message'] ?? $response->body()));
        }
    }
    
    private function logError(string $message, Response $response): void
    {
        Log::error($message, [
            'status' => $response->status(),
            'body' => $response->body(),
            'headers' => $response->headers()
        ]);
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

    public function cotizar(array $data): array
    {
        try {
            return $this->makeAuthenticatedRequest('post', '/cotizar', $data);
        } catch (ConnectionException $e) {
            throw new \Exception('Error de conexión con API Amplifica: ' . $e->getMessage());
        } catch (RequestException $e) {
            throw new \Exception('Error en solicitud a API Amplifica: ' . $e->getMessage());
        }
    }
}