<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('business_segments') || !Schema::hasTable('merchant_membership_plans')) {
            return;
        }

        if (!Schema::hasColumn('business_segments', 'membership_plan_id')) {
            Schema::table('business_segments', function (Blueprint $table) {
                $table->unsignedBigInteger('membership_plan_id')->nullable();
            });
        }

        Schema::table('business_segments', function (Blueprint $table) {
            $table->foreign('membership_plan_id')
                ->references('id')
                ->on('merchant_membership_plans')
                ->onUpdate('RESTRICT')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('business_segments')) {
            return;
        }

        Schema::table('business_segments', function (Blueprint $table) {
            $table->dropForeign(['membership_plan_id']);
        });
    }
};
