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
        Schema::create('location_language', function (Blueprint $table) {
            $table->uuid('id');
            $table->foreignIdFor(\App\Models\Location::class);
            $table->foreignIdFor(\App\Models\Language::class);
            $table->string('type'); // main, secondary
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_language');
    }
};
