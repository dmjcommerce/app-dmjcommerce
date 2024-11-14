<?php

namespace App\Filament\Perfil\Resources\UserResource\Pages;

use App\Filament\Perfil\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
