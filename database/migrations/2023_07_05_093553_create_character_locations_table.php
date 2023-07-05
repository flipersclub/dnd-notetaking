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
        Schema::create('character_locations', function (Blueprint $table) {
            $table->uuid('id');
            $table->foreignIdFor(\App\Models\Location::class);
            $table->uuidMorphs('locatable');
            $table->string('type')->nullable(); // ruler, owner, leader, staff
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_locations');
    }
};
