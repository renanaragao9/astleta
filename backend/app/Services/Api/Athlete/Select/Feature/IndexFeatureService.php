<?php

namespace App\Services\Api\Athlete\Select\Feature;

use App\Models\Feature;
use App\Services\Api\Athlete\Select\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class IndexFeatureService extends BaseService
{
    public function run(Request $request): Collection
    {
        $query = Feature::query()
            ->select('id', 'name', 'position_id')
            ->orderBy('name');

        if ($request->has('position_id') && $request->position_id) {
            $query->where('position_id', $request->position_id);
        }

        return $query->get();
    }
}
