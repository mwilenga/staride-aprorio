<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('vehicle_delivery_booking_packages')) {
            Schema::create('vehicle_delivery_booking_packages', function (Blueprint $table) {
                $table->id();
                $table->integer('booking_id')->unsigned();
                $table->foreign('booking_id')->references('id')->on('bookings')->onUpdate('RESTRICT')->onDelete('CASCADE');
                $table->integer('booking_delivery_detail_id')->unsigned();
                $table->foreign('booking_delivery_detail_id', 'vdbp_booking_del_detail_fk')
                    ->references('id')
                    ->on('booking_delivery_details')
                    ->onUpdate('RESTRICT')
                    ->onDelete('CASCADE');
                $table->string('quantity')->nullable();
                $table->timestamps();
            });

            return;
        }

        MigrationSchema::ensureForeign('vehicle_delivery_booking_packages', 'booking_id', 'bookings');
        MigrationSchema::ensureForeign(
            'vehicle_delivery_booking_packages',
            'booking_delivery_detail_id',
            'booking_delivery_details',
            'RESTRICT',
            'CASCADE',
            'vdbp_booking_del_detail_fk'
        );
    }

    public function down()
    {
        Schema::dropIfExists('vehicle_delivery_booking_packages');
    }
};
