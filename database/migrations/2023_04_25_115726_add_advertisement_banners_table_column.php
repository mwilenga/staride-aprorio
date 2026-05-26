<?php
/**
 * Created by PhpStorm.
 * User: apporio
 * Date: 25/4/23
 * Time: 6:26 PM
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdvertisementBannersTableColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('advertisement_banners', function (Blueprint $table) {
            $table->foreign("product_id")->on("products")->references("id")->onUpdate("RESTRICT")->onDelete("CASCADE");
            $table->foreign("category_id")->on("categories")->references("id")->onUpdate("RESTRICT")->onDelete("CASCADE");

            $columns = [
                'action_type' => function (Blueprint $table) {
                    $table->text("action_type")->nullable()->after("image")->comment("URL, PRODUCT, CATEGORY");
                },
                'product_id' => function (Blueprint $table) {
                    $table->unsignedInteger("product_id")->nullable()->after("redirect_url");
                },
                'category_id' => function (Blueprint $table) {
                    $table->unsignedInteger("category_id")->nullable()->after("product_id");
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('advertisement_banners', $column)) {
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
        Schema::dropIfExists('user_favourite_product');
    }
}
