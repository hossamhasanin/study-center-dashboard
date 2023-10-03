<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\Subjects;
use App\Models\Teacher;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = "Study management";

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function form(Form $form): Form
    {
        $isEditing = $form->getRecord() != null;

        if ($isEditing){
            $studentId = $form->getRecord()->student_code;
        } else {
            $latestStudent = Student::query()->latest()->first() ?? 0;
            $studentId = $latestStudent == 0 ? 0 : $latestStudent->id + 1;
        }

        $studentId = intval($studentId) + 100000;

        return $form
            ->schema([
                Forms\Components\Section::make("Authentication info")->schema([
                    Forms\Components\TextInput::make('student_code')
                        ->disabled()
                        ->default("Student-$studentId")
                        ->suffixIcon("heroicon-m-identification")
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make("email")
                        ->email()
                        ->unique(table: User::class, column: "email", ignorable: $form->getRecord() ? $form->getRecord()->user()->get()->first():null),
                    Forms\Components\TextInput::make("password")
                        ->password()
                        ->hiddenOn(["view"])
                        ->required(!$isEditing),
                ])->columns(2),
                Forms\Components\Section::make("Basic info")->columns(2)->schema([
                    Forms\Components\TextInput::make("name")
                        ->required(),
                    Forms\Components\Select::make('academic_year_id')
                        ->label("Academic year")
                        ->relationship('academicYear', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($set, $state){
                            $set("teachers" , []);
                        }),

                    Forms\Components\Select::make('teachers')
                        ->label("Teachers to signup with")
                        ->live()
                        ->options(function () use ($isEditing, $form){
                            $academicYear = $form->getRawState()["academic_year_id"];
                            if ($academicYear == null){
                                return [];
                            }

                            if ($isEditing){
                                return AcademicYear::query()->find($academicYear)->teachers()
                                    ->whereNotIn("subject_teacher_name", $form->getRecord()->teachers()
                                        ->get(["subject_teacher_name"])
                                        ->map(fn ($model) => $model->subject_teacher_name)
                                        ->all())
                                    ->pluck("subject_teacher_name", "subject_teacher_name");
                            } else {
                                return AcademicYear::query()->find($academicYear)->teachers()->pluck("subject_teacher_name", "id");
                            }
                        })
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->badge()
                    ->color("info")
                    ->sortable(),
                Tables\Columns\TextColumn::make('teachers.subject_teacher_name')
                    ->badge()
                    ->color("success")
                    ->listWithLineBreaks()
                    ->searchable(),
                Tables\Columns\TextColumn::make('student_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'view' => Pages\ViewStudent::route('/{record}'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
