<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCheckedToCustomerSupportsTable extends Migration
{
    /**
     * Sidebar unread count uses customer_supports.is_checked (0 or null = unread).
     */
    public function up()
    {
        if (!Schema::hasTable('customer_supports')) {
            return;
        }

        if (Schema::hasColumn('customer_supports', 'is_checked')) {
            return;
        }

        Schema::table('customer_supports', function (Blueprint $table) {
            $table->tinyInteger('is_checked')
                ->nullable()
                ->default(0)
                ->comment('0=unread, 1=read')
                ->after('query');
        });
    }

    public function down()
    {
        if (!Schema::hasTable('customer_supports')) {
            return;
        }

        if (!Schema::hasColumn('customer_supports', 'is_checked')) {
            return;
        }

        Schema::table('customer_supports', function (Blueprint $table) {
            $table->dropColumn('is_checked');
        });
    }
}
