<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBankDetailColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('account_type_id')->references('id')->on('account_types')->onUpdate('RESTRICT');

            $columns = [
                'bank_name' => function (Blueprint $table) {
                    $table->string('bank_name', 191)->nullable();
                },
                'account_holder_name' => function (Blueprint $table) {
                    $table->string('account_holder_name', 191)->nullable();
                },
                'account_number' => function (Blueprint $table) {
                    $table->string('account_number', 191)->nullable();
                },
                'account_type_id' => function (Blueprint $table) {
                    $table->unsignedInteger('account_type_id')->nullable();
                },
                'online_code' => function (Blueprint $table) {
                    $table->string('online_code')->nullable();
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('users', $column)) {
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
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
