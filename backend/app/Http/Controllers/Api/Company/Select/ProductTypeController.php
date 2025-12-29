<?php

namespace App\Http\Controllers\Api\Company\Select;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProductTypeController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $productTypes = ProductType::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'data' => $productTypes,
                'message' => 'Tipos de produto carregados com sucesso.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao carregar tipos de produto.',
                'data' => [],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
