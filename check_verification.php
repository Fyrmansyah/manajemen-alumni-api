<?php

require 'vendor/autoload.php';
require 'bootstrap/app.php';

use App\Models\Company;

$companies = Company::select('id', 'company_name', 'status', 'is_verified', 'is_approved', 'verified_at')->get();

echo "Company verification status check:\n";
echo "==================================\n";

foreach ($companies as $company) {
    echo "ID: {$company->id}\n";
    echo "Name: {$company->company_name}\n";
    echo "Status: {$company->status}\n";
    echo "is_verified: " . ($company->is_verified ? 'true' : 'false') . "\n";
    echo "is_approved: " . ($company->is_approved ? 'true' : 'false') . "\n";
    echo "verified_at: {$company->verified_at}\n";
    echo "Badge logic result: " . (($company->is_verified ?? false) || ($company->is_approved ?? false) ? 'Terverifikasi' : 'Menunggu') . "\n";
    echo "---\n";
}
