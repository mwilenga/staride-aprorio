<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdvertisementBannersTableColumn extends Migration
{
    public function up()
    {
        MigrationSchema::recreateColumn(
            'advertisement_banners',
            'action_type',
            fn (Blueprint $table) => $table->text('action_type')->nullable()->after('image')->comment('URL, PRODUCT, CATEGORY')
        );

        MigrationSchema::addColumnWithForeign(
            'advertisement_banners',
            'product_id',
            fn (Blueprint $table) => $table->unsignedInteger('product_id')->nullable()->after('redirect_url'),
            'products'
        );

        MigrationSchema::addColumnWithForeign(
            'advertisement_banners',
            'category_id',
            fn (Blueprint $table) => $table->unsignedInteger('category_id')->nullable()->after('product_id'),
            'categories'
        );
    }

    public function down()
    {
        Schema::dropIfExists('user_favourite_product');
    }
}
