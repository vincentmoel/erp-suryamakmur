<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('vendor_id')->constrained('vendors')->restrictOnDelete();
            $table->string('invoice_number')->nullable();
            $table->date('purchase_date');
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->unsignedBigInteger('tax_amount')->default(0);
            $table->unsignedBigInteger('grand_total')->default(0);
            $table->string('status');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->integer('quantity');
            $table->unsignedBigInteger('unit_price');
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->unsignedBigInteger('tax_amount')->default(0);
            $table->unsignedBigInteger('subtotal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_details');
        Schema::dropIfExists('purchases');
    }
};
