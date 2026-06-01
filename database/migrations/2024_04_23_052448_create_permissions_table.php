<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignid('role_id')->constrained('roles')->onDelete('cascade');
            $table->string('module');
            $table->boolean('menu')->default(false);
            $table->boolean('create')->default(false);
            $table->boolean('read')->default(false);
            $table->boolean('update')->default(false);
            $table->boolean('delete')->default(false);
            $table->boolean('restore')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
