<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('customer_phone')->nullable()->after('customer_email');
            $table->string('delivery_zone')->nullable()->after('shipping_address');
            $table->decimal('delivery_fee', 10, 2)->default(0)->after('delivery_zone');
            $table->string('payment_method')->default('pay_now')->after('delivery_fee');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn(['customer_phone', 'delivery_zone', 'delivery_fee', 'payment_method']);
        });
    }
};
