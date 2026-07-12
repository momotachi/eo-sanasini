<?php

namespace App\Console\Commands;

use App\Models\Division;
use App\Services\BracketEngine;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BracketGenerate extends Command
{
    protected $signature = 'bracket:generate {division_id : ID Division yang mau di-generate bracket-nya}';
    protected $description = 'Generate bracket untuk sebuah Division (sport). Match lama akan dihapus & diganti.';

    public function handle(BracketEngine $engine): int
    {
        $divisionId = $this->argument('division_id');

        try {
            $division = Division::findOrFail($divisionId);
        } catch (ModelNotFoundException $e) {
            $this->error("Division dengan ID {$divisionId} tidak ditemukan.");
            return self::FAILURE;
        }

        if (!in_array($division->format, ['FULL_KNOCKOUT', 'GROUP_KNOCKOUT', 'ROUND_ROBIN'])) {
            $this->warn("Division ini format-nya '{$division->format}' — tidak didukung auto-generate.");
            return self::FAILURE;
        }

        $approvedCount = $division->participants()->where('status', 'APPROVED')->count();
        if (!$this->confirm(
            "Generate bracket untuk: {$division->discipline} — {$division->age_category} " .
            "{$division->gender} {$division->class_name} ({$approvedCount} peserta approved)? " .
            "Match lama akan dihapus."
        )) {
            $this->info('Dibatalkan.');
            return self::SUCCESS;
        }

        $result = $engine->generateForDivision($division);

        if ($result['matches_count'] > 0) {
            $this->info("✓ {$result['message']}");
            return self::SUCCESS;
        }

        $this->warn("✗ {$result['message']}");
        return self::FAILURE;
    }
}
