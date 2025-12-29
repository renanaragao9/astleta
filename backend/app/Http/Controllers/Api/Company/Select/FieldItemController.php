<?php

namespace App\Http\Controllers\Api\Company\Select;

use App\Http\Controllers\Controller;
use App\Models\FieldItem;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FieldItemController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $fieldItems = FieldItem::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'data' => $fieldItems,
                'message' => 'Itens de campo carregados com sucesso.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao carregar itens de campo.',
                'data' => [],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
