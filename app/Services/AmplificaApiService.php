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
        $startTime = microtime(true);
        $requestData = ['username' => 'joaquin.alamiro@ejemplo.com', 'password' => '[HIDDEN]'];
        
        Log::info('API Request - Token Authentication', [
            'endpoint' => '/auth',
            'method' => 'POST',
            'request_data' => $requestData
        ]);
        
        try {
            $response = Http::timeout(30)->post($this->baseUrl . '/auth', [
                'username' => 'joaquin.alamiro@ejemplo.com',
                'password' => '12345'
            ]);
            
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            
            Log::info('API Response - Token Authentication', [
                'endpoint' => '/auth',
                'method' => 'POST',
                'status_code' => $response->status(),
                'duration_ms' => $duration,
                'success' => $response->successful()
            ]);

            if (!$response->successful()) {
                $this->logError('Token request failed', $response);
                $this->handleApiError($response);
            }

            $token = $response->json('token');
            if (!$token) {
                Log::error('API Error - Token not received', [
                    'endpoint' => '/auth',
                    'response_body' => $response->body()
                ]);
                throw new \Exception('Token no recibido en la respuesta de autenticación');
            }
            
            Log::info('API Success - Token obtained successfully', [
                'endpoint' => '/auth',
                'token_length' => strlen($token)
            ]);

            return $token;
        } catch (ConnectionException $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            Log::error('API Connection Error - Token Authentication', [
                'endpoint' => '/auth',
                'method' => 'POST',
                'duration_ms' => $duration,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Error de conexión al obtener token: Verifique su conexión a internet');
        }
    }

    private function makeAuthenticatedRequest(string $method, string $endpoint, array $data = []): array
    {
        $maxRetries = 2;
        $attempt = 0;
        $startTime = microtime(true);
        
        Log::info('API Request - Authenticated', [
            'endpoint' => $endpoint,
            'method' => strtoupper($method),
            'request_data' => $data,
            'attempt' => $attempt + 1,
            'max_retries' => $maxRetries
        ]);
        
        while ($attempt < $maxRetries) {
            try {
                $token = $this->getToken();
                $response = Http::timeout(30)->withToken($token)->{$method}($this->baseUrl . $endpoint, $data);
                
                $duration = round((microtime(true) - $startTime) * 1000, 2);
                
                Log::info('API Response - Authenticated', [
                    'endpoint' => $endpoint,
                    'method' => strtoupper($method),
                    'status_code' => $response->status(),
                    'duration_ms' => $duration,
                    'attempt' => $attempt + 1,
                    'success' => $response->successful()
                ]);
                
                // Si el token expiró (401), renovar y reintentar
                if ($response->status() === 401 && $attempt === 0) {
                    Log::warning('API Token Expired - Refreshing', [
                        'endpoint' => $endpoint,
                        'method' => strtoupper($method),
                        'attempt' => $attempt + 1
                    ]);
                    Cache::forget('amplifica_token');
                    $attempt++;
                    continue;
                }
                
                if (!$response->successful()) {
                    $this->logError("API request failed: {$method} {$endpoint}", $response);
                    $this->handleApiError($response);
                }
                
                $responseData = $response->json() ?? [];
                
                Log::info('API Success - Request completed', [
                    'endpoint' => $endpoint,
                    'method' => strtoupper($method),
                    'response_size' => strlen($response->body()),
                    'duration_ms' => $duration
                ]);
                
                return $responseData;
                
            } catch (ConnectionException $e) {
                $duration = round((microtime(true) - $startTime) * 1000, 2);
                Log::error('API Connection Error - Authenticated Request', [
                    'endpoint' => $endpoint,
                    'method' => strtoupper($method),
                    'duration_ms' => $duration,
                    'attempt' => $attempt + 1,
                    'error' => $e->getMessage()
                ]);
                throw new \Exception('Error de conexión con la API: Verifique su conexión a internet');
            }
        }
        
        Log::error('API Error - Max retries exceeded', [
            'endpoint' => $endpoint,
            'method' => strtoupper($method),
            'max_retries' => $maxRetries
        ]);
        
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
        Log::error('API Error - ' . $message, [
            'status_code' => $response->status(),
            'response_body' => $response->body(),
            'response_headers' => $response->headers(),
            'content_type' => $response->header('Content-Type'),
            'response_size' => strlen($response->body())
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
        $startTime = microtime(true);
        
        Log::info('API Request - Custom Credentials Auth', [
            'endpoint' => '/auth',
            'method' => 'POST',
            'username' => $email
        ]);
        
        try {
            $response = Http::post($this->baseUrl . '/auth', [
                'username' => $email,
                'password' => $password,
            ]);
            
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            
            Log::info('API Response - Custom Credentials Auth', [
                'endpoint' => '/auth',
                'method' => 'POST',
                'status_code' => $response->status(),
                'duration_ms' => $duration,
                'success' => $response->successful(),
                'username' => $email
            ]);

            if ($response->successful()) {
                $token = $response->json('token');
                Log::info('API Success - Custom token obtained', [
                    'endpoint' => '/auth',
                    'username' => $email,
                    'token_length' => strlen($token)
                ]);
                return $token;
            }

            // If API returned an error status, throw with body message when available
            $body = $response->json();
            $message = is_array($body) && isset($body['message']) ? $body['message'] : $response->body();
            
            Log::error('API Error - Custom Credentials Auth Failed', [
                'endpoint' => '/auth',
                'username' => $email,
                'status_code' => $response->status(),
                'error_message' => $message
            ]);
            
            throw new \Exception('Amplifica auth error: ' . $message);
        } catch (ConnectionException $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            Log::error('API Connection Error - Custom Credentials Auth', [
                'endpoint' => '/auth',
                'method' => 'POST',
                'duration_ms' => $duration,
                'username' => $email,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Error de conexión con API Amplifica: ' . $e->getMessage());
        } catch (RequestException $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            Log::error('API Request Error - Custom Credentials Auth', [
                'endpoint' => '/auth',
                'method' => 'POST',
                'duration_ms' => $duration,
                'username' => $email,
                'error' => $e->getMessage()
            ]);
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