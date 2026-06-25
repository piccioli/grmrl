<?php

namespace App\Filament\Resources\RegistrationResource\Pages;

use App\Filament\Resources\RegistrationResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListRegistrations extends ListRecords
{
    protected static string $resource = RegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Esporta Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn (): string => route('filament.admin.admin.export', array_filter([
                    'activity_id' => $this->tableFilters['activity_id']['value'] ?? null,
                ])))
                ->openUrlInNewTab(),
            Action::make('print')
                ->label('Vista stampabile')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn (): string => route('filament.admin.admin.print', array_filter([
                    'activity_id' => $this->tableFilters['activity_id']['value'] ?? null,
                ])))
                ->openUrlInNewTab(),
        ];
    }
}
