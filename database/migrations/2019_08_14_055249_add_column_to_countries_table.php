<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            $columns = [
                'transaction_code' => function (Blueprint $table) {
                    $table->string('transaction_code')->after('minNumPhone')->nullable();
                },
                'tip_short_amount' => function (Blueprint $table) {
                    $table->string('tip_short_amount')->after('country_status')->nullable();
                },
                'document_auto_verify' => function (Blueprint $table) {
                    $table->string('document_auto_verify')->after('tip_short_amount')->nullable();
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('countries', $column)) {
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
        Schema::table('countries', function (Blueprint $table) {
            //
        });
    }
}
