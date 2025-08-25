<?php

namespace Database\Seeders;

use App\Models\Nisn;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class NisnBulkSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/nisns.csv');
        if (!file_exists($path)) {
            $this->command?->warn('nisns.csv not found, skipping NisnBulkSeeder');
            return;
        }

        $handle = fopen($path, 'r');
        if (!$handle) {
            $this->command?->error('Cannot open nisns.csv');
            return;
        }

        $batch = [];
        $now = now();
        $count = 0; $skipped = 0;
        while (($line = fgetcsv($handle)) !== false) {
            if (!isset($line[0])) continue;
            $raw = trim($line[0]);
            if ($raw === '' || !preg_match('/^\d{5,}$/', $raw)) { $skipped++; continue; }
            $batch[] = [
                'number' => $raw,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            if (count($batch) === 1000) {
                $this->flush($batch, $count, $skipped);
                $batch = [];
            }
        }
        fclose($handle);
        if ($batch) {
            $this->flush($batch, $count, $skipped);
        }
        $this->command?->info("NISN import done: total inserted/updated={$count}, skipped_format={$skipped}");
    }

    private function flush(array $rows, int & $count, int $skipped): void
    {
        // upsert by number
        Nisn::upsert($rows, ['number'], ['updated_at']);
        $count += count($rows);
    }
}
