<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegistrationResource\Pages;
use App\Mail\RegistrationCancellation;
use App\Models\Activity;
use App\Models\Registration;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Iscrizione';

    protected static ?string $pluralModelLabel = 'Iscrizioni';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nome e Cognome')
                    ->getStateUsing(fn ($record) => $record->first_name.' '.$record->last_name)
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name', 'last_name']),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telefono'),
                TextColumn::make('activity.name')
                    ->label('Attività')
                    ->sortable(),
                TextColumn::make('minors_count')
                    ->counts('minors')
                    ->label('Num. minori'),
                TextColumn::make('created_at')
                    ->label('Data iscrizione')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('is_cai_member')
                    ->label('Socio CAI')
                    ->formatStateUsing(fn ($state) => $state ? 'Sì' : 'No'),
                TextColumn::make('caiSection.name')
                    ->label('Sezione')
                    ->default('Non socio'),
            ])
            ->filters([
                SelectFilter::make('activity_id')
                    ->label('Attività')
                    ->options(fn () => Activity::pluck('name', 'id')),
                Filter::make('search')
                    ->form([
                        TextInput::make('query')
                            ->label('Cerca per nome, cognome o email')
                            ->placeholder('Mario Rossi'),
                    ])
                    ->query(function (Builder $query, array $data): void {
                        if ($data['query'] ?? null) {
                            $q = $data['query'];
                            $query->where(function (Builder $sub) use ($q): void {
                                $sub->where('first_name', 'like', "%{$q}%")
                                    ->orWhere('last_name', 'like', "%{$q}%")
                                    ->orWhere('email', 'like', "%{$q}%");
                            });
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Elimina iscrizione')
                    ->modalDescription(fn (Registration $record): string => 'Sei sicuro di voler eliminare l\'iscrizione di '.$record->first_name.' '.$record->last_name.'? L\'operazione non è reversibile.')
                    ->modalSubmitActionLabel('Sì, elimina')
                    ->action(function (Registration $record): void {
                        $mailable = new RegistrationCancellation($record);
                        $record->minors()->delete();
                        $record->delete();
                        Mail::to($mailable->registration->email)->send($mailable);
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Dati adulto')
                    ->schema([
                        TextEntry::make('first_name')->label('Nome'),
                        TextEntry::make('last_name')->label('Cognome'),
                        TextEntry::make('email'),
                        TextEntry::make('phone')->label('Telefono'),
                        TextEntry::make('birth_date')->label('Data di nascita')->date('d/m/Y'),
                        TextEntry::make('is_cai_member')
                            ->label('Socio CAI')
                            ->formatStateUsing(fn ($state) => $state ? 'Sì' : 'No'),
                        TextEntry::make('caiSection.name')->label('Sezione CAI')->default('—'),
                        TextEntry::make('fiscal_code')->label('Codice fiscale')->default('—'),
                    ])->columns(2),
                Section::make('Attività scelta')
                    ->schema([
                        TextEntry::make('activity.name')->label('Rifugio'),
                        TextEntry::make('activity.meeting_time')->label('Orario ritrovo'),
                        TextEntry::make('activity.meeting_place')->label('Luogo di partenza'),
                    ])->columns(3),
                Section::make('Minori')
                    ->schema([
                        RepeatableEntry::make('minors')
                            ->label('')
                            ->schema([
                                TextEntry::make('first_name')->label('Nome'),
                                TextEntry::make('last_name')->label('Cognome'),
                                TextEntry::make('birth_date')->label('Data di nascita')->date('d/m/Y'),
                                TextEntry::make('is_cai_member')
                                    ->label('Socio CAI')
                                    ->formatStateUsing(fn ($state) => $state ? 'Sì' : 'No'),
                                TextEntry::make('caiSection.name')->label('Sezione CAI')->default('—'),
                                TextEntry::make('fiscal_code')->label('Codice fiscale')->default('—'),
                            ])->columns(3),
                    ])->hidden(fn (Registration $record) => $record->minors->isEmpty()),
                Section::make('Consensi')
                    ->schema([
                        TextEntry::make('privacy_accepted')
                            ->label('Privacy')
                            ->formatStateUsing(fn ($state) => $state ? 'Accettato' : 'Non accettato'),
                        TextEntry::make('photo_release_accepted')
                            ->label('Liberatoria foto/video')
                            ->formatStateUsing(fn ($state) => $state ? 'Accettato' : 'Non accettato'),
                        TextEntry::make('rules_accepted')
                            ->label('Regolamento escursione')
                            ->formatStateUsing(fn ($state) => $state ? 'Accettato' : 'Non accettato'),
                        TextEntry::make('weather_cancellation_accepted')
                            ->label('Annullamento maltempo')
                            ->formatStateUsing(fn ($state) => $state ? 'Accettato' : 'Non accettato'),
                        TextEntry::make('equipment_check_accepted')
                            ->label('Controllo attrezzatura')
                            ->formatStateUsing(fn ($state) => $state ? 'Accettato' : 'Non accettato'),
                    ])->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistrations::route('/'),
            'view' => Pages\ViewRegistration::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['activity', 'caiSection', 'minors.caiSection']);
    }
}
