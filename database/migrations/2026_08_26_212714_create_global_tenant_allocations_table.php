<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_tenant_allocations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->string('tenant_id');

            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->cascadeOnDelete();

            $table->foreignId('global_season_id')
                ->constrained('global_seasons')
                ->cascadeOnDelete();

            $table->foreignId('global_commodity_id')
                ->constrained('global_commodities')
                ->cascadeOnDelete();

            $table->decimal('allocated_stock', 14, 2);

            $table->timestamps();

            $table->unique(
                [
                    'tenant_id',
                    'global_season_id',
                    'global_commodity_id',
                ],
                'tenant_season_commodity_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_tenant_allocations');
    }
};