<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('business_segments') || !Schema::hasTable('merchant_membership_plans')) {
            return;
        }

        MigrationSchema::recreateColumn(
            'business_segments',
            'membership_plan_id',
            fn (Blueprint $table) => $table->unsignedBigInteger('membership_plan_id')->nullable()
        );

        MigrationSchema::ensureForeign(
            'business_segments',
            'membership_plan_id',
            'merchant_membership_plans',
            'RESTRICT',
            'CASCADE',
            'bs_membership_plan_id_fk'
        );
    }

    public function down()
    {
        MigrationSchema::dropForeignIfExists('business_segments', 'membership_plan_id', 'bs_membership_plan_id_fk');
    }
};
