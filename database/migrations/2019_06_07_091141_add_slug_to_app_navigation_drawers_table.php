<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSlugToAppNavigationDrawersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_navigation_drawers', function (Blueprint $table) {
            $columns = [
                'slug' => function (Blueprint $table) {
                    // $table->text('slug')->after('status')->nullable();
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('app_navigation_drawers', $column)) {
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
        Schema::table('app_navigation_drawers', function (Blueprint $table) {
            $columns = [
                'slug',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('app_navigation_drawers', $column)) {
                    $table->dropColumn($column);
                }
            }
});
    }
}
