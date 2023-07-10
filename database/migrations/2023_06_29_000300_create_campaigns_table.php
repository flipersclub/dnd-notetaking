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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->nullable()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignIdFor(\App\Models\User::class, 'game_master_id')->nullable();
            $table->integer('level')->nullable();
            $table->foreignIdFor(\App\Models\System::class)->nullable();
            $table->foreignIdFor(\App\Models\Setting::class)->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedTinyInteger('visibility')->default(\App\Enums\CampaignVisibility::private->value);
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
