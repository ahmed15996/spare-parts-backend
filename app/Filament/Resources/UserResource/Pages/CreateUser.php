<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\UserTypeEnum;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    

    protected static string $resource = UserResource::class;


    public function getTitle(): string
    {
        return __("Create User");
    }
    public function getBreadcrumb(): string
    {
        return __("Create User");
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = UserTypeEnum::Admin;
        return $data;
    }
}