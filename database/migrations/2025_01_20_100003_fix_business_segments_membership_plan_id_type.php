<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Safety migration if 100001 was already recorded or failed with int/bigint mismatch.
 */
return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('business_segments') || !Schema::hasTable('merchant_membership_plans')) {
            return;
        }

        MigrationSchema::dropForeignKeysOnColumn('business_segments', 'membership_plan_id');

        if (!Schema::hasColumn('business_segments', 'membership_plan_id')) {
            Schema::table('business_segments', function (Blueprint $table) {
                $table->unsignedBigInteger('membership_plan_id')->nullable();
            });
        } else {
            MigrationSchema::modifyColumnToUnsignedBigInt('business_segments', 'membership_plan_id', true);
        }

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
