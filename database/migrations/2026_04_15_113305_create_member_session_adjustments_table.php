<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_session_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_schedule_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['add', 'remove']);
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
            $table->string('reason');
            $table->foreignId('adjusted_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_session_adjustments');
    }
};
