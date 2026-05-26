<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCdnColumnsToConfigurationsTable extends Migration
{
    /**
     * get_image() reads working_with_cdn and cloudflare_cdn_url from configurations.
     */
    public function up()
    {
        if (!Schema::hasTable('configurations')) {
            return;
        }

        Schema::table('configurations', function (Blueprint $table) {
            if (!Schema::hasColumn('configurations', 'working_with_cdn')) {
                $table->tinyInteger('working_with_cdn')
                    ->nullable()
                    ->default(2)
                    ->comment('1=use Cloudflare CDN, 2=S3 only');
            }

            if (!Schema::hasColumn('configurations', 'cloudflare_cdn_url')) {
                $table->string('cloudflare_cdn_url', 500)->nullable();
            }

            if (!Schema::hasColumn('configurations', 'driver_training')) {
                $table->tinyInteger('driver_training')
                    ->nullable()
                    ->default(2)
                    ->comment('1=driver training enabled, 2=disabled');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('configurations')) {
            return;
        }

        Schema::table('configurations', function (Blueprint $table) {
            foreach (['driver_training', 'cloudflare_cdn_url', 'working_with_cdn'] as $column) {
                if (Schema::hasColumn('configurations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
