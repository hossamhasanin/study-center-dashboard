<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Filament\Resources\TeacherResource;
use App\Models\Teacher;
use App\Models\User;
use App\Models\UserTypes;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateTeacher extends CreateRecord
{
    protected static string $resource = TeacherResource::class;

//    protected function beforeValidate(){
//        $data = collect($this->data);
//        $userAuthData = $data
//            ->only(["name", "email","password"])
//            ->merge(["user_type" => UserTypes::Teacher->value])
//            ->all();
//        $validatedUserData = Validator::make($userAuthData, [
//            "name" => "required",
//            "email" => "required|unique:users|email",
//            "password" => "required"
//        ])->stopOnFirstFailure();
//
//        if ($validatedUserData->fails()){
//            Notification::make()
//                ->danger()
//                ->title($validatedUserData->errors()->first())
//                ->send();
//            throw new Halt();
//        }
//    }

//    protected function onValidationError(ValidationException $exception): void
//    {
//        Notification::make()
//            ->title($exception->getMessage())
//            ->danger()
//            ->send();
//    }

    protected function beforeCreate() : void{
        $data = collect($this->data);
        $userAuthData = $data
            ->only(["name", "email","password"])
            ->merge(["user_type" => UserTypes::Teacher->value])
            ->all();
        /**
         * @var User | $user
         */
        $user = User::query()->create($userAuthData);
        $this->data = $data
            ->except(["name", "email","password"])
            ->merge(["user_id" => $user->id])
            ->all();
    }

    protected function handleRecordCreation(array $data): Model
    {
        $dataCollection = collect($this->data);
        /** @var Teacher $record */
        $record = parent::handleRecordCreation($dataCollection->except(["academic_years"])->all());

        $record->academicYears()->attach($dataCollection->only(["academic_years"])->first());

        return $record;
    }
}
