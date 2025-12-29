<?php

namespace App\Nova\Dashboards;

use Laravel\Nova\Dashboard;

class Main extends Dashboard
{
    public function name(): string
    {
        return 'Painel da Transparência';
    }

    public function cards(): array
    {
        return [
            new \App\Nova\Metrics\UsersCount,
            new \App\Nova\Metrics\ProfilesCount,
            new \App\Nova\Metrics\CompanyCount,
            new \App\Nova\Metrics\FieldsCount,
            new \App\Nova\Metrics\TeamsCount,
            new \App\Nova\Metrics\BookingsCount,
            new \App\Nova\Metrics\TabsCount,
            new \App\Nova\Metrics\ExpensesCount,
            new \App\Nova\Metrics\ProductsCount,
        ];
    }
}
