<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;

class AddOrderIdColumnsToBookingTransactionsTable extends Migration
{
    public function up()
    {
        MigrationSchema::ensureForeign('booking_transactions', 'order_id', 'orders', 'RESTRICT', 'CASCADE');
        MigrationSchema::ensureForeign('booking_transactions', 'handyman_order_id', 'handyman_orders', 'RESTRICT', 'CASCADE');
        MigrationSchema::ensureForeign('booking_transactions', 'merchant_id', 'merchants', 'RESTRICT', 'CASCADE');
    }

    public function down()
    {
        foreach (['order_id', 'handyman_order_id', 'merchant_id'] as $column) {
            MigrationSchema::dropForeignIfExists('booking_transactions', $column);
        }
    }
}
