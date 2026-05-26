<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToCorporatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('corporates', function (Blueprint $table) {
            $table->foreign('country_id')->references('id')->on('countries')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign('segment_id')->references('id')->on('segments')->onUpdate('restrict')->onDelete('cascade');

            $columns = [
                'country_id' => function (Blueprint $table) {
                    $table->unsignedInteger('country_id')->after('merchant_id');
                },
                'segment_id' => function (Blueprint $table) {
                    $table->unsignedInteger('segment_id')->after('country_id')->nullable();
                },
                'password' => function (Blueprint $table) {
                    $table->string('password')->after('email');
                },
                'corporate_logo' => function (Blueprint $table) {
                    $table->string('corporate_logo')->after('password');
                },
                'remember_token' => function (Blueprint $table) {
                    $table->string('remember_token')->nullable()->after('corporate_logo');
                },
                'alias_name' => function (Blueprint $table) {
                    $table->string('alias_name')->after('corporate_name');
                },
                'status' => function (Blueprint $table) {
                    $table->string('status')->default(1)->after('corporate_address');
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('corporates', $column)) {
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
        Schema::table('corporates', function (Blueprint $table) {
            //
        });
    }
}
