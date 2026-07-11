<?php

namespace App\Filament\Resources\Divisions\Tables\Actions;

use App\Services\BracketEngine;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class GenerateBracketAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'generateBracket';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Generate Bracket')
            ->icon('heroicon-o-squares-plus')
            ->color('primary')
            ->requiresConfirmation()
            ->modalHeading('Generate Bracket')
            ->modalDescription('Semua match lama di divisi ini akan dihapus dan diganti dengan bracket baru. Lanjutkan?')
            ->modalSubmitActionLabel('Ya, Generate')
            ->action(function ($record, array $data) {
                $engine = app(BracketEngine::class);
                $result = $engine->generateForDivision($record);

                Notification::make()
                    ->title($result['matches_count'] > 0 ? 'Bracket berhasil dibuat' : 'Gagal generate')
                    ->body($result['message'])
                    ->{$result['matches_count'] > 0 ? 'success' : 'warning'}()
                    ->send();
            });
    }
}
