<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStripeconnectToDriverAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_accounts', function (Blueprint $table) {
            $columns = [
                'instant_settlement' => function (Blueprint $table) {
                    $table->unsignedTinyInteger('instant_settlement')->default(0)->comment('0-no, 1-yes');
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('driver_accounts', $column)) {
                    $callback($table);
                }
            }
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_accounts', function (Blueprint $table) {
            $columns = [
                'instant_settlement',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('driver_accounts', $column)) {
                    $table->dropColumn($column);
                }
            }
});
    }
}
