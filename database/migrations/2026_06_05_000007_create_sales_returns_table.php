<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('invoice_id')->constrained('invoices')->restrictOnDelete();
            $table->date('return_date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sales_return_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_return_id')->constrained('sales_returns')->cascadeOnDelete();
            $table->foreignId('invoice_detail_batch_id')->constrained('invoice_detail_batches')->restrictOnDelete();
            $table->integer('quantity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_return_details');
        Schema::dropIfExists('sales_returns');
    }
};
