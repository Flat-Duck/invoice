<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('administrations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('administration_id')->nullable()->after('company_id')->constrained()->restrictOnDelete();
            $table->date('received_date')->nullable()->after('invoice_date');
            $table->date('financial_return_date')->nullable()->after('received_date');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('administration_id');
            $table->dropColumn(['received_date', 'financial_return_date']);
        });

        Schema::dropIfExists('administrations');
    }
};
