<?php

namespace App\Services\Api\Public\Marketing;

use App\Models\Marketing;
use Illuminate\Database\Eloquent\Collection;

class IndexPublicMarketingService
{
    public function run(array $data): Collection
    {
        $age = $data['age'] ?? null;

        $marketings = Marketing::with([
            'marketingType',
        ])
            ->when(isset($data['age']) && $age !== null, function ($query) use ($age) {
                $query->where('age', '<=', $age);
            })
            ->whereDate('start_date', '<=', now()->toDateString())
            ->whereDate('end_date', '>=', now()->toDateString())
            ->orderBy(
                $data['sort'] ?? 'created_at',
                $data['direction'] ?? 'desc'
            )
            ->get();

        return $marketings;
    }
}
