<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_detail_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_detail_id')->constrained('invoice_details')->cascadeOnDelete();
            $table->foreignId('inventory_detail_id')->constrained('inventory_details')->restrictOnDelete();
            $table->integer('quantity');
            $table->unsignedBigInteger('unit_cost');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_detail_batches');
    }
};
