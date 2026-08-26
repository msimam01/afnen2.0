<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_tenant_allocation_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('global_tenant_allocation_id')
                ->constrained('global_tenant_allocations')
                ->cascadeOnDelete();

            $table->enum('type', [
                'allocation',
                'consumption',
                'adjustment',
                'reversal',
                'transfer',
            ]);

            $table->decimal('quantity', 14, 2);

            $table->string('reference_type')->nullable();
            $table->string('reference_id')->nullable();

            $table->text('note')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'reference_type',
                'reference_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_tenant_allocation_transactions');
    }
};