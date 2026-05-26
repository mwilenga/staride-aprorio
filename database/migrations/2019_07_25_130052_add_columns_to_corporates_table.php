<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddColumnsToCorporatesTable extends Migration
{
    public function up()
    {
        MigrationSchema::addColumnWithForeign(
            'corporates',
            'country_id',
            fn (Blueprint $table) => $table->unsignedInteger('country_id')->after('merchant_id'),
            'countries',
            'RESTRICT',
            'CASCADE'
        );

        MigrationSchema::addColumnWithForeign(
            'corporates',
            'segment_id',
            fn (Blueprint $table) => $table->unsignedInteger('segment_id')->after('country_id')->nullable(),
            'segments',
            'RESTRICT',
            'CASCADE'
        );

        MigrationSchema::recreateColumn('corporates', 'password', fn (Blueprint $table) => $table->string('password')->after('email'));
        MigrationSchema::recreateColumn('corporates', 'corporate_logo', fn (Blueprint $table) => $table->string('corporate_logo')->after('password'));
        MigrationSchema::recreateColumn('corporates', 'remember_token', fn (Blueprint $table) => $table->string('remember_token')->nullable()->after('corporate_logo'));
        MigrationSchema::recreateColumn('corporates', 'alias_name', fn (Blueprint $table) => $table->string('alias_name')->after('corporate_name'));
        MigrationSchema::recreateColumn('corporates', 'status', fn (Blueprint $table) => $table->string('status')->default(1)->after('corporate_address'));
    }

    public function down()
    {
        //
    }
}
