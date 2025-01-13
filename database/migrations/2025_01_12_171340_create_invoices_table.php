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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('government_id');
            $table->string('email');
            $table->decimal('debt_amount', 10, 2);
            $table->date('debt_due_date');
            $table->uuid('debt_id')->unique();
            $table->boolean('processed')->default(false);
            $table->timestamps();
            $table->unique(['government_id', 'debt_id'], 'unique_invoice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
