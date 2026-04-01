<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_schedules', function (Blueprint $table) {
            $table->integer('monthly_limit')->default(4)->after('monthly_price');
        });
    }

    public function down(): void
    {
        Schema::table('member_schedules', function (Blueprint $table) {
            $table->dropColumn('monthly_limit');
        });
    }
};