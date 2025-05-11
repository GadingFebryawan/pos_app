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
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->default(0);
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->default(0);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->default(0);
        });
        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->default(0);
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['company_id']);
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['company_id']);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['company_id']);
        });
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['company_id']);
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['company_id']);
        });
    }
};
