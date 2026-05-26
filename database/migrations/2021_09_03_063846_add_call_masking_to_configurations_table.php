<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCallMaskingToConfigurationsTable extends Migration
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
                'twilio_call_masking' => function (Blueprint $table) {
                    $table->tinyInteger('twilio_call_masking')->nullable()->default(2)->comment('1 - Enable, 2 - Disable');
                },
                'twilio_sid' => function (Blueprint $table) {
                    $table->string('twilio_sid')->nullable();
                },
                'twilio_service_id' => function (Blueprint $table) {
                    $table->string('twilio_service_id')->nullable();
                },
                'twilio_token' => function (Blueprint $table) {
                    $table->string('twilio_token')->nullable();
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
