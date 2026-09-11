<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_tenant_administrators', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->unique();
            $table->string('name');
            $table->string('email');
            $table->text('password'); // encrypted at rest via the model cast
            $table->timestamps();

            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_tenant_administrators');
    }
};
