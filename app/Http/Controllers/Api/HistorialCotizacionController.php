<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use App\Http\Resources\CotizacionResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HistorialCotizacionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/historial-cotizaciones",
     *     summary="Obtener historial de cotizaciones del usuario",
     *     tags={"Cotizaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Número de registros a obtener",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Historial de cotizaciones",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="comuna_destino", type="string"),
     *                 @OA\Property(property="peso_total", type="number"),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            
            $historial = Cotizacion::where('user_id', Auth::id())
                                  ->orderBy('created_at', 'desc')
                                  ->limit($limit)
                                  ->get();

            return CotizacionResource::collection($historial);
        } catch (\Exception $e) {
            Log::error('Error getting cotizaciones history', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener historial',
                'message' => 'No se pudo cargar el historial de cotizaciones'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/historial-cotizaciones/{id}",
     *     summary="Obtener detalle de cotización",
     *     tags={"Cotizaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Detalle de cotización"),
     *     @OA\Response(response=404, description="Cotización no encontrada")
     * )
     */
    public function show($id)
    {
        try {
            $cotizacion = Cotizacion::where('user_id', Auth::id())
                                   ->findOrFail($id);

            return new CotizacionResource($cotizacion);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Cotizacion not found', ['id' => $id, 'user_id' => Auth::id()]);
            return response()->json([
                'success' => false,
                'error' => 'Cotización no encontrada',
                'message' => 'La cotización solicitada no existe o no pertenece al usuario'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error getting cotizacion detail', [
                'error' => $e->getMessage(), 
                'id' => $id,
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Error interno del servidor',
                'message' => 'No se pudo obtener el detalle de la cotización'
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/historial-cotizaciones/{id}",
     *     summary="Eliminar cotización del historial",
     *     tags={"Cotizaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Cotización eliminada"),
     *     @OA\Response(response=404, description="Cotización no encontrada")
     * )
     */
    public function destroy($id)
    {
        try {
            $cotizacion = Cotizacion::where('user_id', Auth::id())
                                   ->findOrFail($id);
            
            $cotizacion->delete();
            
            return response()->json(['message' => 'Cotización eliminada del historial']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Cotizacion not found for deletion', ['id' => $id, 'user_id' => Auth::id()]);
            return response()->json([
                'success' => false,
                'error' => 'Cotización no encontrada',
                'message' => 'La cotización a eliminar no existe o no pertenece al usuario'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting cotizacion', [
                'error' => $e->getMessage(), 
                'id' => $id,
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Error al eliminar cotización',
                'message' => 'No se pudo eliminar la cotización del historial'
            ], 500);
        }
    }
}