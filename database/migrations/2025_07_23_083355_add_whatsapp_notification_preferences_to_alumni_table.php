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
        Schema::table('alumnis', function (Blueprint $table) {
            // Add WhatsApp notification preferences (after no_tlp column)
            $table->boolean('whatsapp_job_notifications')->default(true)->after('no_tlp');
            $table->boolean('whatsapp_news_notifications')->default(true)->after('whatsapp_job_notifications');
            $table->boolean('whatsapp_status_notifications')->default(true)->after('whatsapp_news_notifications');
            $table->string('whatsapp_number')->nullable()->after('whatsapp_status_notifications');
            
            // Add index for phone number for better performance
            $table->index('no_tlp');
            $table->index('whatsapp_number');
        });

        Schema::table('companies', function (Blueprint $table) {
            // Add WhatsApp notification preferences for companies
            $table->boolean('whatsapp_application_notifications')->default(true)->after('contact_person_phone');
            
            // Add index for better performance
            $table->index('contact_person_phone');
            $table->index('phone');
        });

        // Check if admins table exists before modifying
        if (Schema::hasTable('admins')) {
            Schema::table('admins', function (Blueprint $table) {
                // Add WhatsApp number for admin if not exists (after username column)
                if (!Schema::hasColumn('admins', 'whatsapp_number')) {
                    $table->string('whatsapp_number')->nullable()->after('username');
                }
                
                // Add notification preferences
                if (!Schema::hasColumn('admins', 'whatsapp_company_registration_notifications')) {
                    $table->boolean('whatsapp_company_registration_notifications')->default(true)->after('whatsapp_number');
                }
                if (!Schema::hasColumn('admins', 'whatsapp_job_posting_notifications')) {
                    $table->boolean('whatsapp_job_posting_notifications')->default(true)->after('whatsapp_company_registration_notifications');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumnis', function (Blueprint $table) {
            $table->dropIndex(['no_tlp']);
            $table->dropIndex(['whatsapp_number']);
            $table->dropColumn([
                'whatsapp_job_notifications',
                'whatsapp_news_notifications', 
                'whatsapp_status_notifications',
                'whatsapp_number'
            ]);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropIndex(['contact_person_phone']);
            $table->dropIndex(['phone']);
            $table->dropColumn('whatsapp_application_notifications');
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
    }
};
