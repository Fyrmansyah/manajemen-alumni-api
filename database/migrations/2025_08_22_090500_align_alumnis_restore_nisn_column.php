<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Re-introduce direct `nisn` column on `alumnis` table for authentication.
     * If the table currently uses `nisn_id` FK -> `nisns.number`, we copy the data,
     * add a unique index, then drop the FK/column to simplify the model.
     */
    public function up(): void
    {
        // 1. Add nisn column if missing (nullable first to allow backfill safely)
        if (!Schema::hasColumn('alumnis', 'nisn')) {
            Schema::table('alumnis', function (Blueprint $table) {
                $table->string('nisn', 20)->nullable()->after('id');
            });
        }

        // 2. If nisn_id exists, backfill nisn values from related table
        if (Schema::hasColumn('alumnis', 'nisn_id')) {
            // Perform a bulk update using a join (DB specific). Fallback to per-row if join unsupported.
            $driver = DB::getDriverName();
            if (in_array($driver, ['mysql', 'mariadb'])) {
                DB::statement('UPDATE alumnis a JOIN nisns n ON a.nisn_id = n.id SET a.nisn = n.number WHERE a.nisn IS NULL');
            } else {
                // Generic (less efficient) fallback
                $rows = DB::table('alumnis')->whereNull('nisn')->whereNotNull('nisn_id')->get(['id','nisn_id']);
                foreach ($rows as $row) {
                    $number = DB::table('nisns')->where('id', $row->nisn_id)->value('number');
                    if ($number) {
                        DB::table('alumnis')->where('id', $row->id)->update(['nisn' => $number]);
                    }
                }
            }
        }

        // 3. Ensure no null/empty duplicates before adding unique index
        //    Remove any duplicate rows with same nisn keeping the earliest id (business rule: unique NISN)
        $duplicates = DB::table('alumnis')
            ->select('nisn')
            ->whereNotNull('nisn')
            ->groupBy('nisn')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('nisn');
        foreach ($duplicates as $dup) {
            $ids = DB::table('alumnis')->where('nisn', $dup)->orderBy('id')->pluck('id');
            $keep = $ids->shift();
            if ($ids->isNotEmpty()) {
                DB::table('alumnis')->whereIn('id', $ids)->update(['nisn' => null]);
            }
        }

        // 4. Add unique index if not already present (simple detection by try/catch create)
        //    We can't easily introspect indexes portable; attempt inside conditional.
        // Add unique index only if it doesn't already exist
        try {
            $connection = Schema::getConnection();
            $sm = $connection->getDoctrineSchemaManager();
            $indexes = $sm->introspectTable('alumnis')->getIndexes();
            $hasUnique = false;
            foreach ($indexes as $idx) {
                if ($idx->isUnique() && $idx->getColumns() === ['nisn']) {
                    $hasUnique = true; break;
                }
            }
            if (!$hasUnique) {
                Schema::table('alumnis', function (Blueprint $table) { $table->unique('nisn'); });
            }
        } catch (Throwable $e) {
            // Fallback: attempt and ignore failure
            try { Schema::table('alumnis', function (Blueprint $table) { $table->unique('nisn'); }); } catch (Throwable $e2) { /* ignore */ }
        }

        // 5. Drop nisn_id if present (FK + column) after backfill
        if (Schema::hasColumn('alumnis', 'nisn_id')) {
            // Determine existing foreign keys referencing nisn_id
            try {
                $connection = Schema::getConnection();
                $sm = $connection->getDoctrineSchemaManager();
                $doctrineTable = $sm->introspectTable('alumnis');
                foreach ($doctrineTable->getForeignKeys() as $fk) {
                    if (in_array('nisn_id', $fk->getLocalColumns(), true)) {
                        Schema::table('alumnis', function (Blueprint $table) use ($fk) {
                            try { $table->dropForeign($fk->getName()); } catch (Throwable $e) { /* ignore */ }
                        });
                    }
                }
            } catch (Throwable $e) {
                // Fallback attempt standard name
                try { Schema::table('alumnis', function (Blueprint $table) { $table->dropForeign(['nisn_id']); }); } catch (Throwable $e2) { /* ignore */ }
            }
            // Now drop column
            Schema::table('alumnis', function (Blueprint $table) {
                try { $table->dropColumn('nisn_id'); } catch (Throwable $e) { /* ignore */ }
            });
        }

        // 6. Finally make sure no remaining nulls (set to placeholder if any) - better to enforce manual fix
        DB::table('alumnis')->whereNull('nisn')->update(['nisn' => DB::raw('CONCAT("TEMP", id)')]);
    }

    public function down(): void
    {
        // Recreate nisn_id (nullable) and drop nisn to revert (data in nisn lost unless re-seeded)
        if (!Schema::hasColumn('alumnis', 'nisn_id')) {
            Schema::table('alumnis', function (Blueprint $table) {
                $table->unsignedBigInteger('nisn_id')->nullable()->after('id');
            });
        }

        // Attempt to map existing nisn back into nisns table (create if missing)
        if (Schema::hasTable('nisns')) {
            $alumnis = DB::table('alumnis')->select('id','nisn','nisn_id')->get();
            foreach ($alumnis as $al) {
                if ($al->nisn && !$al->nisn_id) {
                    $nisnId = DB::table('nisns')->where('number', $al->nisn)->value('id');
                    if (!$nisnId) {
                        $nisnId = DB::table('nisns')->insertGetId([
                            'number' => $al->nisn,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    DB::table('alumnis')->where('id', $al->id)->update(['nisn_id' => $nisnId]);
                }
            }
        }

        // Drop unique index + column nisn
        if (Schema::hasColumn('alumnis', 'nisn')) {
            Schema::table('alumnis', function (Blueprint $table) {
                try { $table->dropUnique(['alumnis_nisn_unique']); } catch (Throwable $e) { /* ignore */ }
                $table->dropColumn('nisn');
            });
        }
    }
};
