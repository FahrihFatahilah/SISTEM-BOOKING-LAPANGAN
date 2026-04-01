<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained()->onDelete('cascade');
            $table->json('days_of_week'); // [1,2,3,4,5] untuk Senin-Jumat
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('price_per_hour', 10, 2);
            $table->string('rule_name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(1); // Prioritas jika ada overlap
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pricing_rules');
    }
};