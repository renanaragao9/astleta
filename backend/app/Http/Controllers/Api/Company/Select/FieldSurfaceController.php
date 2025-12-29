<?php

namespace App\Http\Controllers\Api\Company\Select;

use App\Http\Controllers\Controller;
use App\Models\FieldSurface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FieldSurfaceController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $fieldSurfaces = FieldSurface::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'data' => $fieldSurfaces,
                'message' => 'Superfícies de campo carregadas com sucesso.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao carregar superfícies de campo.',
                'data' => [],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
