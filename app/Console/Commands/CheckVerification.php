<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;

class CheckVerification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:verification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check company verification status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $companies = Company::select('id', 'company_name', 'status', 'is_verified', 'is_approved', 'verified_at')->get();

        $this->info("Company verification status check:");
        $this->info("==================================");

        foreach ($companies as $company) {
            $this->line("ID: {$company->id}");
            $this->line("Name: {$company->company_name}");
            $this->line("Status: {$company->status}");
            $this->line("is_verified: " . ($company->is_verified ? 'true' : 'false'));
            $this->line("is_approved: " . ($company->is_approved ? 'true' : 'false'));
            $this->line("verified_at: {$company->verified_at}");
            $badgeResult = (($company->is_verified ?? false) || ($company->is_approved ?? false)) ? 'Terverifikasi' : 'Menunggu';
            $this->line("Badge logic result: {$badgeResult}");
            $this->line("---");
        }

        return 0;
    }
}
