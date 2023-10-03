<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\Teacher;
use App\Models\User;
use App\Models\UserTypes;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function beforeCreate() : void{
        $data = collect($this->data);
        $userAuthData = $data
            ->only(["name", "email","password"])
            ->map(function ($value, $key) use($data){
                if ($key == "email" && $value == null){
                    return $data["student_code"]."@student.com";
                }
                return $value;
            })
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
        $record = parent::handleRecordCreation($dataCollection->except(["teachers"])->all());

        $record->teachers()->attach($dataCollection->only(["teachers"])->first());

        return $record;
    }
}
