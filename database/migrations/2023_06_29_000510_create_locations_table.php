<?php

use App\Models\Location;
use App\Models\LocationType;
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
        Schema::create('locations', function (Blueprint $table) {
            $table->uuid('id');
            $table->foreignIdFor(Location::class, 'parent_id')->nullable()->constrained();
            $table->string('name');
            $table->json('aliases')->nullable();
            $table->string('type');
            $table->foreignIdFor(\App\Models\LocationSize::class); // metropolis, etc
            $table->text('description')->nullable();
            $table->string('demonym')->nullable();
            $table->integer('population')->nullable();
            $table->foreignIdFor(\App\Models\LocationGovernmentType::class)->nullable(); // democracy, plutocracy, etc
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
