<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Filament\Resources\ExamResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateExam extends CreateRecord
{
    protected static string $resource = ExamResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $dataCollection = collect($data);
        if ($dataCollection->get("teacher_id") == null){
            $dataCollection = $dataCollection->merge(["teacher_id" => Filament::auth()->user()->teacher()->first()->id]);
        }

        return $dataCollection->all();
    }
}
