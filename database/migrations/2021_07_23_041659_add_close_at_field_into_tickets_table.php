<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCloseAtFieldIntoTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->timestamp('close_at')->nullable();
        });

        $closedTickets = \App\Models\Ticket::whereStatus(\App\Models\Ticket::STATUS_CLOSED)->where('close_at', '=', null)->get();

        foreach ($closedTickets as $closedTicket) {
            $closedTicket->update(['close_at' => $closedTicket->updated_at]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('close_at');
        });
    }
}
