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
        Schema::create('location_natural_resource', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Compendium\Location\Location::class);
            $table->foreignIdFor(\App\Models\Compendium\NaturalResource::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_natural_resource');
    }
};
