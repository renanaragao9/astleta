<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Sortable
{
    protected function applySorting($query, ?string $sort, string $direction = 'asc'): Builder
    {
        if ($sort) {
            $query->orderBy($sort, $direction);

            return $query;
        }

        $query->orderBy('created_at', 'desc');

        return $query;
    }
}
