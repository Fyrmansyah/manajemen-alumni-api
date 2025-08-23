<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // If direct nisn column exists, move its values into nisns table then drop and add nisn_id
        if (Schema::hasColumn('alumnis', 'nisn') && !Schema::hasColumn('alumnis', 'nisn_id')) {
            Schema::table('alumnis', function (Blueprint $table) {
                $table->foreignId('nisn_id')->nullable()->after('id')->constrained('nisns')->nullOnDelete();
            });

            // Migrate existing values
            $alumnis = DB::table('alumnis')->select('id','nisn')->whereNotNull('nisn')->get();
            foreach ($alumnis as $row) {
                $nisnId = DB::table('nisns')->where('number', $row->nisn)->value('id');
                if (!$nisnId) {
                    $nisnId = DB::table('nisns')->insertGetId([
                        'number' => $row->nisn,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                DB::table('alumnis')->where('id', $row->id)->update(['nisn_id' => $nisnId]);
            }

            // Finally drop old column
            Schema::table('alumnis', function (Blueprint $table) {
                $table->dropColumn('nisn');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('alumnis', 'nisn') && Schema::hasColumn('alumnis', 'nisn_id')) {
            Schema::table('alumnis', function (Blueprint $table) {
                $table->string('nisn', 20)->nullable()->after('id');
            });

            $alumnis = DB::table('alumnis')->select('id','nisn_id')->whereNotNull('nisn_id')->get();
            foreach ($alumnis as $row) {
                $number = DB::table('nisns')->where('id', $row->nisn_id)->value('number');
                if ($number) {
                    DB::table('alumnis')->where('id', $row->id)->update(['nisn' => $number]);
                }
            }

            Schema::table('alumnis', function (Blueprint $table) {
                $table->dropForeign(['nisn_id']);
                $table->dropColumn('nisn_id');
            });
        }
    }
};
