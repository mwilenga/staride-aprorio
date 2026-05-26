<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('language_vehicle_delivery_packages')) {
            Schema::create('language_vehicle_delivery_packages', function (Blueprint $table) {
                $table->id();
                $table->integer('vehicle_delivery_package_id')->unsigned();
                $table->foreign('vehicle_delivery_package_id', 'lvdp_vdp_id_fk')
                    ->references('id')
                    ->on('vehicle_delivery_packages')
                    ->onUpdate('RESTRICT')
                    ->onDelete('CASCADE');
                $table->string('package_name')->nullable();
                $table->string('locale', 10)->index();
                $table->timestamps();
            });

            return;
        }

        MigrationSchema::ensureForeign(
            'language_vehicle_delivery_packages',
            'vehicle_delivery_package_id',
            'vehicle_delivery_packages',
            'RESTRICT',
            'CASCADE',
            'lvdp_vdp_id_fk'
        );
    }

    public function down()
    {
        Schema::dropIfExists('language_vehicle_delivery_packages');
    }
};
