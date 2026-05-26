<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEnableStoreUserChatToConfigurationsTable extends Migration
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
                'enable_store_user_chat' => function (Blueprint $table) {
                    // $table->tinyInteger('enable_store_user_chat')->nullable()->default(2);
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
