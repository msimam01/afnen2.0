<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_seasons', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->string('name');

            $table->enum('type', [
                'dry',
                'wet',
            ]);

            $table->enum('loan_type', [
                'co-funded',
                'complete-loan',
            ])->default('co-funded');

            $table->date('start_date');
            $table->date('end_date');

            $table->date('collection_start_date')->nullable();
            $table->date('collection_end_date')->nullable();

            $table->decimal('budget', 14, 2)->nullable();

            $table->enum('status', [
                'open',
                'closed',
            ])->default('open');

            $table->date('return_deadline')->nullable();

            $table->decimal('insurance_rate', 5, 2)->default(0);

            $table->unsignedInteger('send_reminder_after_days')
                ->default(7);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_seasons');
    }
};