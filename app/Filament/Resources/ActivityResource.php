<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use App\Models\Activity;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $modelLabel = 'Attività';

    protected static ?string $pluralModelLabel = 'Attività';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Nome')
                ->required()
                ->maxLength(255),
            Textarea::make('description')
                ->label('Descrizione')
                ->rows(3),
            TextInput::make('meeting_time')
                ->label('Orario'),
            TextInput::make('meeting_place')
                ->label('Luogo di ritrovo'),
            TextInput::make('max_capacity')
                ->label('Capienza massima')
                ->numeric(),
            Toggle::make('is_active')
                ->label('Attiva'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('meeting_place')
                    ->label('Luogo'),
                TextColumn::make('meeting_time')
                    ->label('Orario'),
                TextColumn::make('max_capacity')
                    ->label('Capienza'),
                TextColumn::make('available_spots')
                    ->label('Posti rimasti')
                    ->getStateUsing(fn (Activity $record) => $record->availableSpots()),
                IconColumn::make('is_active')
                    ->label('Stato')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
            'edit'  => Pages\EditActivity::route('/{record}/edit'),
        ];
    }
}
