<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBusinessSegmentKeysToOnesignalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('onesignals', function (Blueprint $table) {
            $columns = [
                'business_segment_application_key' => function (Blueprint $table) {
                    // $table->string('business_segment_application_key')->nullable();
                },
                'business_segment_rest_key' => function (Blueprint $table) {
                    $table->string('business_segment_rest_key')->nullable();
                },
                'business_segment_channel_id' => function (Blueprint $table) {
                    $table->string('business_segment_channel_id')->nullable();
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('onesignals', $column)) {
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
        Schema::table('onesignals', function (Blueprint $table) {
            $columns = [
                'business_segment_application_key',
                'business_segment_rest_key',
                'business_segment_channel_id',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('onesignals', $column)) {
                    $table->dropColumn($column);
                }
            }
});
    }
}
