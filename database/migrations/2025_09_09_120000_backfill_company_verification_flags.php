    <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Mark verified for previously approved/active companies
        DB::table('companies')
            ->where(function($q){
                $q->where('is_approved', true)
                  ->orWhere('status', 'aktif')
                  ->orWhere('status', 'active');
            })
            ->where(function($q){
                $q->whereNull('is_verified')->orWhere('is_verified', false);
            })
            ->update([
                'is_verified' => true,
                'verified_at' => now(),
            ]);
    }

    public function down(): void
    {
        // No-op: we won't unverify data on rollback
    }
};
