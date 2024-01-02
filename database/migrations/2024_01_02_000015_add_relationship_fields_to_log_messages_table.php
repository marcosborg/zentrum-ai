<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToLogMessagesTable extends Migration
{
    public function up()
    {
        Schema::table('log_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('log_id')->nullable();
            $table->foreign('log_id', 'log_fk_9357354')->references('id')->on('logs');
        });
    }
}
