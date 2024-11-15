<?php

namespace App\Filament\Perfil\Resources\UserResource\RelationManagers;

use App\Filament\Perfil\Resources\OrderResource;
use App\Models\order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id')
                ->label('Order ID')
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

            ])
            ->filters([
                //
            ])
            ->headerActions([
                //Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                /*se crea la siguiente accion personalizada para redirigir y dar una vista detallada 
                del pedido, esto redirige a la pagina de view de la orden*/
                Action::make('View Order')
                ->url(fn (order $record):string => OrderResource::getUrl('view', ['record' => $record]))
                ->color('info')
                ->icon('heroicon-o-eye'),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
