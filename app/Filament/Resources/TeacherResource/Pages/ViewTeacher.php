<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Filament\Resources\ResourcesHelpers;
use App\Filament\Resources\TeacherResource;
use App\Filament\Resources\TeacherResource\TeacherResourceHelpers;
use App\Models\AcademicYear;
use App\Models\Teacher;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTeacher extends ViewRecord
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
        ];
    }

    protected function fillFormWithDataAndCallHooks(array $data): void
    {
        $preparedData = TeacherResourceHelpers::prepareTeacherDataToView($data);
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
