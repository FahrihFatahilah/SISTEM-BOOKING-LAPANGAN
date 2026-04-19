<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_schedules', function (Blueprint $table) {
            $table->string('member_phone')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('member_schedules', function (Blueprint $table) {
            $table->string('member_phone')->nullable(false)->change();
        });
    }
};
