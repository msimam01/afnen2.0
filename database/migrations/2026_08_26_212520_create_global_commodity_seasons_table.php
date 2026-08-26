<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_commodity_seasons', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('global_season_id')
                ->constrained('global_seasons')
                ->cascadeOnDelete();

            $table->foreignId('global_commodity_id')
                ->constrained('global_commodities')
                ->cascadeOnDelete();

            $table->decimal('stock', 14, 2);

            $table->timestamps();

            $table->unique(
                ['global_season_id', 'global_commodity_id'],
                'global_season_commodity_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_commodity_seasons');
    }
};