<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamResource\Pages;
use App\Filament\Resources\ExamResource\RelationManagers;
use App\Models\Exam;
use App\Models\UserTypes;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use function PHPUnit\Framework\matches;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = "Study management";


    public static function registerNavigationItems(): void
    {
        if (!UserTypes::isTeacherOrAdmin(Filament::auth()->user()->user_type)){
            return;
        }

        parent::registerNavigationItems();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make("teacher_id")
                    ->label("Teacher")
                    ->relationship("teacher", "subject_teacher_name")
                    ->searchable()
                    ->preload()
                    ->hidden(UserTypes::isTeacherOnly(Filament::auth()->user()->user_type)),
                Forms\Components\Select::make("academic_year_id")
                    ->label("Academic year")
                    ->relationship("academicYear", "name")
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->maxLength(255),
                Forms\Components\TextInput::make('degree')
                    ->numeric()
                    ->required(),
                Forms\Components\DatePicker::make('start_date')
                    ->required()
                    ->default(Carbon::today()),
                Forms\Components\DatePicker::make('end_date')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isTeacher = UserTypes::isTeacherOnly(Filament::auth()->user()->user_type);
        $teacherId = $isTeacher ? Filament::auth()->user()->teacher()->first()->id : -1;
        return $table
            ->query($isTeacher ?
                Exam::query()->where("teacher_id", "=", $teacherId) :
                Exam::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('teacher.user.name')
                    ->label("Teacher name")
                    ->hidden(UserTypes::isTeacherOnly(Filament::auth()->user()->user_type)),
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label("Academic year")
                    ->badge(),
                Tables\Columns\TextColumn::make('degree')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make("Status")
                    ->default(function (?Exam $exam){
                        return Carbon::today()->between(Carbon::parse($exam->start_date), Carbon::parse($exam->end_date)) ? "Active" : "Ended";
                    })
                    ->badge()
                    ->color(fn ($state): string => match($state) {
                        "Active" => "success",
                        "Ended" => "danger"
                    }),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
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
            'index' => Pages\ListExams::route('/'),
            'create' => Pages\CreateExam::route('/create'),
            'view' => Pages\ViewExam::route('/{record}'),
            'edit' => Pages\EditExam::route('/{record}/edit'),
        ];
    }
}
