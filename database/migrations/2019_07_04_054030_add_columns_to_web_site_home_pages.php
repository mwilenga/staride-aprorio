<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToWebSiteHomePages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('web_site_home_pages', function (Blueprint $table) {
            $columns = [
                'user_estimate_image' => function (Blueprint $table) {
                    $table->string('user_estimate_image')->nullable();
                },
                'featured_component_main_image' => function (Blueprint $table) {
                    $table->string('featured_component_main_image')->nullable();
                },
                'user_login_bg_image' => function (Blueprint $table) {
                    $table->string('user_login_bg_image')->nullable();
                },
                'driver_login_bg_image' => function (Blueprint $table) {
                    $table->string('driver_login_bg_image')->nullable();
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('web_site_home_pages', $column)) {
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
        Schema::table('web_site_home_pages', function (Blueprint $table) {
            $columns = [
                'featured_component_main_image',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('web_site_home_pages', $column)) {
                    $table->dropColumn($column);
                }
            }
});
    }
}
