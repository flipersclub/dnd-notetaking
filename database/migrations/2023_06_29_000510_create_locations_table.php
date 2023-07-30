<?php

use App\Models\Compendium\Location\GovernmentType;
use App\Models\Compendium\Location\Location;
use App\Models\Compendium\Location\Size;
use App\Models\Compendium\Location\Type;
use App\Models\Setting;
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
            $table->foreignIdFor(Setting::class)->nullable();
            $table->foreignIdFor(Location::class, 'parent_id')->nullable();
            $table->string('name');
            $table->foreignIdFor(Type::class); // region, settlement, building
            $table->foreignIdFor(Size::class); // metropolis, etc
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
