<?php

namespace App\Http\Controllers\Api\Company\Select;

use App\Http\Controllers\Controller;
use App\Models\ExpenseType;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ExpenseTypeController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $expenseTypes = ExpenseType::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'data' => $expenseTypes,
                'message' => 'Tipos de despesa carregados com sucesso.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao carregar tipos de despesa.',
                'data' => [],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
