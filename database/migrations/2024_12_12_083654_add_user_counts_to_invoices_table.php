<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->integer('total_users')->default(0)->after('total_amount');
            $table->integer('active_users')->default(0)->after('total_users');
            $table->integer('registered_users')->default(0)->after('active_users');
            $table->integer('appointment_users')->default(0)->after('registered_users');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'total_users',
                'active_users',
                'registered_users',
                'appointment_users',
            ]);
        });
    }
};
