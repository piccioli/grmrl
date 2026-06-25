<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->date('birth_date');
            $table->boolean('is_cai_member')->default(false);
            $table->foreignId('cai_section_id')->nullable()->constrained('cai_sections')->nullOnDelete();
            $table->string('fiscal_code')->nullable();
            $table->foreignId('activity_id')->constrained('activities');
            $table->boolean('privacy_accepted');
            $table->boolean('photo_release_accepted');
            $table->boolean('rules_accepted');
            $table->boolean('weather_cancellation_accepted');
            $table->boolean('equipment_check_accepted');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
