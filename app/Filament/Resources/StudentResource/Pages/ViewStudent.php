<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\ResourcesHelpers;
use App\Filament\Resources\StudentResource;
use App\Filament\Resources\StudentResource\StudentResourceHelper;
use App\Filament\Resources\TeacherResource\TeacherResourceHelpers;
use Filament\Actions;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function fillFormWithDataAndCallHooks(array $data): void
    {
        $preparedData = StudentResourceHelper::prepareStudentDataToView($data);
        parent::fillFormWithDataAndCallHooks($preparedData);
    }

    protected function configureDeleteAction(DeleteAction $action): void
    {
        parent::configureDeleteAction($action);
        $action->after(function () use($action){
            ResourcesHelpers::deleteUser($action->getRecord()->user_id);
        });
    }
}
