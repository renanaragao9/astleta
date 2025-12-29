<?php

namespace App\Http\Controllers\Api\Company\Select;

use App\Http\Controllers\Controller;
use App\Models\FieldType;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FieldTypeController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $fieldTypes = FieldType::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'data' => $fieldTypes,
                'message' => 'Tipos de campo carregados com sucesso.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao carregar tipos de campo.',
                'data' => [],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
