<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Company\Expense\IndexExpenseRequest;
use App\Http\Requests\Api\Company\Expense\StoreExpenseRequest;
use App\Http\Requests\Api\Company\Expense\UpdateExpenseRequest;
use App\Http\Resources\Company\ExpenseResource;
use App\Models\Expense;
use App\Services\Api\Company\Expense\DeleteExpenseService;
use App\Services\Api\Company\Expense\IndexExpenseService;
use App\Services\Api\Company\Expense\StoreExpenseService;
use App\Services\Api\Company\Expense\UpdateExpenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExpenseController extends BaseController
{
    public function index(
        IndexExpenseRequest $indexExpenseRequest,
        IndexExpenseService $indexExpenseService,
    ): AnonymousResourceCollection {
        $data = $indexExpenseRequest->validated();
        $expenses = $indexExpenseService->run($data);

        return ExpenseResource::collection($expenses);
    }

    public function show(Expense $expense): JsonResponse
    {
        return $this->successResponse(
            new ExpenseResource($expense),
            'Despesa encontrada com sucesso.'
        );
    }

    public function store(
        StoreExpenseRequest $storeExpenseRequest,
        StoreExpenseService $storeExpenseService
    ): JsonResponse {
        $data = $storeExpenseRequest->validated();
        $expense = $storeExpenseService->run($data);

        return $this->successResponse(
            new ExpenseResource($expense),
            'Despesa criada com sucesso.'
        );
    }

    public function update(
        UpdateExpenseRequest $updateExpenseRequest,
        UpdateExpenseService $updateExpenseService,
        Expense $expense
    ): JsonResponse {
        $data = $updateExpenseRequest->validated();
        $expense = $updateExpenseService->run($expense, $data);

        return $this->successResponse(
            new ExpenseResource($expense),
            'Despesa atualizada com sucesso.'
        );
    }

    public function destroy(Expense $expense, DeleteExpenseService $deleteExpenseService): JsonResponse
    {
        $deleteExpenseService->run($expense);

        return $this->successResponse(
            null,
            'Despesa removida com sucesso.'
        );
    }
}
