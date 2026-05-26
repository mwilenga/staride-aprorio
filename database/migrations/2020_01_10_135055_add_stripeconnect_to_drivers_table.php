<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStripeconnectToDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $columns = [
                'sc_address_status' => function (Blueprint $table) {
                    $table->string('sc_address_status' , 50)->nullable()->comment('pending, rejected, verified');
                },
                'sc_identity_photo' => function (Blueprint $table) {
                    $table->string('sc_identity_photo')->nullable()->comment('stripe connect identity photo');
                },
                'sc_identity_photo_status' => function (Blueprint $table) {
                    $table->string('sc_identity_photo_status' , 10)->nullable()->comment('pending , verified, rejected');
                },
                'routing_number' => function (Blueprint $table) {
                    $table->string('routing_number')->nullable()->after('account_number');
                },
                'ssn' => function (Blueprint $table) {
                    $table->string('ssn')->nullable();
                },
                'device_ip' => function (Blueprint $table) {
                    $table->string('device_ip')->nullable()->comment('ip while registering');
                },
                'sc_account_id' => function (Blueprint $table) {
                    $table->string('sc_account_id')->nullable()->comment('stripe connect account id');
                },
                'sc_account_status' => function (Blueprint $table) {
                    $table->string('sc_account_status')->nullable()->comment('active, rejected, pending');
                },
                'bsb_number' => function (Blueprint $table) {
                    $table->string('bsb_number')->nullable();
                },
                'abn_number' => function (Blueprint $table) {
                    $table->string('abn_number')->nullable();
                },
                'paystack_account_id' => function (Blueprint $table) {
                    $table->string('paystack_account_id')->nullable()->comment('Paystack split account id');
                },
                'paystack_account_status' => function (Blueprint $table) {
                    $table->string('paystack_account_status')->nullable()->comment('active, rejected, pending');
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('drivers', $column)) {
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
        Schema::table('drivers', function (Blueprint $table) {
            $columns = [
                'ssn',
                'device_ip',
                'sc_account_id',
                'sc_account_status',
                'sc_address_status',
                'sc_identity_photo',
                'sc_identity_photo_status',
                'routing_number',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('drivers', $column)) {
                    $table->dropColumn($column);
                }
            }
});
    }
}
