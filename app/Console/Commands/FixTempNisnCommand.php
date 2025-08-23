<?php

namespace App\Console\Commands;

use App\Models\Alumni;
use App\Models\Nisn;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixTempNisnCommand extends Command
{
    protected $signature = 'alumni:fix-temp-nisn {--interactive : Prompt for each TEMP entry} {--export= : Export a CSV to path before changing}';
    protected $description = 'Detect and fix alumni NISN values that still use TEMP placeholders';

    public function handle(): int
    {
        // Join via relation accessor; filter by related nisn number starting with TEMP
        $query = Alumni::query()->whereHas('nisnNumber', function($q){
            $q->where('number','LIKE','TEMP%');
        });
        $count = $query->count();
        if ($count === 0) {
            $this->info('No TEMP* NISN records found.');
            return self::SUCCESS;
        }

        $this->warn("Found {$count} alumni with TEMP NISN");

    $records = $query->with('nisnNumber')->get(['id','nama','email','nisn_id']);

        if ($path = $this->option('export')) {
            $this->exportCsv($path, $records);
            $this->info("Exported current mapping to {$path}");
        }

        $interactive = $this->option('interactive');
        $updated = 0; $skipped = 0;

        foreach ($records as $alumni) {
            $new = null;
            if ($interactive) {
                $this->line("Alumni #{$alumni->id} | {$alumni->nama} | current: ".($alumni->nisnNumber->number ?? '-'));
                $new = $this->ask('Enter real NISN (10 digits) or leave blank to skip');
                if ($new === null || trim($new) === '') {
                    $skipped++; continue;
                }
            } else {
                // Auto strategy: if email starts with digits 10 length treat as candidate
                $prefix = preg_replace('/[^0-9]/','', (string)$alumni->email);
                if (strlen($prefix) === 10) {
                    $new = $prefix;
                } else {
                    $this->line("Skip (no heuristic) ID {$alumni->id}");
                    $skipped++; continue;
                }
            }

            $new = trim($new);
            if (!preg_match('/^[0-9]{10}$/',$new)) {
                $this->error('Invalid NISN format, skipping.');
                $skipped++; continue;
            }
            $existingNisn = Nisn::where('number',$new)->first();
            if (!$existingNisn) {
                $existingNisn = Nisn::create(['number' => $new]);
            }
            if (Alumni::where('nisn_id',$existingNisn->id)->where('id','!=',$alumni->id)->exists()) {
                $this->error('Duplicate NISN already exists, skipping.');
                $skipped++; continue;
            }

            $alumni->nisn_id = $existingNisn->id;
            $alumni->save();
            $updated++;
        }

        $this->info("Updated: {$updated}, Skipped: {$skipped}");
    $remaining = Alumni::whereHas('nisnNumber', function($q){ $q->where('number','LIKE','TEMP%'); })->count();
        $this->info("Remaining TEMP*: {$remaining}");
        return self::SUCCESS;
    }

    private function exportCsv(string $path, $collection): void
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $fh = fopen($path,'w');
        fputcsv($fh, ['id','nama','email','nisn']);
        foreach ($collection as $row) {
            fputcsv($fh, [$row->id,$row->nama,$row->email,$row->nisn]);
        }
        fclose($fh);
    }
}
