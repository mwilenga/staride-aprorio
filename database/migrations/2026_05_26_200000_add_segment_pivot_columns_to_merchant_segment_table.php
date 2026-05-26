<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSegmentPivotColumnsToMerchantSegmentTable extends Migration
{
    /**
     * Merchant::Segment() withPivot expects these columns on merchant_segment.
     */
    public function up()
    {
        if (!Schema::hasTable('merchant_segment')) {
            return;
        }

        Schema::table('merchant_segment', function (Blueprint $table) {
            if (!Schema::hasColumn('merchant_segment', 'dynamic_url')) {
                $table->string('dynamic_url', 500)->nullable()->after('is_coming_soon');
            }

            if (!Schema::hasColumn('merchant_segment', 'segment_background_gradient_1')) {
                $table->string('segment_background_gradient_1', 50)->nullable()->after('dynamic_url');
            }

            if (!Schema::hasColumn('merchant_segment', 'segment_background_gradient_2')) {
                $table->string('segment_background_gradient_2', 50)->nullable()->after('segment_background_gradient_1');
            }

            if (!Schema::hasColumn('merchant_segment', 'segment_home_screen_image')) {
                $table->string('segment_home_screen_image', 255)->nullable()->after('segment_background_gradient_2');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('merchant_segment')) {
            return;
        }

        Schema::table('merchant_segment', function (Blueprint $table) {
            $columns = [
                'segment_home_screen_image',
                'segment_background_gradient_2',
                'segment_background_gradient_1',
                'dynamic_url',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('merchant_segment', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
