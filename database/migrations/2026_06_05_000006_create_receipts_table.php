<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->date('receipt_date');
            $table->string('payment_method');
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->string('image')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('receipt_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_id')->constrained('receipts')->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained('invoices')->restrictOnDelete();
            $table->unsignedBigInteger('amount');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipt_details');
        Schema::dropIfExists('receipts');
    }
};
