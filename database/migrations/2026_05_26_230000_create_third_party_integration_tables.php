<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThirdPartyIntegrationTables extends Migration
{
    /**
     * Third-party integration master list + per-merchant configuration.
     * Required by sidebar, MainScreenController, and IntegrationController.
     */
    public function up()
    {
        if (!Schema::hasTable('third_party_integrations')) {
            Schema::create('third_party_integrations', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 191)->index();
                $table->text('params')->nullable();
                $table->string('description', 191)->nullable();
                $table->tinyInteger('status')->default(1)->comment('1=active, 2=inactive');
                $table->tinyInteger('environment')->nullable()->comment('1=production, 2=test');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('third_party_integration_configurations')) {
            Schema::create('third_party_integration_configurations', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('merchant_id')->index();
                $table->unsignedInteger('third_party_integration_id')->index();
                $table->string('provider_slug', 100)->nullable()->index();
                $table->tinyInteger('display_home_screen')->default(2)->comment('1=show on home/sidebar, 2=hidden');
                $table->string('api_key', 500)->nullable();
                $table->string('api_secret', 500)->nullable();
                $table->text('auth_token')->nullable();
                $table->string('auth_password', 255)->nullable();
                $table->string('authorization', 500)->nullable();
                $table->tinyInteger('environment')->nullable();
                $table->string('operator', 191)->nullable();
                $table->text('additional_req')->nullable();
                $table->string('sender', 191)->nullable();
                $table->tinyInteger('status')->default(1)->nullable()->comment('1=active, 2=inactive');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('third_party_integration_configurations');
        Schema::dropIfExists('third_party_integrations');
    }
}
