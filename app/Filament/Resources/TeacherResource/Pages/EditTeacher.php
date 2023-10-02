<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Filament\Resources\TeacherResource;
use App\Filament\Resources\TeacherResource\TeacherResourceHelpers;
use App\Models\AcademicYear;
use App\Models\Teacher;
use App\Models\User;
use App\Models\UserTypes;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTeacher extends EditRecord
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

//    protected function getRedirectUrl(): ?string
//    {
//        return TeacherResource::getUrl("edit" , ["record" => $this->getRecord()]);
//    }

    protected function beforeSave(){
        $data = collect($this->data);
        $userAuthData = $data
            ->only(["name", "email","password"])
            ->filter(fn ($value) => $value != null)
            ->all();
        /**
         * @var User | $user
         */
        $user = User::query()->find($data["user_id"]);
        $user->update($userAuthData);
        $this->data = $data
            ->except(["name", "email","password"])
            ->all();
    }

    /** @param Teacher $record */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $dataCollection = collect($this->data);
        $academicYears = AcademicYear::query()
            ->whereIn("name" , $dataCollection->only(["academic_years"])->first())
            ->get(["id"])
            ->map(fn ($model) => $model->id)
            ->all();
        $record->academicYears()->sync($academicYears);
        return parent::handleRecordUpdate($record, $dataCollection->except(["academic_years"])->all());
    }

    protected function fillFormWithDataAndCallHooks(array $data): void
    {
        $preparedData = TeacherResourceHelpers::prepareTeacherDataToView($data);
        parent::fillFormWithDataAndCallHooks($preparedData);
    }

    protected function afterSave(){
        $this->fillForm();
    }
}
