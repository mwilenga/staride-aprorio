<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddColumnsForWalletAction extends Migration
{
    public function up()
    {
        foreach (['user_wallet_transactions', 'driver_wallet_transactions', 'business_segment_wallet_transactions'] as $table) {
            MigrationSchema::addColumnWithForeign(
                $table,
                'action_merchant_id',
                fn (Blueprint $table) => $table->unsignedInteger('action_merchant_id')->nullable(),
                'merchants'
            );
        }
    }

    public function down()
    {
        foreach (['user_wallet_transactions', 'driver_wallet_transactions', 'business_segment_wallet_transactions'] as $table) {
            MigrationSchema::dropForeignIfExists($table, 'action_merchant_id');
        }
    }
}
