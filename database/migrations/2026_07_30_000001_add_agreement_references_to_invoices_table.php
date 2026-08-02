<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('work_order')->nullable()->after('invoice_number');
            $table->string('service_agreement')->nullable()->after('work_order');
            $table->string('to_reference')->nullable()->after('service_agreement');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['work_order', 'service_agreement', 'to_reference']);
        });
    }
};
