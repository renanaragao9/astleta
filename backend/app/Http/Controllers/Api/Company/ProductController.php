<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Company\Product\IndexProductRequest;
use App\Http\Requests\Api\Company\Product\StoreProductRequest;
use App\Http\Requests\Api\Company\Product\UpdateProductRequest;
use App\Http\Resources\Company\ProductResource;
use App\Models\Product;
use App\Services\Api\Company\Product\DeleteProductService;
use App\Services\Api\Company\Product\IndexProductService;
use App\Services\Api\Company\Product\SelectProductService;
use App\Services\Api\Company\Product\StoreProductService;
use App\Services\Api\Company\Product\UpdateProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends BaseController
{
    public function index(
        IndexProductRequest $indexProductRequest,
        IndexProductService $indexProductService,
    ): AnonymousResourceCollection {
        $data = $indexProductRequest->validated();
        $products = $indexProductService->run($data);

        return ProductResource::collection($products);
    }

    public function show(Product $product): JsonResponse
    {
        return $this->successResponse(
            new ProductResource($product),
            'Produto encontrado com sucesso.'
        );
    }

    public function store(
        StoreProductRequest $storeProductRequest,
        StoreProductService $storeProductService
    ): JsonResponse {
        $data = $storeProductRequest->validated();
        $product = $storeProductService->run($data);

        return $this->successResponse(
            new ProductResource($product),
            'Produto criado com sucesso.'
        );
    }

    public function update(
        UpdateProductRequest $updateProductRequest,
        UpdateProductService $UpdateProductService,
        Product $product
    ): JsonResponse {
        $data = $updateProductRequest->validated();
        $product = $UpdateProductService->run($product, $data);

        return $this->successResponse(
            new ProductResource($product),
            'Produto atualizado com sucesso.'
        );
    }

    public function destroy(Product $product, DeleteProductService $deleteService): JsonResponse
    {
        $deleteService->run($product);

        return $this->successResponse(
            null,
            'Produto removido com sucesso.'
        );
    }

    public function selectProducts(SelectProductService $selectProductService): JsonResponse
    {
        $products = $selectProductService->run();

        return $this->successResponse(
            $products,
            'Produtos carregados com sucesso.'
        );
    }
}
