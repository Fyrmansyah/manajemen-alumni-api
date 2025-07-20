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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->text('address');
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('jurusans')->nullOnDelete();
            $table->year('established_year')->nullable();
            $table->string('company_size')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_person_phone')->nullable();
            $table->string('logo')->nullable();
            $table->string('password');
            $table->enum('status', ['pending', 'aktif', 'inactive'])->default('pending');
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
