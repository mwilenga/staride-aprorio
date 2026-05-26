<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddColumnProductsTable extends Migration
{
    public function up()
    {
        MigrationSchema::addColumnWithForeign(
            'products',
            'brand_id',
            fn (Blueprint $table) => $table->unsignedBigInteger('brand_id')->after('sku_id')->nullable(),
            'brands',
            'RESTRICT',
            'CASCADE'
        );
    }

    public function down()
    {
        //
    }
}
