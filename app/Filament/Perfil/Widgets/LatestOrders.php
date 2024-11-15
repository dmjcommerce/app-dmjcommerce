<?php

namespace App\Filament\Perfil\Widgets;

use App\Filament\Perfil\Resources\OrderResource;
use App\Models\order;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    //Con esto se amplia la tabla de pedidos en el dashboard
    protected int | string | array $columnSpan = 'full'; 
    //Con esto las estadisticas del dashboard se posicionan de primero
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        //Se mostraran en el dashboard los ultimos pedidos
        return $table
            ->query(OrderResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                ->label('Order ID')
                ->searchable(),
                
                TextColumn::make('user.name')
                ->searchable(),

                TextColumn::make('grand_total')
                ->money('COP'),

                TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->color(fn (string $state):string => match($state){
                    'new' => 'info',
                    'processing' => 'warning',
                    'shipped' => 'success',
                    'delivered' => 'success',
                    'canceled' => 'danger'
                })
                ->icon(fn (string $state):string => match($state){
                    'new'=>'heroicon-m-sparkles',
                    'processing'=>'heroicon-m-arrow-path',
                    'shipped'=>'heroicon-m-truck',
                    'delivered'=>'heroicon-m-check-badge',
                    'canceled'=>'heroicon-m-x-circle'
                })
                ->sortable(),

                TextColumn::make('payment_method')
                ->sortable()
                ->searchable(),

                TextColumn::make('payment_status')
                ->badge()
                ->sortable()
                ->searchable(),

                TextColumn::make('created_at')
                ->label('Order Date')
            ])->actions([
                Action::make('View Order')
                 ->url(fn (order $record):string => OrderResource::getUrl('view', ['record' => $record]))
                 ->icon('heroicon-m-eye'),
            ]);
    }
}
