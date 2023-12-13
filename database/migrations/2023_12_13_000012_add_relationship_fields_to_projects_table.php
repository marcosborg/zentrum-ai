<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToProjectsTable extends Migration
{
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('openai_id')->nullable();
            $table->foreign('openai_id', 'openai_fk_9301116')->references('id')->on('openais');
        });
    }
}
