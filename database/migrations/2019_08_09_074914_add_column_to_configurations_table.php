<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $columns = [
                'driver_address' => function (Blueprint $table) {
                    // $table->tinyInteger('driver_address')->nullable();
                },
                'reminder_doc_expire' => function (Blueprint $table) {
                    $table->integer('reminder_doc_expire')->nullable()->default(1)->comment('In Days');
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('configurations', $column)) {
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
        Schema::table('configurations', function (Blueprint $table) {
            //
        });
    }
}
