<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('language_merchant_membership_plans')) {
            Schema::create('language_merchant_membership_plans', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('merchant_membership_plan_id');
                $table->foreign('merchant_membership_plan_id', 'lmmp_plan_id_fk')
                    ->references('id')
                    ->on('merchant_membership_plans')
                    ->onUpdate('RESTRICT')
                    ->onDelete('CASCADE');
                $table->string('plan_title')->nullable();
                $table->text('description')->nullable();
                $table->string('locale')->default('en');
                $table->string('plan_name')->nullable();
                $table->timestamps();
            });

            return;
        }

        MigrationSchema::recreateColumn(
            'language_merchant_membership_plans',
            'merchant_membership_plan_id',
            fn (Blueprint $table) => $table->unsignedBigInteger('merchant_membership_plan_id')
        );

        MigrationSchema::ensureForeign(
            'language_merchant_membership_plans',
            'merchant_membership_plan_id',
            'merchant_membership_plans',
            'RESTRICT',
            'CASCADE',
            'lmmp_plan_id_fk'
        );
    }

    public function down()
    {
        Schema::dropIfExists('language_merchant_membership_plans');
    }
};
