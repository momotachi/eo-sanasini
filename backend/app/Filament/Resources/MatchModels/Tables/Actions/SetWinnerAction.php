<?php

namespace App\Filament\Resources\MatchModels\Tables\Actions;

use App\Models\Participant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class SetWinnerAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'setWinner';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Set Hasil')
            ->icon('heroicon-o-trophy')
            ->color('success')
            ->visible(fn($record) => $record->status !== 'COMPLETED' && ($record->participant_a_id || $record->participant_b_id))
            ->fillForm(function ($record): array {
                return [
                    'winner_id' => $record->winner_id,
                    'score_a' => $record->score_a['value'] ?? null,
                    'score_b' => $record->score_b['value'] ?? null,
                ];
            })
            ->form([
                Select::make('winner_id')
                    ->label('Pemenang')
                    ->options(fn($record) => collect([
                        $record->participant_a_id => $record->participantA?->name,
                        $record->participant_b_id => $record->participantB?->name,
                    ])->filter())
                    ->required(),
                TextInput::make('score_a')->label('Skor A (opsional)'),
                TextInput::make('score_b')->label('Skor B (opsional)'),
            ])
            ->action(function ($record, array $data) {
                $record->update([
                    'winner_id' => $data['winner_id'],
                    'score_a' => isset($data['score_a']) && $data['score_a'] !== '' ? ['value' => $data['score_a']] : $record->score_a,
                    'score_b' => isset($data['score_b']) && $data['score_b'] !== '' ? ['value' => $data['score_b']] : $record->score_b,
                    'status' => 'COMPLETED',
                ]);

                Notification::make()
                    ->title('Hasil pertandingan disimpan')
                    ->body('Pemenang: ' . ($record->winner?->name ?? '-'))
                    ->success()
                    ->send();
            });
    }
}
