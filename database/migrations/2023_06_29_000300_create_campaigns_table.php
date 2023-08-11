<?php

use App\Enums\CampaignVisibility;
use App\Models\Compendium\Compendium;
use App\Models\System;
use App\Models\User;
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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->nullable()->index();
            $table->string('name');
            $table->foreignIdFor(User::class, 'game_master_id');
            $table->text('content')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('level')->nullable();
            $table->foreignIdFor(System::class)->nullable();
            $table->foreignIdFor(Compendium::class)->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedTinyInteger('visibility')->default(CampaignVisibility::private->value);
            $table->integer('player_limit')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
