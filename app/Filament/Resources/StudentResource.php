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

        $studentId += 100000;

        return $form
            ->schema([
                Forms\Components\Section::make("Authentication info")->schema([
                    Forms\Components\TextInput::make("email")
                        ->email()
                        ->unique(table: User::class, column: "email", ignorable: $form->getRecord() ? $form->getRecord()->user()->get()->first():null)
                        ->required(),
                    Forms\Components\TextInput::make("password")
                        ->password()
                        ->hiddenOn(["view"])
                        ->required(!$isEditing),
                    Forms\Components\TextInput::make('student_code')
                        ->disabled()
                        ->default("Student-$studentId")
                        ->suffixIcon("heroicon-m-identification")
                        ->columnSpanFull(),
                ])->columns(2),
                Forms\Components\Section::make("Basic info")->columns(2)->schema([
                    Forms\Components\Select::make('academic_year_id')
                        ->label("Academic year")
                        ->relationship('academicYear', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\Select::make('teachers')
                        ->label("Signup with")
                        ->options(function () use ($isEditing, $form){
                            if ($isEditing){
                                return Teacher::query()
                                    ->whereNotIn("subject_teacher_name", $form->getRecord()->teachers()
                                        ->get(["subject_teacher_name"])
                                        ->map(fn ($model) => $model->subject_teacher_name)
                                        ->all())
                                    ->pluck("subject_teacher_name", "subject_teacher_name");
                            } else {
                                return Teacher::query()->pluck("subject_teacher_name", "id");
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
                    ->numeric()->copyable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->numeric()
                    ->sortable(),
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
