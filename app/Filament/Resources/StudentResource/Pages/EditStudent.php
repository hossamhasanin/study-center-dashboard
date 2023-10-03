<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\ResourcesHelpers;
use App\Filament\Resources\StudentResource;
use App\Filament\Resources\StudentResource\StudentResourceHelper;
use App\Filament\Resources\TeacherResource\TeacherResourceHelpers;
use App\Models\AcademicYear;
use App\Models\Teacher;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeSave(){
        $data = collect($this->data);
        $userAuthData = $data
            ->only(["name", "email","password"])
            ->map(function ($value, $key) use($data){
                if ($key == "email" && $value == null){
                    return $data["student_code"]."@student.com";
                }
                return $value;
            })
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

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $dataCollection = collect($this->data);
        $teachers = Teacher::query()
            ->whereIn("subject_teacher_name" , $dataCollection->only(["teachers"])->first())
            ->get(["id"])
            ->map(fn ($model) => $model->id)
            ->all();
        $record->teachers()->sync($teachers);
        return parent::handleRecordUpdate($record, $dataCollection->except(["teachers"])->all());
    }

    protected function fillFormWithDataAndCallHooks(array $data): void
    {
        $preparedData = StudentResourceHelper::prepareStudentDataToView($data);
        parent::fillFormWithDataAndCallHooks($preparedData);
    }

    protected function afterSave(){
        $this->fillForm();
    }

    protected function configureDeleteAction(DeleteAction $action): void
    {
        parent::configureDeleteAction($action);
        $action->after(function () use($action){
            ResourcesHelpers::deleteUser($action->getRecord()->user_id);
        });
    }
}
