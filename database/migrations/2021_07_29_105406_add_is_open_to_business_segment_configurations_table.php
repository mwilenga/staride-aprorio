<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsOpenToBusinessSegmentConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_segment_configurations', function (Blueprint $table) {
            $columns = [
                'is_open' => function (Blueprint $table) {
                    // $table->tinyInteger('is_open')->default(1)->comment('1:Yes, 2:No');
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('business_segment_configurations', $column)) {
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
        Schema::table('business_segment_configurations', function (Blueprint $table) {
            $columns = [
                'is_open',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('business_segment_configurations', $column)) {
                    $table->dropColumn($column);
                }
            }
});
    }
}
