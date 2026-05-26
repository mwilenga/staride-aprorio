<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToDriverDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_documents', function (Blueprint $table) {
            $columns = [
                'temp_document_file' => function (Blueprint $table) {
                    $table->string('temp_document_file')->nullable()->after('document_verification_status');
                },
                'temp_expire_date' => function (Blueprint $table) {
                    $table->date('temp_expire_date')->nullable()->after('temp_document_file');
                },
                'temp_doc_verification_status' => function (Blueprint $table) {
                    $table->integer('temp_doc_verification_status')->nullable()->after('temp_expire_date');
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
