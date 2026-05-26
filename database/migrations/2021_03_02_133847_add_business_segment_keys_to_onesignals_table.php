<?php

use App\Support\MigrationSchema;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddBusinessSegmentKeysToOnesignalsTable extends Migration
{
    public function up()
    {
        MigrationSchema::addColumnIfMissing(
            'onesignals',
            'business_segment_application_key',
            fn (Blueprint $table) => $table->string('business_segment_application_key')->nullable()
        );

        MigrationSchema::recreateColumn(
            'onesignals',
            'business_segment_rest_key',
            fn (Blueprint $table) => $table->string('business_segment_rest_key')->nullable()
        );

        MigrationSchema::recreateColumn(
            'onesignals',
            'business_segment_channel_id',
            fn (Blueprint $table) => $table->string('business_segment_channel_id')->nullable()
        );
    }

    public function down()
    {
        if (!Schema::hasTable('onesignals')) {
            return;
        }

        foreach (['business_segment_application_key', 'business_segment_rest_key', 'business_segment_channel_id'] as $column) {
            if (Schema::hasColumn('onesignals', $column)) {
                Schema::table('onesignals', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
}
