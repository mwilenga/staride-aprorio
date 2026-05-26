<?php

use App\Support\MigrationSchema;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderIdColumnInWalletTransactionsTable extends Migration
{
    public function up()
    {
        MigrationSchema::addColumnWithForeign(
            'user_wallet_transactions',
            'order_id',
            fn (Blueprint $table) => $table->unsignedInteger('order_id')->nullable(),
            'orders'
        );

        MigrationSchema::addColumnWithForeign(
            'user_wallet_transactions',
            'handyman_order_id',
            fn (Blueprint $table) => $table->unsignedInteger('handyman_order_id')->nullable(),
            'handyman_orders'
        );
    }

    public function down()
    {
        if (!Schema::hasTable('user_wallet_transactions')) {
            return;
        }

        foreach (['order_id', 'handyman_order_id'] as $column) {
            MigrationSchema::dropForeignIfExists('user_wallet_transactions', $column);
            if (Schema::hasColumn('user_wallet_transactions', $column)) {
                Schema::table('user_wallet_transactions', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
}
