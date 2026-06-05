<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('salesperson_id')->constrained('users')->restrictOnDelete();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->unsignedBigInteger('discount_amount')->nullable();
            $table->unsignedBigInteger('tax_amount')->nullable();
            $table->unsignedBigInteger('paid_amount')->default(0);
            $table->string('status');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->integer('quantity');
            $table->unsignedBigInteger('unit_price');
            $table->unsignedBigInteger('discount_amount')->nullable();
            $table->unsignedBigInteger('tax_amount')->nullable();
            $table->unsignedBigInteger('amount');
            $table->json('product_snapshot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_details');
        Schema::dropIfExists('invoices');
    }
};
