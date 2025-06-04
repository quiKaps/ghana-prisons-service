<?php

namespace App\Filament\HQ\Resources\UserResource\Pages;

use App\Filament\HQ\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
