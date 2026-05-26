<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLoginBackgroundImageToBusinessSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_segments', function (Blueprint $table) {
            $columns = [
                'login_background_image' => function (Blueprint $table) {
                    // column is duplicate // $table->string('login_background_image')->nullable();
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('business_segments', $column)) {
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
        Schema::table('business_segments', function (Blueprint $table) {
            $columns = [
                'login_background_image',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('business_segments', $column)) {
                    $table->dropColumn($column);
                }
            }
});
    }
}
