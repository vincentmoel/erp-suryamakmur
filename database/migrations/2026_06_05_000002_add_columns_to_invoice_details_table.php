<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_details', function (Blueprint $table) {
            $table->decimal('tax_percent', 5, 2)->default(0)->after('discount_amount');
            $table->unsignedBigInteger('subtotal')->default(0)->after('tax_amount');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_details', function (Blueprint $table) {
            $table->dropColumn(['tax_percent', 'subtotal']);
        });
    }
};
