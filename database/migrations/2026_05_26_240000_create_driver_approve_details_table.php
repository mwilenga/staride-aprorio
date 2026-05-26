<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverApproveDetailsTable extends Migration
{
    /**
     * Pending driver profile changes awaiting merchant approval.
     */
    public function up()
    {
        if (Schema::hasTable('driver_approve_details')) {
            return;
        }

        Schema::create('driver_approve_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('driver_id')->index('dad_driver_id_idx');
            $table->unsignedInteger('merchant_id')->index('dad_merchant_id_idx');
            $table->longText('driver_details')->nullable();
            $table->tinyInteger('is_approve')->default(0)->comment('0=pending, 1=approved');
            $table->tinyInteger('is_reject')->default(0)->comment('0=no, 1=rejected');
            $table->text('reject_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('driver_approve_details');
    }
}
