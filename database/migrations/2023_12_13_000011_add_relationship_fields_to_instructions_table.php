<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToInstructionsTable extends Migration
{
    public function up()
    {
        Schema::table('instructions', function (Blueprint $table) {
            $table->unsignedBigInteger('assistant_id')->nullable();
            $table->foreign('assistant_id', 'assistant_fk_9301055')->references('id')->on('assistants');
        });
    }
}
