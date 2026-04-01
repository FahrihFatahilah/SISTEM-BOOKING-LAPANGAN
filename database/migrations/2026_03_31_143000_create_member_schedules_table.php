<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('member_name');
            $table->string('member_phone');
            $table->foreignId('field_id')->constrained()->onDelete('cascade');
            $table->integer('day_of_week'); // 0=Minggu, 1=Senin, dst
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('monthly_price', 10, 2);
            $table->date('start_date'); // Mulai membership
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_schedules');
    }
};