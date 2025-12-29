<?php

namespace App\Http\Controllers\Api\Athlete;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Athlete\Tab\IndexTabAthleteRequest;
use App\Http\Resources\Athlete\TabResource;
use App\Models\Tab;
use App\Services\Api\Athlete\Tab\IndexTabAthleteService;
use App\Services\Api\Athlete\Tab\ViewTabAthleteService;
use App\Services\Pdf\TabPdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TabAthleteController extends BaseController
{
    public function index(
        IndexTabAthleteRequest $indexTabAthleteRequest,
        IndexTabAthleteService $indexTabAthleteService
    ): AnonymousResourceCollection {
        $data = $indexTabAthleteRequest->validated();
        $tabs = $indexTabAthleteService->run($data);

        return TabResource::collection($tabs);
    }

    public function show(
        Tab $tab,
        ViewTabAthleteService $viewTabAthleteService
    ): JsonResponse {
        $tab = $viewTabAthleteService->run($tab);

        return $this->successResponse(
            new TabResource($tab),
            'Comanda encontrada com sucesso.'
        );

    }

    public function downloadReceipt(
        Tab $tab,
        ViewTabAthleteService $viewTabAthleteService,
        TabPdfService $tabPdfService
    ) {
        $tab = $viewTabAthleteService->run($tab);
        $pdfContent = $tabPdfService->generateTabReceiptPdfContent($tab);
        $filename = strtoupper("comprovante_comanda_{$tab->code}.pdf");

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }
}
