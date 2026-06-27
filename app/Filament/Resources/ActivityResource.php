<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use App\Models\Activity;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $modelLabel = 'Attività';

    protected static ?string $pluralModelLabel = 'Attività';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Attività')
                ->columnSpanFull()
                ->tabs([

                    Tabs\Tab::make('Principale')->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('meeting_time')
                            ->label('Orario'),
                        TextInput::make('meeting_place')
                            ->label('Luogo di ritrovo')
                            ->columnSpanFull(),
                        TextInput::make('max_capacity')
                            ->label('Capienza massima')
                            ->numeric(),
                        Toggle::make('is_active')
                            ->label('Attiva'),
                    ])->columns(2),

                    Tabs\Tab::make('Descrizione')->schema([
                        Textarea::make('description')
                            ->label('Descrizione')
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('detailed_description')
                            ->label('Descrizione dettagliata')
                            ->rows(5)
                            ->columnSpanFull(),
                        Textarea::make('itinerary_description')
                            ->label('Descrizione itinerario')
                            ->rows(4)
                            ->columnSpanFull(),
                        TextInput::make('image_url')
                            ->label('URL immagine')
                            ->url()
                            ->live(onBlur: true)
                            ->columnSpanFull(),
                        Placeholder::make('image_preview')
                            ->label('Anteprima immagine')
                            ->columnSpanFull()
                            ->content(function (Get $get) {
                                $url = $get('image_url');
                                if (! $url) {
                                    return new HtmlString('<span class="text-sm text-gray-400 italic">Inserisci un URL per vedere l\'anteprima</span>');
                                }

                                return new HtmlString(
                                    '<img src="' . e($url) . '" alt="Anteprima" style="max-width:100%;max-height:340px;border-radius:8px;object-fit:cover;box-shadow:0 2px 8px rgba(0,0,0,0.12);">'
                                );
                            }),
                    ]),

                    Tabs\Tab::make('Dati tecnici')->schema([
                        TextInput::make('difficulty')
                            ->label('Difficoltà'),
                        TextInput::make('elevation_gain')
                            ->label('Dislivello'),
                        TextInput::make('trail_length')
                            ->label('Lunghezza / Durata'),
                        TextInput::make('water_description')
                            ->label('Acqua'),
                    ])->columns(2),

                    Tabs\Tab::make('Mappa')->schema([
                        Placeholder::make('interactive_map')
                            ->label('Posizione sulla mappa')
                            ->columnSpanFull()
                            ->content(function (?Activity $record) {
                                return new HtmlString(
                                    view('filament.activity-interactive-map', [
                                        'lat' => $record?->latitude ?? 45.4654,
                                        'lng' => $record?->longitude ?? 9.1859,
                                        'zoom' => ($record?->latitude && $record?->longitude) ? 13 : 6,
                                    ])->render()
                                );
                            }),

                        Grid::make(2)->schema([
                            TextInput::make('latitude')
                                ->label('Latitudine')
                                ->numeric()
                                ->step(0.000001),
                            TextInput::make('longitude')
                                ->label('Longitudine')
                                ->numeric()
                                ->step(0.000001),
                        ]),
                    ]),

                ]),
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
            'edit' => Pages\EditActivity::route('/{record}/edit'),
        ];
    }
}
