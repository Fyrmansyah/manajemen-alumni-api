<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use App\Models\Alumni;

class CheckAlumniColumns extends Command
{
    protected $signature = 'check:alumni-columns';
    protected $description = 'Check alumni table columns';

    public function handle()
    {
        $columns = Schema::getColumnListing('alumnis');
        
        $this->info("Alumni table columns:");
        $this->info("====================");
        
        foreach ($columns as $column) {
            $this->line($column);
        }
        
        $this->info("\nChecking for status_kerja column:");
        $hasStatusKerja = in_array('status_kerja', $columns);
        $this->line("status_kerja exists: " . ($hasStatusKerja ? 'YES' : 'NO'));
        
        // Check a sample alumni record
        $alumni = Alumni::first();
        if ($alumni) {
            $this->info("\nSample alumni data:");
            $this->line("ID: {$alumni->id}");
            $this->line("Nama: {$alumni->nama_lengkap}");
            $statusKerja = $alumni->status_kerja ?? 'NULL';
            $this->line("status_kerja: {$statusKerja}");
        }

        return 0;
    }
}
