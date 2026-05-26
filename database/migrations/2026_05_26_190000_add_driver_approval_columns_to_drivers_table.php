<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDriverApprovalColumnsToDriversTable extends Migration
{
    /**
     * Driver dashboard / training flows expect in_training and is_approved.
     * These columns exist in production Apporio DBs but were missing from migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('drivers')) {
            return;
        }

        Schema::table('drivers', function (Blueprint $table) {
            if (!Schema::hasColumn('drivers', 'in_training')) {
                $table->tinyInteger('in_training')
                    ->nullable()
                    ->default(2)
                    ->comment('1=in training, 2=not in training, 3=training rejected/resubmit')
                    ->after('reject_driver');
            }

            if (!Schema::hasColumn('drivers', 'is_approved')) {
                $table->tinyInteger('is_approved')
                    ->nullable()
                    ->default(2)
                    ->comment('1=approved, 2=pending')
                    ->after('in_training');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('drivers')) {
            return;
        }

        Schema::table('drivers', function (Blueprint $table) {
            if (Schema::hasColumn('drivers', 'is_approved')) {
                $table->dropColumn('is_approved');
            }

            if (Schema::hasColumn('drivers', 'in_training')) {
                $table->dropColumn('in_training');
            }
        });
    }
}
