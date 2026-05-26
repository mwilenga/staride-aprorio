<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToCancelReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cancel_reasons', function (Blueprint $table) {
            $table->foreign('segment_id')->references('id')->on('segments')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $columns = [
                'segment_id' => function (Blueprint $table) {
                    $table->unsignedInteger('segment_id')->nullable();
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('cancel_reasons', $column)) {
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
        Schema::table('cancel_reasons', function (Blueprint $table) {
            //
        });
    }
}
