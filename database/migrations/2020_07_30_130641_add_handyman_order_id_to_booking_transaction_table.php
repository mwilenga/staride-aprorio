<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;

class AddHandymanOrderIdToBookingTransactionTable extends Migration
{
    public function up()
    {
        MigrationSchema::ensureForeign('booking_transactions', 'handyman_order_id', 'handyman_orders', 'RESTRICT', 'CASCADE');
    }

    public function down()
    {
        MigrationSchema::dropForeignIfExists('booking_transactions', 'handyman_order_id');
    }
}
