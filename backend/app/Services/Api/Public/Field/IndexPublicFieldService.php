<?php

namespace App\Services\Api\Public\Field;

use App\Models\Field;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexPublicFieldService
{
    public function run(array $data): LengthAwarePaginator
    {
        $fields = Field::with([
            'fieldType',
            'fieldSurface',
            'fieldSize',
            'fieldItems',
            'fieldSchedules',
            'fieldImages',
            'company' => function ($query) {
                $query->select('id', 'name', 'phone', 'description', 'is_open', 'is_free', 'image_path');
            },
            'company.addresses' => function ($query) {
                $query->select('id', 'addressable_type', 'addressable_id', 'city', 'state', 'district', 'street', 'number', 'zipcode', 'latitude', 'longitude');
            },
        ])
            ->whereHas('company', function ($query) {
                $query->where('status', 'aprovado');
            })
            ->where('is_active', operator: true)
            ->when(isset($data['search']) && ! empty($data['search']), function ($query) use ($data) {
                $query->whereRaw('LOWER(name) LIKE LOWER(?)', ['%'.$data['search'].'%'])
                    ->orWhereHas('company', function ($q) use ($data) {
                        $q->whereRaw('LOWER(name) LIKE LOWER(?)', ['%'.$data['search'].'%']);
                    });
            })
            ->when(isset($data['city']) && ! empty($data['city']), function ($query) use ($data) {
                $query->whereHas('company.addresses', function ($q) use ($data) {
                    $q->whereRaw('LOWER(city) LIKE LOWER(?)', ['%'.$data['city'].'%']);
                });
            })
            ->when(isset($data['state']) && ! empty($data['state']), function ($query) use ($data) {
                $query->whereHas('company.addresses', function ($q) use ($data) {
                    $q->whereRaw('LOWER(state) LIKE LOWER(?)', ['%'.$data['state'].'%']);
                });
            })
            ->when(isset($data['district']) && ! empty($data['district']), function ($query) use ($data) {
                $query->whereHas('company.addresses', function ($q) use ($data) {
                    $q->whereRaw('LOWER(district) LIKE LOWER(?)', ['%'.$data['district'].'%']);
                });
            })
            ->when(isset($data['sport_type']) && ! empty($data['sport_type']), function ($query) use ($data) {
                $query->whereHas('fieldType', function ($q) use ($data) {
                    $q->whereRaw('LOWER(name) LIKE LOWER(?)', ['%'.$data['sport_type'].'%']);
                });
            })
            ->when(isset($data['price_min']) && ! empty($data['price_min']), function ($query) use ($data) {
                $query->where('price_per_hour', '>=', $data['price_min']);
            })
            ->when(isset($data['price_max']) && ! empty($data['price_max']), function ($query) use ($data) {
                $query->where('price_per_hour', '<=', $data['price_max']);
            })
            ->when(isset($data['sort']) && ! empty($data['sort']), function ($query) use ($data) {
                $sortField = $data['sort'];
                $sortDirection = $data['direction'] ?? 'asc';

                switch ($sortField) {
                    case 'name':
                        $query->orderBy('name', $sortDirection);
                        break;
                    case 'price':
                        $query->orderBy('price_per_hour', $sortDirection);
                        break;
                    case 'price_low_to_high':
                        $query->orderBy('price_per_hour', 'asc');
                        break;
                    case 'price_high_to_low':
                        $query->orderBy('price_per_hour', 'desc');
                        break;
                    case 'company_name':
                        $query->join('companies', 'fields.company_id', '=', 'companies.id')
                            ->orderBy('companies.name', $sortDirection)
                            ->select('fields.*');
                        break;
                    case 'city':
                        $query->join('companies', 'fields.company_id', '=', 'companies.id')
                            ->join('addresses', function ($join) {
                                $join->on('addresses.addressable_id', '=', 'companies.id')
                                    ->where('addresses.addressable_type', '=', 'App\\Models\\Company');
                            })
                            ->orderBy('addresses.city', $sortDirection)
                            ->select('fields.*');
                        break;
                    case 'newest':
                        $query->orderBy('created_at', 'desc');
                        break;
                    case 'oldest':
                        $query->orderBy('created_at', 'asc');
                        break;
                    case 'updated_at':
                        $query->orderBy('updated_at', $sortDirection);
                        break;
                    default:
                        $query->orderBy('created_at', 'desc');
                        break;
                }
            }, function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->paginate(
                $data['per_page'] ?? 12,
                ['*'],
                'page',
                $data['page'] ?? 1
            );

        return $fields;
    }
}
