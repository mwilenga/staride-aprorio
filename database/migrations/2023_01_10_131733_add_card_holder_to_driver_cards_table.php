<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddCardHolderToDriverCardsTable extends Migration
{
    public function up()
    {
        MigrationSchema::addColumnIfMissing(
            'driver_cards',
            'card_holder',
            fn (Blueprint $table) => $table->string('card_holder')->nullable()->after('token')
        );
    }

    public function down()
    {
        //
    }
}
