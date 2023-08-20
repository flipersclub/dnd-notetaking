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
            $table->string('slug')->nullable()->index();
            $table->foreignIdFor(Compendium::class)->constrained();
            $table->foreignIdFor(Location::class, 'parent_id')->nullable()->constrained('locations');
            $table->string('name');
            $table->foreignIdFor(Type::class)->constrained('location_types'); // region, village, town, city, building
            $table->text('content')->nullable();
            $table->string('demonym')->nullable();
            $table->integer('population')->nullable();
            $table->foreignIdFor(GovernmentType::class)->nullable()->constrained('location_government_types'); // democracy, plutocracy, etc
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
