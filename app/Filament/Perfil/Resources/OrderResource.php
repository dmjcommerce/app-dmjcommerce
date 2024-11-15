<?php

namespace App\Filament\Perfil\Resources;

use App\Filament\Perfil\Resources\OrderResource\Pages;
use App\Filament\Perfil\Resources\OrderResource\RelationManagers;
use App\Filament\Perfil\Resources\OrderResource\RelationManagers\AddressRelationManager;
use App\Models\Order;
use App\Models\product;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationGroup = 'Administracion';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 2;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Order Information')->schema([
                        Select::make('user_id')
                        ->label('Customer')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                        Select::make('payment_method')
                        ->options([
                            'stripe' => 'Stripe',
                            'cod' => 'Cash on Delivery'
                        ])
                        ->required(),

                        Select::make('payment_status')
                        ->options([
                            'pending'=>'Pending',
                            'paid'=>'Paid',
                            'failed'=>'Failed'
                        ])
                        ->default('pending')
                        ->required(),

                        ToggleButtons::make('status')
                        ->default('new')
                        ->required()
                        ->inline()
                        ->options([
                            'new'=>'New',
                            'processing'=>'Processing',
                            'shipped'=>'Shipped',
                            'delivered'=>'Delivered',
                            'canceled'=>'Cancelled'
                        ])
                        ->colors([
                            'new'=>'info',
                            'processing'=>'warning',
                            'shipped'=>'success',
                            'delivered'=>'success',
                            'canceled'=>'danger'
                        ])
                        ->icons([
                            'new'=>'heroicon-m-sparkles',
                            'processing'=>'heroicon-m-arrow-path',
                            'shipped'=>'heroicon-m-truck',
                            'delivered'=>'heroicon-m-check-badge',
                            'canceled'=>'heroicon-m-x-circle'
                        ]),

                        Select::make('currency')
                        ->options([
                            'cop'=>'COP',
                            'usd'=>'USD',
                            'eur'=>'EUR',
                            'gbp'=>'GBP'
                        ])
                        ->default('cop')
                        ->required(),

                        Select::make('shipping_method')
                        ->options([
                            'fedex'=>'FedEx',
                            'ups'=>'UPS',
                            'dhl'=>'DHL',
                            'usps'=>'USPS'
                        ]),

                        Textarea::make('notes')
                        ->columnSpanFull()
                    ])->columns(2),

                    Section::make('Order Items')->schema([
                        Repeater::make('items')
                        ->relationship()
                        ->schema([

                            Select::make('product_id')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->distinct()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->columnSpan(4)
                            ->reactive()
                            //Se pasa el estado de manera que cuando seleccionemos un producto
                            //Obtendermos el id del producto
                            ->afterStateUpdated(fn ($state, Set $set) =>$set('unit_amount', product::find($state)?->price ?? 0))
                            ->afterStateUpdated(fn ($state, Set $set) =>$set('total_amount', product::find($state)?->price ?? 0)),

                            TextInput::make('quantity')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1)
                            ->columnSpan(2)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, Set $set, Get $get)=> $set('total_amount', 
                            $state * $get('unit_amount'))),

                            TextInput::make('unit_amount')
                            ->numeric()
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(3),

                            TextInput::make('total_amount')
                            ->numeric()
                            ->required()
                            ->dehydrated()
                            ->columnSpan(3)
                        ])->columns(12),
                        
                        //Con este se da un total general de los productos seleccionados
                        Placeholder::make('grand_total_placeholder')
                        ->label('Gran Total')
                        ->content(function (Get $get, Set $set){
                            $total = 0;
                            if (!$repeaters = $get('items')){
                                return $total;
                            }

                            foreach ($repeaters as $key => $repeater){
                                $total += $get("items.{$key}.total_amount");
                            }
                            $set('grand_total', $total);
                            return Number::currency($total, 'COP');
                        }),

                        Hidden::make('grand_total')
                        ->default(0)
                    ])
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                ->sortable()
                ->label('Customer')
                ->searchable(),

                TextColumn::make('grand_total')
                ->numeric()
                ->sortable()
                ->money('COP'),

                TextColumn::make('payment_method')
                ->searchable()
                ->sortable(),

                TextColumn::make('payment_status')
                ->searchable()
                ->sortable(),

                TextColumn::make('currency')
                ->searchable()
                ->sortable(),

                TextColumn::make('shipping_method')
                ->searchable()
                ->sortable(),

                SelectColumn::make('status')
                ->options([
                    'new'=>'New',
                    'processing'=>'Processing',
                    'shipped'=>'Shipped',
                    'delivered'=>'Delivered',
                    'canceled'=>'Cancelled'
                ])
                ->searchable()
                ->sortable(),

                TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault:true),

                TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault:true)
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //Dentro de esta funcion se obtiene la relacion creada con address para que podamos
            //Crear direcciones en las ordenes.
            AddressRelationManager::class
        ];
    }
    //Esta funcion es para mostrar la cantidad de ordenes en el panel de ordenes 
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    //Este metodo cambia el color del conteo si es mayor a 10, de lo contrario lo deja verde
    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? 'danger' : 'success';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
