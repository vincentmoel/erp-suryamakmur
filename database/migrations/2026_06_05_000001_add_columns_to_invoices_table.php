<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->json('customer_snapshot')->nullable()->after('customer_id');
            $table->unsignedBigInteger('subtotal')->default(0)->after('due_date');
            $table->decimal('tax_percent', 5, 2)->default(0)->after('subtotal');
            $table->unsignedBigInteger('grand_total')->default(0)->after('tax_amount');
            $table->text('notes')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['customer_snapshot', 'subtotal', 'tax_percent', 'grand_total', 'notes']);
        });
    }
};
