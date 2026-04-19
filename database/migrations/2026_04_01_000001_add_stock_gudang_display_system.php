<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // customer_phone nullable
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('customer_phone')->nullable()->change();
        });

        // Tambah kolom stock gudang & display di products
        Schema::table('products', function (Blueprint $table) {
            $table->integer('warehouse_stock')->default(0)->after('stock');
            $table->integer('display_stock')->default(0)->after('warehouse_stock');
        });

        // Tabel transfer barang gudang -> display
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->date('transfer_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['warehouse_stock', 'display_stock']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('customer_phone')->nullable(false)->change();
        });
    }
};
