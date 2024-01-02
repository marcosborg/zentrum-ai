<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('log_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('role');
            $table->longText('message');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
