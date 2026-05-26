<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReferralCheckColumnToConfigurationsTable extends Migration
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
                'referral_code_enable' => function (Blueprint $table) {
                    $table->tinyInteger('referral_code_enable')->default(2)->nullable();
                },
                'referral_code_mandatory_driver_signup' => function (Blueprint $table) {
                    $table->tinyInteger('referral_code_mandatory_driver_signup')->default(2)->nullable();
                },
                'referral_code_mandatory_user_signup' => function (Blueprint $table) {
                    $table->tinyInteger('referral_code_mandatory_user_signup')->default(2)->nullable();
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
