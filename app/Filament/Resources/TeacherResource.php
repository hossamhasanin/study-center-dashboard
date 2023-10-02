<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherResource\Pages;
use App\Filament\Resources\TeacherResource\RelationManagers;
use App\Models\AcademicYear;
use App\Models\Subjects;
use App\Models\Teacher;
use App\Models\User;
use App\Models\UserTypes;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = "Center Management";
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function registerNavigationItems(): void
    {
        if (!UserTypes::isAdmin(Filament::auth()->user()->user_type)){
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
                Forms\Components\Section::make("Basic info")->schema([
                    Forms\Components\TextInput::make("name")
                        ->required()
                        ->live(debounce: 700)
                        ->afterStateUpdated(function ($set, $state) use ($form, $subjects){
                            $subject = $form->getRawState()["subject_id"];

                            if ($subject != ""){
                                $subject = $subjects[$subject];
                                $set('subject_teacher_name', "$subject ($state)");
                            }
                        }),

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

                    Forms\Components\TextInput::make('subject_teacher_name')
                        ->required()
                        ->disabled()
                        ->maxLength(255),

                    Forms\Components\Select::make('academic_years')
                        ->options(function () use ($isEditing, $form){
                            if ($isEditing){
                                return AcademicYear::query()
                                    ->whereNotIn("name", $form->getRecord()->academicYears()
                                        ->get(["name"])
                                        ->map(fn ($model) => $model->name)
                                        ->all())
                                    ->pluck("name", "name");
                            } else {
                                return AcademicYear::query()->pluck("name", "id");
                            }
                        })
                        ->searchable()
                        ->multiple()
                        ->required(),
                ])->columns(2),

                Forms\Components\Section::make("Authentication info")->schema([
                    Forms\Components\TextInput::make("email")
                        ->email()
                        ->unique(table: User::class, column: "email", ignorable: $form->getRecord() ? $form->getRecord()->user()->get()->first():null)
                        ->required(),
                    Forms\Components\TextInput::make("password")
                        ->password()
                        ->hiddenOn(["view"])
                        ->required(!$isEditing),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->badge()
                    ->color("info")
                    ->searchable(),
                Tables\Columns\TextColumn::make('academicYears.name')
                    ->badge()
                    ->color("success")
                    ->listWithLineBreaks()
                    ->searchable(),
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
                SelectFilter::make('academic_year_id')
                    ->label("Academic Year")
                    ->baseQuery(function (Builder $query, array $data): Builder {

                        if (count($data["values"]) == 0){
                            return $query;
                        }

                        $builder = $query
                            ->join("teachers_academic_years" , "teachers.id" , "=" , "teachers_academic_years.teacher_id")
                            ->join("academic_years", "teachers_academic_years.academic_year_id", "=", "academic_years.id")
                            ->select("teachers.*");

                        $filter = [];
                        foreach ($data["values"] as $value){
                            $filter[] = $value;
                        }

                        $builder->whereIn("academic_years.id", $filter);

                        return $builder->distinct();
                    })
                    ->multiple()
                    ->options(fn (): array => AcademicYear::query()->pluck('name', 'id')->all()),
                SelectFilter::make('subject_id')
                    ->label("Subject")
                    ->query(fn (Builder $query, array $data) => count($data["values"]) == 0 ? $query: $query->whereIn("subject_id" , $data["values"]))
                    ->multiple()
                    ->options(fn (): array => Subjects::query()->pluck('name', 'id')->all())
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
