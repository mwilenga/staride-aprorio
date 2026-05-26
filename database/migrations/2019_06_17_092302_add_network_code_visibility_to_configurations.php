<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNetworkCodeVisibilityToConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('configurations') && !Schema::hasColumn('configurations', 'network_code_visibility')) {
            Schema::table('configurations', function (Blueprint $table) {
                $columns = [
                    'network_code_visibility' => function (Blueprint $table) {
                        $table->string('network_code_visibility')->nullable();
                    },
                ];

                foreach ($columns as $column => $callback) {
                    if (!Schema::hasColumn('configurations', $column)) {
                        $callback($table);
                    }
                }
});
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('configurations', 'network_code_visibility')) {
            Schema::table('configurations', function (Blueprint $table) {
                $columns = [
                    'network_code_visibility',
                ];

                foreach ($columns as $column) {
                    if (Schema::hasColumn('configurations', $column)) {
                        $table->dropColumn($column);
                    }
                }
});
        }
    }
}
