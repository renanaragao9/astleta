<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Public\Marketing\IndexPublicMarketingRequest;
use App\Http\Resources\Public\PublicMarketingResource;
use App\Services\Api\Public\Marketing\IndexPublicMarketingService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PublicMarketingController extends BaseController
{
    public function index(
        IndexPublicMarketingRequest $indexPublicMarketingRequest,
        IndexPublicMarketingService $indexPublicMarketingService,
    ): AnonymousResourceCollection {
        $data = $indexPublicMarketingRequest->validated();
        $marketings = $indexPublicMarketingService->run($data);

        return PublicMarketingResource::collection($marketings);
    }
}
