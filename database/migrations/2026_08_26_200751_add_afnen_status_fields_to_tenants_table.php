<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('provisioning_status')
                ->default('pending')
                ->after('data');

            $table->string('status')
                ->default('inactive')
                ->after('provisioning_status');

            $table->timestamp('activated_at')
                ->nullable()
                ->after('status');

            $table->timestamp('deactivated_at')
                ->nullable()
                ->after('activated_at');

            $table->text('deactivation_reason')
                ->nullable()
                ->after('deactivated_at');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'provisioning_status',
                'status',
                'activated_at',
                'deactivated_at',
                'deactivation_reason',
            ]);
        });
    }
};