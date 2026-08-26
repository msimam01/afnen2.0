<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farmers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->string('registration_number')->unique();
            $table->string('full_name');
            $table->string('phone')->unique();

            $table->string('nin')->unique();
            $table->string('bvn')->unique();

            $table->string('state');
            $table->string('lga');
            $table->text('address');

            $table->string('cluster')->nullable();
            $table->string('username')->unique()->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farmers');
    }
};