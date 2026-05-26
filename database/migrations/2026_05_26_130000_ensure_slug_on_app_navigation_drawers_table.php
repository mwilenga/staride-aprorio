<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnsureSlugOnAppNavigationDrawersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('app_navigation_drawers')) {
            return;
        }

        if (Schema::hasColumn('app_navigation_drawers', 'slug')) {
            return;
        }

        Schema::table('app_navigation_drawers', function (Blueprint $table) {
            $table->text('slug')->after('status')->nullable();
        });
    }

    public function down()
    {
        if (!Schema::hasTable('app_navigation_drawers')) {
            return;
        }

        if (!Schema::hasColumn('app_navigation_drawers', 'slug')) {
            return;
        }

        Schema::table('app_navigation_drawers', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
}
