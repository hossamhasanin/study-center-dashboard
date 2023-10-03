<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\ResourcesHelpers;
use App\Filament\Resources\StudentResource;
use App\Models\UserTypes;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    public function mount(): void
    {
        abort_unless(UserTypes::isTeacher(Filament::auth()->user()->user_type), 403);
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
