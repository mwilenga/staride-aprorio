<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddColumnToDriverDocumentsTable extends Migration
{
    public function up()
    {
        MigrationSchema::addColumnWithForeign(
            'driver_documents',
            'temp_reject_reason_id',
            fn (Blueprint $table) => $table->unsignedInteger('temp_reject_reason_id')->nullable()->after('temp_doc_verification_status'),
            'reject_reasons'
        );
    }

    public function down()
    {
        //
    }
}
