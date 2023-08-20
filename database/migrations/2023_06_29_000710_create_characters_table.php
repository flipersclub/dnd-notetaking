<?php

use App\Models\Compendium\Compendium;
use App\Models\Compendium\Species;
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
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->nullable()->index();
            $table->foreignIdFor(Compendium::class)->constrained();
            $table->string('name');
            $table->smallInteger('age')->nullable();
            $table->string('gender')->nullable();
            $table->foreignIdFor(Species::class)->nullable()->constrained();
            $table->text('content')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
