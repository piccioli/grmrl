<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->string('difficulty')->nullable()->after('longitude');
            $table->string('elevation_gain')->nullable()->after('difficulty');
            $table->string('trail_length')->nullable()->after('elevation_gain');
            $table->string('water_description')->nullable()->after('trail_length');
            $table->text('itinerary_description')->nullable()->after('water_description');
            $table->string('image_url')->nullable()->after('itinerary_description');
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['difficulty', 'elevation_gain', 'trail_length', 'water_description', 'itinerary_description', 'image_url']);
        });
    }
};
