<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('global_commodity_market_prices', function (Blueprint $table) {
        $table->id();
        $table->uuid('uuid')->unique();

        $table->foreignId('global_commodity_id')
            ->constrained('global_commodities')
            ->cascadeOnDelete();

        $table->foreignId('global_season_id')
            ->nullable()
            ->constrained('global_seasons')
            ->nullOnDelete();

    $table->enum('scope', [
        'national',
        'zone',
        'state',
        'lga',
        'market',
    ])->default('national');

    $table->string('zone')->nullable();
    $table->string('state')->nullable();
    $table->string('lga')->nullable();
    $table->string('market')->nullable();

    $table->decimal('price', 14, 2);

    $table->date('effective_date');

    $table->text('notes')->nullable();

    $table->timestamps();

    $table->index(
    ['global_commodity_id', 'global_season_id'],
    'gcmp_commodity_season_idx'
);
});
    }

    public function down(): void
    {
        Schema::dropIfExists('global_commodity_market_prices');
    }
};