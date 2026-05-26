<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'designation_id' => function (Blueprint $table) {
                    $table->string('designation_id')->nullable();
                },
                'login_via' => function (Blueprint $table) {
                    $table->integer('login_via')->default(1)->comment('1: Personal Account, 2 :Corporate Account');
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
