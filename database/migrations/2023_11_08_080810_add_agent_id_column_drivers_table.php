<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up()
    {
        MigrationSchema::addColumnWithForeign(
            'drivers',
            'agent_id',
            fn (Blueprint $table) => $table->unsignedInteger('agent_id')->after('taxi_company_id')->nullable(),
            'agents'
        );
    }

    public function down()
    {
        MigrationSchema::dropForeignIfExists('drivers', 'agent_id');
    }
};
