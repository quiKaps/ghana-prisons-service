<?php

namespace App\Filament\Station\Resources\UserResource\Pages;

use App\Filament\Station\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
