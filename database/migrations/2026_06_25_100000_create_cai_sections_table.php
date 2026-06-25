<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cai_sections', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->string('region')->nullable();
            $table->string('province')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cai_sections');
    }
};
