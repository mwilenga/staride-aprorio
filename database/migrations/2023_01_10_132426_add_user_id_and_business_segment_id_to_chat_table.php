<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddUserIdAndBusinessSegmentIdToChatTable extends Migration
{
    public function up()
    {
        MigrationSchema::recreateColumn(
            'chats',
            'user_id',
            fn (Blueprint $table) => $table->unsignedInteger('user_id')->nullable()->after('id')
        );

        MigrationSchema::recreateColumn(
            'chats',
            'order_id',
            fn (Blueprint $table) => $table->unsignedInteger('order_id')->nullable()->after('booking_id')
        );

        MigrationSchema::recreateColumn(
            'chats',
            'handyman_order_id',
            fn (Blueprint $table) => $table->unsignedInteger('handyman_order_id')->nullable()->after('order_id')
        );

        MigrationSchema::recreateColumn(
            'chats',
            'business_segment_id',
            fn (Blueprint $table) => $table->unsignedInteger('business_segment_id')->nullable()->after('handyman_order_id')
        );
    }

    public function down()
    {
        //
    }
}
