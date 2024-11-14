<?php

namespace App\Filament\Perfil\Resources\OrderResource\Pages;

use App\Filament\Perfil\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}
