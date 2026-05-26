<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('login_logs')) {
            return;
        }

        Schema::create('login_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('merchant_id')->nullable()->index();
            $table->unsignedInteger('business_segment_id')->nullable()->index();
            $table->string('user_name', 191)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->dateTime('login_time')->nullable()->index();
            $table->tinyInteger('is_business_segment_direct_login')->nullable()->default(2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('login_logs')) {
            return;
        }

        Schema::dropIfExists('login_logs');
    }
}

