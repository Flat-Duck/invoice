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
            $table->foreignId('company_id')->constrained()->restrictOnDelete();
            $table->string('invoice_number')->unique();
            $table->date('invoice_date')->index();
            $table->unsignedSmallInteger('invoice_year')->index();
            $table->unsignedTinyInteger('invoice_month')->index();
            $table->decimal('amount', 15, 2);
            $table->decimal('exchange_rate', 15, 6);
            $table->string('location')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['invoice_year', 'invoice_month']);
            $table->index(['company_id', 'invoice_year', 'invoice_month']);
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
