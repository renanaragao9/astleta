<?php

namespace App\Services\Api\Athlete\Select\Sport;

use App\Models\Sport;
use App\Services\Api\Athlete\Select\BaseService;
use Illuminate\Database\Eloquent\Collection;

class IndexSportService extends BaseService
{
    public function run(): Collection
    {
        return Sport::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }
}
