<?php

namespace App\Http\Controllers\Api\Company\Select;

use App\Http\Controllers\Controller;
use App\Models\FieldSize;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FieldSizeController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $fieldSizes = FieldSize::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'data' => $fieldSizes,
                'message' => 'Tamanhos de campo carregados com sucesso.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao carregar tamanhos de campo.',
                'data' => [],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
