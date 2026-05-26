<?php

use App\Support\MigrationSchema;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddBusinessProfileImageToBusinessSegmentTable extends Migration
{
    public function up()
    {
        MigrationSchema::addColumnIfMissing(
            'business_segments',
            'business_profile_image',
            fn (Blueprint $table) => $table->string('business_profile_image')->nullable()
        );
    }

    public function down()
    {
        if (!Schema::hasTable('business_segments') || !Schema::hasColumn('business_segments', 'business_profile_image')) {
            return;
        }

        Schema::table('business_segments', function (Blueprint $table) {
            $table->dropColumn('business_profile_image');
        });
    }
}
