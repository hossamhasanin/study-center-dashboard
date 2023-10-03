<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Filament\Resources\ResourcesHelpers;
use App\Filament\Resources\TeacherResource;
use App\Models\UserTypes;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;

class ListTeachers extends ListRecords
{
    protected static string $resource = TeacherResource::class;
    public function mount(): void
    {
        abort_unless(UserTypes::isAdmin(Filament::auth()->user()->user_type), 403);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function configureDeleteAction(Tables\Actions\DeleteAction $action): void
    {
        parent::configureDeleteAction($action);

        $action->after(function () use($action){
            ResourcesHelpers::deleteUser($action->getRecord()->user_id);
        });
    }

    protected function configureDeleteBulkAction(Tables\Actions\DeleteBulkAction $action): void
    {
        parent::configureDeleteBulkAction($action);

        $action->after(function () use($action){
            foreach ($action->getRecords() as $record){
                ResourcesHelpers::deleteUser($record->user_id);
            }
        });
    }
}
