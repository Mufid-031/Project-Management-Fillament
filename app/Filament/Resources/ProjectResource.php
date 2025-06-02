<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\Pages\ProjectTimeline;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Project Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(), // Agar lebar penuh
                Forms\Components\Select::make('status')
                    ->options([
                        'Baru' => 'Baru',
                        'Dikerjakan' => 'Dikerjakan',
                        'Selesai' => 'Selesai',
                        'Ditunda' => 'Ditunda',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('due_date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('due_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('tasks_count')->counts('tasks')->label('Jumlah Tugas'), // Menampilkan jumlah task
            ])
            ->filters([
                // Tambahkan filter jika perlu, misal berdasarkan status
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Baru' => 'Baru',
                        'Dikerjakan' => 'Dikerjakan',
                        'Selesai' => 'Selesai',
                        'Ditunda' => 'Ditunda',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                // Tables\Actions\Action::make('timeline')
                //     ->label('Timeline Tugas')
                //     ->icon('heroicon-o-chart-bar-square') // Sesuaikan ikon
                //     ->url(fn(Project $record): string => static::getUrl('timeline', ['record' => $record])),
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
            RelationManagers\TasksRelationManager::class, // Daftarkan relation manager untuk tasks
            RelationManagers\TeamMembersRelationManager::class, // Daftarkan relation manager untuk team members
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
            'timeline' => ProjectTimeline::route('/{record}/timeline'), // Halaman timeline kita
        ];
    }
}
