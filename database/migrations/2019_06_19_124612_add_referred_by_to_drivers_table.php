<?php

use App\Support\MigrationSchema;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReferredByToDriversTable extends Migration
{
    public function up()
    {
        MigrationSchema::addColumnWithForeign(
            'drivers',
            'referred_by',
            fn (Blueprint $table) => $table->unsignedInteger('referred_by')->nullable(),
            'drivers',
            'RESTRICT',
            'CASCADE'
        );
    }

    public function down()
    {
        if (!Schema::hasTable('drivers')) {
            return;
        }

        MigrationSchema::dropForeignIfExists('drivers', 'referred_by');
        if (Schema::hasColumn('drivers', 'referred_by')) {
            Schema::table('drivers', function (Blueprint $table) {
                $table->dropColumn('referred_by');
            });
        }
    }
}
