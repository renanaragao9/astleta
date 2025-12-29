<?php

namespace App\Http\Controllers\Api\Company\Select;

use App\Http\Controllers\Controller;
use App\Models\PaymentForm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentFormController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = PaymentForm::query()
                ->select('id', 'name', 'type')
                ->orderBy('name');

            if ($request->has('type') && $request->type) {
                $query->where('type', $request->type);
            }

            $paymentForms = $query->get();

            return response()->json([
                'data' => $paymentForms,
                'message' => 'Formas de pagamento carregadas com sucesso.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao carregar formas de pagamento.',
                'data' => [],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
