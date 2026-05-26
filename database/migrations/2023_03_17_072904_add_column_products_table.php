<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('CASCADE');

            $columns = [
                'brand_id' => function (Blueprint $table) {
                    $table->bigInteger('brand_id')->unsigned()->after('sku_id')->nullable();
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('products', $column)) {
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
        //
    }
}
