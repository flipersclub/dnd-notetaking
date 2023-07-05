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
        Schema::create('location_species', function (Blueprint $table) {
            $table->uuid('id');
            $table->foreignIdFor(\App\Models\Location::class);
            $table->foreignIdFor(\App\Models\Species::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_species');
    }
};
