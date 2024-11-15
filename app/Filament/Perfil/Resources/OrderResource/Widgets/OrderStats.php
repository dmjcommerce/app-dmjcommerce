<?php

namespace App\Filament\Perfil\Resources\OrderResource\Widgets;

use App\Models\order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        //Obtiene los pedidos y se llama a la clase en ListOrders para mostrarse
        return [
            Stat::make('New Orders', order::query()->where('status', 'new')->count()),
            Stat::make('Order Processing', order::query()->where('status', 'processing')->count()),
            Stat::make('Order Shipped', order::query()->where('status', 'shipped')->count()),
            Stat::make('Average Price', Number::currency(order::query()->avg('grand_total')))
        ];
    }
}
