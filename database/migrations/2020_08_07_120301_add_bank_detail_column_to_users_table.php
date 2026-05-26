<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddBankDetailColumnToUsersTable extends Migration
{
    public function up()
    {
        MigrationSchema::recreateColumn('users', 'bank_name', fn (Blueprint $table) => $table->string('bank_name', 191)->nullable());
        MigrationSchema::recreateColumn('users', 'account_holder_name', fn (Blueprint $table) => $table->string('account_holder_name', 191)->nullable());
        MigrationSchema::recreateColumn('users', 'account_number', fn (Blueprint $table) => $table->string('account_number', 191)->nullable());
        MigrationSchema::recreateColumn('users', 'online_code', fn (Blueprint $table) => $table->string('online_code')->nullable());

        MigrationSchema::addColumnWithForeign(
            'users',
            'account_type_id',
            fn (Blueprint $table) => $table->unsignedInteger('account_type_id')->nullable(),
            'account_types',
            'RESTRICT',
            'RESTRICT'
        );
    }

    public function down()
    {
        //
    }
}
