<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToDriverDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_documents', function (Blueprint $table) {
            $table->foreign('temp_reject_reason_id')->references('id')->on('reject_reasons')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $columns = [
                'temp_reject_reason_id' => function (Blueprint $table) {
                    $table->unsignedInteger('temp_reject_reason_id')->nullable()->after('temp_doc_verification_status');
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('driver_documents', $column)) {
                    $callback($table);
                }
            }
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_documents', function (Blueprint $table) {
            //
        });
    }
}
