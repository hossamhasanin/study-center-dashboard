<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherResource\Pages;
use App\Filament\Resources\TeacherResource\RelationManagers;
use App\Models\AcademicYear;
use App\Models\Subjects;
use App\Models\Teacher;
use App\Models\UserTypes;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static ?string $navigationGroup = "Center Management";
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function registerNavigationItems(): void
    {
        if (UserTypes::isAdmin(Filament::auth()->user()->user_type)){
            return;
        }

        parent::registerNavigationItems();
    }

    public static function form(Form $form): Form
    {
        $isEditing = $form->getRecord() != null;
        $subjects = Subjects::query()->pluck("name", "id");
        return $form
            ->schema([
                Forms\Components\TextInput::make("name")
                    ->required()
                    ->debounce()
                    ->afterStateUpdated(function ($set, $state) use ($form, $subjects){
                        $subject = $form->getRawState()["subject_id"];

                        if ($subject != ""){
                            $subject = $subjects[$subject];
                            $set('subject_teacher_name', "$subject ($state)");
                        }
                    }),
                Forms\Components\TextInput::make("email")
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make("password")
                ->password()
                ->required(!$isEditing),
                Forms\Components\Select::make('subject_id')
                    ->label("Subject")
                    ->options($subjects)
                    ->required()
                    ->reactive()
                    ->searchable()
                    ->afterStateUpdated(function ($set, $state) use ($form, $subjects){
                        $name = $form->getRawState()["name"];
                        $subject = $subjects[$state];
                        $set('subject_teacher_name', "$subject ($name)");
                    }),
                Forms\Components\Select::make('academic_years')
                    ->options(AcademicYear::query()->pluck("name", "id"))
                    ->searchable()
                    ->multiple()
                    ->required(),
                Forms\Components\TextInput::make('subject_teacher_name')
                    ->required()
                    ->disabled()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject_teacher_name')
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
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'view' => Pages\ViewTeacher::route('/{record}'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }
}
