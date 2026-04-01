<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop foreign key constraint dulu
        Schema::table('user_memberships', function (Blueprint $table) {
            $table->dropForeign(['membership_package_id']);
        });
        
        // Hapus kolom yang tidak diperlukan dan tambah kolom baru
        Schema::table('user_memberships', function (Blueprint $table) {
            $table->dropColumn(['membership_package_id', 'weekly_schedule', 'end_date']);
            $table->integer('day_of_week'); // 0=Minggu, 1=Senin, dst
            $table->decimal('session_duration_hours', 3, 1)->default(1.5);
            $table->decimal('monthly_price', 10, 2);
        });
    }

    public function down()
    {
        Schema::table('user_memberships', function (Blueprint $table) {
            $table->dropColumn(['day_of_week', 'session_duration_hours', 'monthly_price']);
            $table->foreignId('membership_package_id')->constrained()->onDelete('cascade');
            $table->json('weekly_schedule');
            $table->date('end_date');
        });
    }
};