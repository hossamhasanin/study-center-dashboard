<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Filament\Resources\TeacherResource;
use App\Filament\Resources\TeacherResource\TeacherResourceHelpers;
use App\Models\AcademicYear;
use App\Models\Teacher;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTeacher extends ViewRecord
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function fillFormWithDataAndCallHooks(array $data): void
    {
        $preparedData = TeacherResourceHelpers::prepareTeacherDataToView($data);
        parent::fillFormWithDataAndCallHooks($preparedData);
    }
}
