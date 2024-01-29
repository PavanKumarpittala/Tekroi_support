<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalHoursToTicketDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_details', function (Blueprint $table) {
            $table->string('total_hours', 50)->nullable()->after('total_time');
        $table->string('cumulative_total_hours', 50)->nullable()->after('total_time');
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_details', function (Blueprint $table) {
            //
        });
    }
}
