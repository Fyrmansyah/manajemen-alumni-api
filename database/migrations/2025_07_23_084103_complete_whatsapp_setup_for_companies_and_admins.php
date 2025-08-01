<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Complete WhatsApp setup for companies table
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'whatsapp_application_notifications')) {
                $table->boolean('whatsapp_application_notifications')->default(true)->after('contact_person_phone');
            }
        });

        // Complete WhatsApp setup for admins table  
        if (Schema::hasTable('admins')) {
            Schema::table('admins', function (Blueprint $table) {
                if (!Schema::hasColumn('admins', 'whatsapp_number')) {
                    $table->string('whatsapp_number')->nullable()->after('username');
                }
                
                if (!Schema::hasColumn('admins', 'whatsapp_company_registration_notifications')) {
                    $table->boolean('whatsapp_company_registration_notifications')->default(true)->after('whatsapp_number');
                }
                
                if (!Schema::hasColumn('admins', 'whatsapp_job_posting_notifications')) {
                    $table->boolean('whatsapp_job_posting_notifications')->default(true)->after('whatsapp_company_registration_notifications');
                }
            });
        }

        // Add WhatsApp notifications field to alumnis table if not exists
        Schema::table('alumnis', function (Blueprint $table) {
            if (!Schema::hasColumn('alumnis', 'whatsapp_notifications')) {
                $table->boolean('whatsapp_notifications')->default(false)->after('alamat');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'whatsapp_application_notifications')) {
                $table->dropColumn('whatsapp_application_notifications');
            }
        });

        if (Schema::hasTable('admins')) {
            Schema::table('admins', function (Blueprint $table) {
                if (Schema::hasColumn('admins', 'whatsapp_number')) {
                    $table->dropColumn('whatsapp_number');
                }
                if (Schema::hasColumn('admins', 'whatsapp_company_registration_notifications')) {
                    $table->dropColumn('whatsapp_company_registration_notifications');
                }
                if (Schema::hasColumn('admins', 'whatsapp_job_posting_notifications')) {
                    $table->dropColumn('whatsapp_job_posting_notifications');
                }
            });
        }

        Schema::table('alumnis', function (Blueprint $table) {
            if (Schema::hasColumn('alumnis', 'whatsapp_notifications')) {
                $table->dropColumn('whatsapp_notifications');
            }
        });
    }


};
