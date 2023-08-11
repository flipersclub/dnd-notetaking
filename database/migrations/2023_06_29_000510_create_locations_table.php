<?php

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Location\GovernmentType;
use App\Models\Compendium\Location\Location;
use App\Models\Compendium\Location\Type;
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
            $table->id();
            $table->foreignIdFor(Compendium::class);
            $table->foreignIdFor(Location::class, 'parent_id')->nullable();
            $table->string('name');
            $table->foreignIdFor(Type::class); // region, village, town, city, building
            $table->text('content')->nullable();
            $table->string('demonym')->nullable();
            $table->integer('population')->nullable();
            $table->foreignIdFor(GovernmentType::class)->nullable(); // democracy, plutocracy, etc
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
