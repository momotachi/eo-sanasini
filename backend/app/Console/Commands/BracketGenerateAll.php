<?php

namespace App\Console\Commands;

use App\Models\Division;
use App\Services\BracketEngine;
use Illuminate\Console\Command;

class BracketGenerateAll extends Command
{
    protected $signature = 'bracket:generate-all
        {event_id : ID Event yang mau di-generate semua bracket-nya}
        {--force : Skip konfirmasi}';
    protected $description = 'Generate bracket untuk SEMUA Division di sebuah Event. Berguna untuk cron atau pre-event batch.';

    public function handle(BracketEngine $engine): int
    {
        $eventId = $this->argument('event_id');

        $divisions = Division::where('event_id', $eventId)
            ->whereIn('format', ['FULL_KNOCKOUT', 'GROUP_KNOCKOUT', 'ROUND_ROBIN'])
            ->get();

        if ($divisions->isEmpty()) {
            $this->warn("Tidak ada Division kompetitif di Event ID {$eventId}.");
            return self::FAILURE;
        }

        $this->info("Ditemukan {$divisions->count()} division di Event {$eventId}:");

        $divisions->each(function ($d, $i) {
            $approved = $d->participants()->where('status', 'APPROVED')->count();
            $this->line("  [" . ($i + 1) . "] {$d->discipline} — {$d->age_category} {$d->gender} {$d->class_name} ({$approved} peserta)");
        });

        if (!$this->option('force')) {
            if (!$this->confirm('Lanjutkan generate semua bracket?')) {
                $this->info('Dibatalkan.');
                return self::SUCCESS;
            }
        }

        $success = 0;
        $failed = 0;
        $bar = $this->output->createProgressBar($divisions->count());
        $bar->start();

        foreach ($divisions as $division) {
            $result = $engine->generateForDivision($division);
            if ($result['matches_count'] > 0) {
                $success++;
            } else {
                $failed++;
                $this->line('');
                $this->warn("  ✗ Divisi #{$division->id}: {$result['message']}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->line('');
        $this->info("Selesai. Berhasil: {$success}, Gagal: {$failed}.");
        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
